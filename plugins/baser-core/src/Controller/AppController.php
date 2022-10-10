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
use Cake\Http\Response;
use Cake\Http\ServerRequest;

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

        $isInstall = $request ? $request->is('install') : false;

        // インストールされていない場合、トップページにリダイレクトする
        // コンソールベースのインストールの際のページテンプレート生成において、
        // BC_INSTALLED、$isInstall ともに true でない為、コンソールの場合は無視する
        if (!(BC_INSTALLED || BcUtil::isConsole()) && !$isInstall) {
            $this->redirect('/');
        }
        $request->getSession()->start();
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
        if ((!BC_INSTALLED || $this->getRequest()->is('install') || $this->getRequest()->is('update')) && !in_array($this->getName(), ['Error', 'BcError'])) {
            $this->viewBuilder()->setTheme(Configure::read('BcApp.defaultAdminTheme'));
            return;
        }

        if ($this->request->is('ajax') || BcUtil::loginUser()) {
            $this->setResponse($this->getResponse()->withDisabledCache());
        }
    }

    /**
     * Before Render
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        if (!isset($this->RequestHandler) || !$this->RequestHandler->prefers('json')) {
            $this->setupFrontView();
        }
    }

    /**
     * フロント用のViewクラスをセットアップする
     * @checked
     * @noTodo
     */
    public function setupFrontView(): void
    {
        $this->viewBuilder()->setClassName('BaserCore.BcFrontApp');
        $this->viewBuilder()->setTheme(BcUtil::getCurrentTheme());
        $this->set($this->getService(AppServiceInterface::class)->getViewVarsForAll());
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
            $inenc = mb_detect_encoding((string) $value);
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
        if ($this->request->is('ajax')) {
            return;
        }
        if (empty(BcSiteConfig::get('maintenance'))) {
            return;
        }
        if (Configure::read('debug')) {
            return;
        }
        if ($this->getRequest()->is('maintenance')) {
            return;
        }
        if ($this->getRequest()->is('admin')) {
            return;
        }
        if (BcUtil::isAdminUser()) {
            return;
        }

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

}
