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
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AppController
 * @property BcMessageComponent $BcMessage
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
     * AppController constructor.
     * @param ServerRequest|null $request
     * @param Response|null $response
     * @param string|null $name
     * @param EventManagerInterface|null $eventManager
     * @param ComponentRegistry|null $components
     * @return void|ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct(
        ?ServerRequest $request = null,
        ?Response $response = null,
        ?string $name = null,
        ?EventManagerInterface $eventManager = null,
        ?ComponentRegistry $components = null
    )
    {
        parent::__construct($request, $response, $name, $eventManager, $components);

        // CSRFトークンの場合は高速化のためここで処理を終了
        if(!BcUtil::isConsole() && !$request->is('requestview')) return;

        $request->getSession()->start();

        // インストールされていない場合、トップページにリダイレクトする
        // コンソールの場合は無視する
        if (!(BcUtil::isInstalled() || BcUtil::isConsole())) {
            if (!($request? $request->is('install') : false)) {
                // app_local.php が存在しない場合は、CakePHPの Internal Server のエラー画面が出て、
                // 原因がわからなくなるので強制的にコピーする
                if ($this->getName() === 'BcError' && !file_exists(CONFIG . 'app_local.php')) {
                    copy(CONFIG . 'app_local.example.php', CONFIG . 'app_local.php');
                    // app_local.php が存在しない場合、.env もない可能性があるので確認
                    if (!file_exists(CONFIG . '.env')) {
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
        $this->loadComponent('FormProtection', [
            'unlockedFields' => ['x', 'y', 'MAX_FILE_SIZE'],
            'validationFailureCallback' => function (BadRequestException $exception) {
                $message = __d('baser_core', "不正なリクエストと判断されました\nもしくは、システムが受信できるデータ上限より大きなデータが送信された可能性があります。") . "\n" . $exception->getMessage();
                throw new BadRequestException($message);
            }
        ]);
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
        $response = parent::beforeFilter($event);
        if ($response) return $response;

        // index.php をつけたURLの場合、base の値が正常でなくなり、
        // 内部リンクが影響を受けておかしくなってしまうため強制的に Not Found とする
        if (preg_match('/\/index\.php\//', $this->getRequest()->getAttribute('base'))) {
            $this->notFound();
        }

        if (!$this->getRequest()->is('requestview')) return;

        $response = $this->redirectIfIsRequireMaintenance();
        if ($response) return $response;

        $this->__cleanupQueryParams();

        // インストーラー、アップデーターの場合はテーマを設定して終了
        // コンソールから利用される場合、$isInstall だけでは判定できないので、BC_INSTALLED も判定に入れる
        if ((!BcUtil::isInstalled() || $this->getRequest()->is('install')) && !in_array($this->getName(), ['Error', 'BcError'])) {
            $this->viewBuilder()->setTheme(Configure::read('BcApp.coreAdminTheme'));
            return;
        }

        if ($this->requirePermission($this->getRequest()) && !$this->checkPermission()) {
            $prefix = BcUtil::getRequestPrefix($this->getRequest());
            if ($prefix === 'Api/Admin') {
                throw new ForbiddenException(__d('baser_core', '指定されたAPIエンドポイントへのアクセスは許可されていません。必要な場合、システム管理者に「{0} {1}」へのアクセス許可を依頼してください。',
                    [$this->getRequest()->getMethod(), $this->getRequest()->getPath()]));
            } else {
                if (BcUtil::loginUser()) {
                    if ($this->getRequest()->getMethod() === 'GET') {
                        $this->BcMessage->setError(__d('baser_core', '指定されたページへのアクセスは許可されていません。必要な場合、システム管理者に「{0} {1}」へのアクセス許可を依頼してください。',
                            [$this->getRequest()->getMethod(), $this->getRequest()->getPath()]));
                    } else {
                        $this->BcMessage->setError(__d('baser_core', '実行した操作は許可されていません。必要な場合、システム管理者に「{0} {1}」へのアクセス許可を依頼してください。',
                            [$this->getRequest()->getMethod(), $this->getRequest()->getPath()]));
                    }
                    $url = Configure::read("BcPrefixAuth.{$prefix}.loginRedirect");
                } else {
                    $url = Router::url(Configure::read("BcPrefixAuth.{$prefix}.loginAction"))
                        . '?redirect=' . urlencode(Router::url());
                }
                return $this->redirect($url);
            }
        }

        if ($this->request->is('ajax') || BcUtil::loginUser()) {
            $this->setResponse($this->getResponse()->withDisabledCache());
        }
    }

    /**
     * パーミッションが必要かどうかを確認する
     *
     * デフォルトは true であるが、設定ファイルで明示的に false に
     * 設定されている場合は false となる。
     *
     * @param ServerRequest $request
     * @return bool
     */
    public function requirePermission(ServerRequest $request): bool
    {
        $prefix = BcUtil::getRequestPrefix($request);
        $requirePermission = Configure::read("BcPrefixAuth.{$prefix}.requirePermission");
        if($requirePermission !== false) {
            $requirePermission = true;
        }
        return $requirePermission;
    }

    /**
     * アクセスルールの権限を確認する
     *
     * 現在アクセスしているURLについて権限があるかどうかを確認する。
     *
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    private function checkPermission()
    {
        $user = BcUtil::loginUser();
        if ($user && $user->user_groups) {
            $userGroupsIds = Hash::extract($user->toArray()['user_groups'], '{n}.id');
        } else {
            $userGroupsIds = [];
        }
        /* @var PermissionsServiceInterface $permission */
        $permission = $this->getService(PermissionsServiceInterface::class);
        $request = $this->getRequest();
        return $permission->check($request->getPath(), $userGroupsIds, $request->getMethod());
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
        $message = __d('baser_core', '不正なリクエストと判断されました。もしくは、システムが受信できるデータ上限より大きなデータが送信された可能性があります') . "\n" . $exception->getMessage();
        throw new BadRequestException($message);
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
        throw new NotFoundException(__d('baser_core', '見つかりませんでした。'));
    }

    /**
     * データベースログを記録する
     *
     * @param string $message
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function saveDblog($message)
    {
        $dblogsService = $this->getService(DblogsServiceInterface::class);
        return $dblogsService->create(['message' => $message]);
    }

    /**
     * Ajax用のエラーを出力する
     *
     * @param int $errorNo エラーのステータスコード
     * @param mixed $message エラーメッセージ
     * @return void
     * @deprecated since 5.0.5 このメソッドは非推奨です。
     * @checked
     * @noTodo
     * @unitTest
     */
    public function ajaxError(int $errorNo = 500, $message = '')
    {
        $this->response = $this->getResponse()->withStatus($errorNo);
        if (!$message) return;
        if (!is_array($message)) $message = [$message];
        $aryMessage = [];
        foreach($message as $value) {
            if (is_array($value)) {
                $aryMessage[] = implode('<br />', $value);
            } else {
                $aryMessage[] = $value;
            }
        }
        echo implode('<br>', $aryMessage);
    }

}
