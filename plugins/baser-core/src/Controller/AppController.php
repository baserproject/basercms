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

namespace BaserCore\Controller;

use App\Controller\AppController as BaseController;
use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use BaserCore\Service\AppServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component\PaginatorComponent;
use Cake\Controller\Component\SecurityComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;

/**
 * Class AppController
 * @property BcMessageComponent $BcMessage
 * @property SecurityComponent $Security
 * @property PaginatorComponent $Paginator
 * @property AuthenticationComponent $Authentication
 */
class AppController extends BaseController
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * BcAppController constructor.
     * @param ServerRequest|null $request
     * @param Response|null $response
     * @param string|null $name
     * @param EventManagerInterface|null $eventManager
     * @param ComponentRegistry|null $components
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct(
        ?ServerRequest         $request = null,
        ?Response              $response = null,
        ?string                $name = null,
        ?EventManagerInterface $eventManager = null,
        ?ComponentRegistry     $components = null
    )
    {
        parent::__construct($request, $response, $name, $eventManager, $components);

        $request->getSession()->start();

        // インストールされていない場合、トップページにリダイレクトする
        // コンソールの場合は無視する
        if (!(BcUtil::isInstalled() || BcUtil::isConsole())) {
            if (!($request? $request->is('install') : false)) {
                // app_local.php が存在しない場合は、CakePHPの Internal Server のエラー画面が出て、
                // 原因がわからなくなるので強制的にコピーする
                if($this->getName() === 'BcError' && !file_exists(CONFIG . 'app_local.php')) {
                    copy(CONFIG . 'app_local.example.php', CONFIG . 'app_local.php');
                    // app_local.php が存在しない場合、.env もない可能性があるので確認
                    if(!file_exists(CONFIG . '.env')){
                        copy(CONFIG . '.env.example', CONFIG . '.env');
                    }
                }
                return $this->redirect('/');
            } else {
                // インストーラーの最初のステップでログイン状態を解除
                if ($request->getParam('action') === 'index') {
                    $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
                    if ($request->getSession()->check($sessionKey)) {
                        $request->getSession()->delete($sessionKey);
                        // 2022/11/16 by ryuring
                        // $this->redirect() を利用した場合、なぜか、リダイレクト後にセッションが復活してしまう。
                        // header() に切り替えるとうまくいった。
                        header('Location: ' . $request->getAttribute('base') . '/');
                        exit();
                    }
                }
            }
        }

    }

    /**
     * Initialize
     * @checked
     * @unitTest
     * @note(value="BcEmailを実装したあとに確認")
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcMessage');
        $this->loadComponent('Paginator');
        $this->loadComponent('Security', [
            'blackHoleCallback' => '_blackHoleCallback',
            'validatePost' => true,
            'requireSecure' => false,
            'unlockedFields' => ['x', 'y', 'MAX_FILE_SIZE']
        ]);

        // TODO ucmitz 未移行のためコメントアウト
        // >>>
//        $this->loadComponent('BaserCore.BcEmail');
        // <<<
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if (!$this->getRequest()->is('requestview')) return;

        $response = $this->redirectIfIsRequireMaintenance();
        if ($response) return $response;

        $this->__convertEncodingHttpInput();
        $this->__cleanupQueryParams();

        // インストーラー、アップデーターの場合はテーマを設定して終了
        // コンソールから利用される場合、$isInstall だけでは判定できないので、BC_INSTALLED も判定に入れる
        if ((!BcUtil::isInstalled() || $this->getRequest()->is('install') || $this->getRequest()->is('update')) && !in_array($this->getName(), ['Error', 'BcError'])) {
            $this->viewBuilder()->setTheme(Configure::read('BcApp.defaultAdminTheme'));
            return;
        }

        if ($this->request->is('ajax') || BcUtil::loginUser()) {
            $this->setResponse($this->getResponse()->withDisabledCache());
        }
    }

    /**
     * Before render
     * @param EventInterface $event
     * @return Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        $this->set($this->getService(AppServiceInterface::class)->getViewVarsForAll());
    }

    /**
     * フロント用のViewクラスをセットアップする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupFrontView(): void
    {
        $this->viewBuilder()->setClassName('BaserCore.BcFrontApp');
        $this->viewBuilder()->setTheme(BcUtil::getCurrentTheme());
    }

    /**
     * Securityコンポーネントのブラックホールからのコールバック
     *
     * フォーム改ざん対策・CSRF対策・SSL制限・HTTPメソッド制限などへの違反が原因で
     * Securityコンポーネントに"ブラックホールされた"場合の動作を指定する
     *
     * @param string $err エラーの種類
     * @return void
     * @throws BadRequestException
     * @uses _blackHoleCallback
     * @checked
     * @noTodo
     * @unitTest
     */
    public function _blackHoleCallback($err, $exception)
    {
        $message = __d('baser', '不正なリクエストと判断されました。') . "\n" . $exception->getMessage();
        throw new BadRequestException($message);
    }

    /**
     * http経由で送信されたデータを変換する
     * とりあえず、UTF-8で固定
     *
     * @return    void
     * @checked
     * @noTodo
     * @unitTest
     */
    private function __convertEncodingHttpInput(): void
    {
        if ($this->getRequest()->getData()) {
            $this->setRequest($this->request->withParsedBody($this->_autoConvertEncodingByArray($this->getRequest()->getData(), 'UTF-8')));
        }
    }

    /**
     * 配列の文字コードを変換する
     *
     * @param array $data 変換前データ
     * @param string $outenc 変換後の文字コード
     * @return array 変換後データ
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _autoConvertEncodingByArray($data, $outenc = 'UTF-8'): array
    {
        if (!$data) return [];
        foreach($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->_autoConvertEncodingByArray($value, $outenc);
                continue;
            }
            $inenc = mb_detect_encoding((string)$value);
            if ($value && $inenc !== $outenc) {
                // 半角カナは一旦全角に変換する
                $value = mb_convert_kana($value, 'KV', $inenc);
                $value = mb_convert_encoding($value, $outenc, $inenc);
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * クエリーパラメーターの調整
     * 環境によって？キーにamp;が付加されてしまうため
     * @checked
     * @noTodo
     * @unitTest
     */
    private function __cleanupQueryParams(): void
    {
        $query = $this->request->getQueryParams();
        if (is_array($query)) {
            foreach($query as $key => $val) {
                if (strpos($key, 'amp;') === 0) {
                    $query[substr($key, 4)] = $val;
                    unset($query[$key]);
                }
            }
        }
        $this->request = $this->request->withQueryParams($query);
    }

    /**
     * Set Title
     * @param string $title
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setTitle($title): void
    {
        $this->set('title', $title);
    }

    /**
     * メンテナンス画面へのリダイレクトが必要な場合にリダイレクトする
     * @return Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function redirectIfIsRequireMaintenance()
    {
        if (!BcUtil::isInstalled()) return;
        if ($this->request->is('ajax')) return;
        if (empty(BcSiteConfig::get('maintenance'))) return;
        if (Configure::read('debug')) return;
        if ($this->getRequest()->is('maintenance')) return;
        if ($this->getRequest()->is('admin')) return;
        if (BcUtil::isAdminUser()) return;

        // TODO ucmitz 削除検討要
        // CakePHP4 から requestAction がなくなっているので不要の可能性が高い
        // cell 機能で呼び出された場合のスルーの処理を書いたら削除する
        if (!empty($this->getRequest()->getParam('return')) && !empty($this->getRequest()->getParam('requested'))) {
            return $this->getResponse();
        }

        $redirectUrl = '/maintenance';
        if ($this->getRequest()->getAttribute('currentSite')->alias) {
            $redirectUrl = '/' . $this->getRequest()->getAttribute('currentSite')->alias . $redirectUrl;
        }
        return $this->redirect($redirectUrl);
    }

    /**
     * 画面の情報をセットする
     *
     * POSTデータとクエリパラメーターをセッションに保存した上で、
     * 指定されたデフォルト値も含めて ServerRequest に設定する。
     *
     * ```
     * $this->setViewConditions(['Content'], [
     *     'group' => 'index',
     *     'default' => [
     *          'query' => ['limit' => 10],
     *          'data' => ['title' => 'default']
     *      ],
     *     'get' => true
     * ]);
     * ```
     *
     * @param array $targetModel ターゲットとなるモデル
     * @param array $options オプション
     *  - `default`: 読み出す初期値（初期値：[]）
     *  - `group`: 保存するグループ名（初期値：''）
     *  - `post`: POSTデータを保存するかどうか（初期値：true）
     *  - `get`: GETデータを保存するかどうか（初期値：false）
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function setViewConditions($targetModel = [], $options = []): void
    {
        $this->saveViewConditions($targetModel, $options);
        $this->loadViewConditions($targetModel, $options);
    }

    /**
     * 画面の情報をセッションに保存する
     *
     * 次のセッション名に保存。
     * - POSTデータ: BcApp.viewConditions.{$contentsName}.data.{$model}
     * - クエリパラメーター: BcApp.viewConditions.{$contentsName}.query
     *
     * $contentsNameは次の形式となる。
     * {$controllerName}{$actionName}.{$group}
     *
     * ただし、ページネーションにおいて、1ページ目はクエリパラメーター`page` を付けない仕様となっているため
     * `page` は保存しない。
     *
     * @param array $targetModel
     * @param array $options オプション
     *  - `group`: 保存するグループ名（初期値：''）
     *  - `post`: POSTデータを保存するかどうか（初期値：true）
     *  - `get`: GETデータを保存するかどうか（初期値：false）
     * @see setViewConditions
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function saveViewConditions($targetModel = [], $options = []): void
    {
        $options = array_merge([
            'group' => '',
            'post' => true,
            'get' => true,
        ], $options);

        $request = $this->getRequest();
        $contentsName = $request->getParam('controller') . Inflector::classify($request->getParam('action'));
        if ($options['group']) $contentsName .= "." . $options['group'];
        if (!is_array($targetModel)) $targetModel = [$targetModel];
        $session = $request->getSession();

        if ($options['post'] && $targetModel) {
            foreach($targetModel as $model) {
                if (count($targetModel) > 1) {
                    $data = $request->getData($model);
                } else {
                    $data = $request->getData();
                }
                if ($data) $session->write("BcApp.viewConditions.{$contentsName}.data.{$model}", $data);
            }
        }

        if ($options['get'] && $request->getQueryParams()) {
            if ($session->check("BcApp.viewConditions.{$contentsName}.query")) {
                $query = array_merge(
                    $session->read("BcApp.viewConditions.{$contentsName}.query"),
                    $request->getQueryParams()
                );
            } else {
                $query = $request->getQueryParams();
            }
            unset($query['page']);
            $session->write("BcApp.viewConditions.{$contentsName}.query", $query);
        }
    }

    /**
     * 画面の情報をセッションから読み込む
     *
     * 初期値が設定されている場合は初期値を設定した上で、セッションで上書きし、
     * ServerRequestに設定する。
     *
     * @param array $targetModel
     * @param array|string $options オプション
     *  - `default`: 読み出す初期値（初期値：[]）
     *  - `group`: 保存するグループ名（初期値：''）
     *  - `post`: POSTデータを保存するかどうか（初期値：true）
     *  - `get`: GETデータを保存するかどうか（初期値：false）
     * @see setViewConditions, saveViewConditions
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function loadViewConditions($targetModel = [], $options = []): void
    {
        $options = array_merge([
            'default' => [],
            'group' => '',
            'post' => true,
            'get' => true,
        ], $options);

        $request = $this->getRequest();
        $contentsName = $request->getParam('controller') . Inflector::classify($request->getParam('action'));
        if ($options['group']) $contentsName .= "." . $options['group'];
        if (!is_array($targetModel)) $targetModel = [$targetModel];
        $session = $request->getSession();

        if ($targetModel) {
            foreach($targetModel as $model) {
                $data = [];
                if (!empty($options['default'][$model])) $data = $options['default'][$model];
                if ($options['post'] && $session->check("BcApp.viewConditions.{$contentsName}.data.{$model}")) {
                    $data = array_merge($data, $session->read("BcApp.viewConditions.{$contentsName}.data.{$model}"));
                }
                if ($data) {
                    if (count($targetModel) > 1) {
                        $this->setRequest($request->withData($model, array_merge($data, $request->getData($model))));
                    } else {
                        $this->setRequest($request->withParsedBody(array_merge($data, $request->getData())));
                    }
                }
            }
        }

        $query = [];
        if (!empty($options['default']['query'])) $query = $options['default']['query'];
        if ($options['get'] && $session->check("BcApp.viewConditions.{$contentsName}.query")) {
            $query = array_merge($query, $session->read("BcApp.viewConditions.{$contentsName}.query"));
            unset($query['url']);
            unset($query['ext']);
            unset($query['x']);
            unset($query['y']);
        }
        if ($query) {
            $request = $this->getRequest();
            $this->setRequest($request->withQueryParams(array_merge($query, $request->getQueryParams())));
        }
    }

    /**
     * NOT FOUNDページを出力する
     *
     * @return void
     * @throws NotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function notFound()
    {
        throw new NotFoundException(__d('baser', '見つかりませんでした。'));
    }

}
