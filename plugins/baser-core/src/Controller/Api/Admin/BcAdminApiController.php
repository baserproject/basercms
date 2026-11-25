<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Controller\Api\Admin;

use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Authenticator\SessionAuthenticator;
use BaserCore\Controller\Api\BcApiController;
use BaserCore\Error\BcBadRequestException;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\HttpException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcAdminApiController
 */
class BcAdminApiController extends BcApiController
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->FormProtection->setConfig('validate', false);
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @throws \Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        if (in_array($this->getRequest()->getParam('action'), $this->Authentication->getUnauthenticatedActions())) {
            // 管理画面 API が無効な場合は、認証不要のアクション（ログイン・JWKS 等）も公開しない。
            // 公開すると USE_CORE_ADMIN_API=false でも API ログインが到達可能になり、
            // 試行回数制限の無い認証オラクルとして利用できてしまう
            if (!filter_var(env('USE_CORE_ADMIN_API', false), FILTER_VALIDATE_BOOLEAN)) {
                throw new ForbiddenException(__d('baser_core', 'baser Admin APIは許可されていません。'));
            }
            return;
        }

        // 管理画面APIが許可されていない場合は弾く
        // ただし、Webブラウザの管理画面 SPA（セッション認証）からのAJAXは通す。
        // GHSA-wgvx-x5g3-9v29: なりすまし可能な Referer 一致判定（CWE-290）に代えて、
        // 認証 provider が SessionAuthenticator かどうかで「ブラウザ管理画面か外部クライアントか」を判定する。
        // （JWT 認証や未認証は SessionAuthenticator にならないため 403 となる）
        if (!filter_var(env('USE_CORE_ADMIN_API', false), FILTER_VALIDATE_BOOLEAN)) {
            $provider = $this->Authentication->getAuthenticationService()->getAuthenticationProvider();
            if (!($provider instanceof SessionAuthenticator)) {
                throw new ForbiddenException(__d('baser_core', 'baser Admin APIは許可されていません。'));
            }
        }

        // ユーザーの有効チェック
        if (!$this->isAvailableUser()) {
            $event->setResult($this->response->withStatus(401));
            return;
        }

        // トークンタイプチェック
        $auth = $this->Authentication->getAuthenticationService()->getAuthenticationProvider();
        if ($auth instanceof JwtAuthenticator) {
            $payload = $auth->getPayload();
            if ($payload->token_type !== 'access_token' && $this->getRequest()->getParam('action') !== 'refresh_token') {
                $event->setResult($this->response->withStatus(401));
                return;
            }
        }

        // 親の beforeFilter で認可チェックが入るので一番最後とする
        parent::beforeFilter($event);
    }

    /**
     * 認証が必要なAPIを利用可能かどうか判定
     *
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function isAdminApiEnabled()
    {
        if (!filter_var(env('USE_CORE_ADMIN_API', false), FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }
        return (bool) $this->Authentication->getIdentity();
    }

    /**
     * ログインユーザーが有効か判定する
     *
     * サービスクラス、または、テーブルクラスにおいて、isAvailable / get メソッドが
     * 存在するかを確認し、あれば実行し、その結果を返す。
     * ない場合は、 true を返却する
     *
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function isAvailableUser(): bool
    {
        // ユーザーの有効チェック
        $user = $this->Authentication->getResult()->getData();
        if(!$user) return false;

        $prefix = $this->getRequest()->getParam('prefix');
        $userModel = Configure::read("BcPrefixAuth.{$prefix}.userModel");

        // サービスクラスチェック
        [$plugin, $model] = pluginSplit($userModel);
        if(!$plugin) throw new BcException(__d('baser_core', 'BcPrefixAuth の userModel の設定ではプラグイン記法を利用してください。'));

        $serviceName = "{$plugin}\\Service\\{$model}ServiceInterface";
        if (interface_exists($serviceName)) {
            $service = $this->getService($serviceName);
            if (method_exists($service, 'isAvailable')) {
                return $service->isAvailable($user->id);
            }
            if (method_exists($service, 'get')) {
                return (bool)$service->get($user->id);
            }
        }

        // テーブルクラスチェック
        $tableName = "{$plugin}\\Model\\Table\\{$model}Table";
        if(class_exists($tableName)) {
            $table = TableRegistry::getTableLocator()->get($userModel);
            if(method_exists($table, 'isAvailable')) {
                return $table->isAvailable($user->id);
            }
            if(method_exists($table, 'get')) {
                return (bool) $table->get($user->id);
            }
        }
        throw new BcException(__d('baser_core', 'BcPrefixAuth の userModel にユーザーの存在確認メソッドが存在しません。'));
    }

    /**
     * 一括処理の送信値を検証する
     *
     * 処理名（batch）が許可されたものか、対象（batch_targets）が配列で、
     * ID を対象とする場合は数字のみで構成されているかを確認する。
     * 不正な場合は 400 とメッセージを応答に設定して false を返す
     *
     * @param array $allowMethod 許可する処理名をキーとした配列
     * @param bool $integerTargets 対象が ID（数字）の場合は true
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function validateBatchRequest(array $allowMethod, bool $integerTargets = true): bool
    {
        $method = $this->getRequest()->getData('batch');
        $targets = $this->getRequest()->getData('batch_targets');
        $message = null;
        if (!is_string($method) || !isset($allowMethod[$method])) {
            $message = __d('baser_core', '一括処理の処理内容が不正です。');
        } elseif (!is_array($targets) || !$targets) {
            $message = __d('baser_core', '一括処理の対象を指定してください。');
        } else {
            foreach($targets as $target) {
                if (!is_scalar($target) || (string)$target === '' || ($integerTargets && !ctype_digit((string)$target))) {
                    $message = __d('baser_core', '一括処理の対象に不正な値が含まれています。');
                    break;
                }
            }
        }
        if ($message === null) return true;
        $this->setResponse($this->response->withStatus(400));
        $this->set('message', $message);
        $this->viewBuilder()->setOption('serialize', ['message']);
        return false;
    }

    /**
     * 処理中に受け止めた例外から、応答のステータスコードを設定し、メッセージを返す
     *
     * - 存在しないデータ（RecordNotFoundException / InvalidPrimaryKeyException）は 404
     * - 入力の誤り（BcBadRequestException / PersistenceFailedException）は 400
     * - HttpException は、その例外のステータスコード
     * - BcException は 500 とし、baserCMS が書いたメッセージをそのまま返す
     * - それ以外（PHP・CakePHP・DB が出す例外）は 500 とし、ログに記録する。
     *   例外メッセージにはサーバーのパスや SQL 等の内部情報が含まれるため、
     *   デバッグモードでない場合は応答に含めない
     *
     * @param \Throwable $e
     * @param string|null $prefix 500 の場合にメッセージの先頭に付ける文言
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function handleThrowable(\Throwable $e, ?string $prefix = null): string
    {
        if ($e instanceof RecordNotFoundException || $e instanceof InvalidPrimaryKeyException) {
            $this->setResponse($this->response->withStatus(404));
            return __d('baser_core', 'データが見つかりません。');
        }
        if ($e instanceof BcBadRequestException) {
            $this->setResponse($this->response->withStatus(400));
            return $e->getMessage();
        }
        if ($e instanceof PersistenceFailedException) {
            $this->setResponse($this->response->withStatus(400));
            return __d('baser_core', '入力エラーです。内容を修正してください。');
        }
        if ($e instanceof HttpException) {
            $this->setResponse($this->response->withStatus($e->getCode()));
            return $e->getMessage();
        }

        $this->setResponse($this->response->withStatus(500));
        if ($prefix === null) $prefix = __d('baser_core', 'データベース処理中にエラーが発生しました。');
        if ($e instanceof BcException) {
            return $prefix . $e->getMessage();
        }
        $this->log(sprintf(
            "[%s] %s in %s on line %d\n%s",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
        if (Configure::read('debug')) {
            return $prefix . $e->getMessage();
        }
        return $prefix;
    }

    /**
     * 送信値から数字の項目を取得する
     *
     * 数字以外の値や配列が送られた場合、必須で未指定の場合は BcBadRequestException とする。
     * 数字以外の値のまま検索条件やサービスの int 型の引数に渡すと、型変換の例外や TypeError となるため
     *
     * @param string $name 項目名（ドット区切りで階層を指定できる）
     * @param bool $required 必須かどうか
     * @param bool $allowNegative 負の数を許可するかどうか（並び替えの移動量など）
     * @return int|null
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function getIntegerData(string $name, bool $required = true, bool $allowNegative = false): ?int
    {
        $value = $this->getRequest()->getData($name);
        if ($value === null || $value === '') {
            if ($required) {
                throw new BcBadRequestException(__d('baser_core', '{0} を指定してください。', $name));
            }
            return null;
        }
        $pattern = $allowNegative? '/\A-?[0-9]+\z/' : '/\A[0-9]+\z/';
        if (!is_scalar($value) || !preg_match($pattern, (string)$value)) {
            throw new BcBadRequestException(__d('baser_core', '{0} に不正な値が指定されています。', $name));
        }
        return (int)$value;
    }

    /**
     * 送信値の項目が文字列（スカラー値）かどうかを確かめる
     *
     * 配列などが送られた場合は BcBadRequestException とする。
     * 文字列として加工する処理に配列を渡すと TypeError となるため
     *
     * @param string $name 項目名（ドット区切りで階層を指定できる）
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function assertScalarData(string $name): void
    {
        $value = $this->getRequest()->getData($name);
        if ($value !== null && !is_scalar($value)) {
            throw new BcBadRequestException(__d('baser_core', '{0} に不正な値が指定されています。', $name));
        }
    }

}
