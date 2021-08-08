<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Component;
use BcSite;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use BaserCore\Controller\Admin\ContentsController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
/**
 * Class BcContentsComponent
 *
 * 階層コンテンツと連携したフォーム画面を作成する為のコンポーネント
 *
 * 《役割》
 * - コンテンツ一覧へのパンくずを自動追加
 * - フロントエンドでコンテンツデータを設定
 *        Controller / View にて、$this->request->getParam('Contents.Content') で参照できる
 * - コンテンツ保存フォームを自動表示
 * - コンテンツ保存フォームのデータソースを設定
 * - コンテンツ保存フォームの初期値を設定
 *
 * @package Baser.Controller.Component
 */
class BcContentsComponent extends Component
{

    /**
     * Content 保存フォームをコントローラーで利用するかどうか
     * settings で指定する
     *
     * @var bool
     */
    public $useForm = false;

    /**
     * コンテンツ編集用のアクション名
     * 判定に利用
     * settings で指定する
     *
     * @var string
     */
    public $editAction = 'admin_edit';

    /**
     * コンテンツタイプ
     * settings で指定する
     *
     * @var string
     */
    public $type = null;

    /**
     * コントローラー
     *
     * @var Controller
     */
    protected $_Controller = null;

    /**
     * プレビューモード
     *
     * @var string default Or alias
     */
    public $preview = null;

    /**
     * Initialize
     *
     * @param array $config
     * @return void
     * @checked
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->_Controller = $this->getController();
        $this->ControllerRequest = $this->_Controller->getRequest();
        // TODO:
        // $controller->uses[] = 'Contents';↓
        // $this->_Controller->Contents = new ContentsController();
        if (!$this->type) {
            if($this->_Controller->getPlugin()) {
                $this->type = $this->_Controller->getPlugin() . '.' . $this->_Controller->getName();
            } else {
                $this->type = $this->_Controller->getName();
            }
        }
        if (BcUtil::isAdminSystem(Router::url(null, false))) {
            // 管理システム設定
            $this->setupAdmin();
        } else {
            // フロントエンド設定
            $this->setupFront();
        }
    }

    /**
     * 管理システム設定
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupAdmin()
    {
        $createdSettings = BcUtil::getContentsItem();
        $this->setConfig('items', $createdSettings);
    }

    /**
     * フロントエンドのセットアップ
     */
    public function setupFront()
    {
        $controller = $this->_Controller;
        // プレビュー時のデータセット
        if (!empty($this->ControllerRequest->getQuery('preview'))) {
            $this->preview = $this->ControllerRequest->getQuery('preview');
            if (!empty($this->ControllerRequest->getData())) {
                $this->ControllerRequest = $this->ControllerRequest->withParam('Contents', $this->ControllerRequest->getData());
                $controller->Security->validatePost = false;
                $controller->Security->csrfCheck = false;
            }
        }

        // 表示設定
        if (!empty($this->ControllerRequest->getAttributes())) {
            // レイアウトテンプレート設定
            $controller->layout = $this->ControllerRequest->getParam('Contents.layout_template');
            if (!$controller->layout) {
                $controller->layout = $this->getParentLayoutTemplate($this->ControllerRequest->getParam('Contents.id'));
            }
            // パンくず
            $controller->crumbs = $this->getCrumbs($this->ControllerRequest->getParam('Contents.id'));
            // 説明文
            $controller->set('description', $this->ControllerRequest->getParam('Contents.description'));
            // タイトル
            $controller->pageTitle = $this->ControllerRequest->getParam('Contents.title');
        }

    }

    /**
     * パンくず用のデータを取得する
     *
     * @param $id
     * @return array
     */
    public function getCrumbs($id)
    {
        // ===========================================================================================
        // 2016/09/22 ryuring
        // PHP 7.0.8 環境にて、コンテンツ一覧追加時、検索インデックス作成のため、BcContentsComponent が
        // 呼び出されるが、その際、モデルのマジックメソッドの戻り値を返すタイミングで処理がストップしてしまう。
        // そのため、ビヘイビアのメソッドを直接実行して対処した。
        // CakePHPも、PHP自体のエラーも発生せず、ただ止まる。PHP7のバグ？PHP側のメモリーを256Mにしても変わらず。
        // ===========================================================================================
        // $contents = $this->_Controller->Contents->getBehavior('Tree')->getPath($this->_Controller->Contents, $id, [], -1); TODO: 結果を見るため元のものも残す
        // $contents = $this->_Controller->Contents->find('path', ['for' => $id]);
        $contents = []; //TODO: 代替手段
        unset($contents[count($contents) - 1]);
        $crumbs = [];
        foreach($contents as $content) {
            if (!$content['Content']['site_root']) {
                $crumb = [
                    'name' => $content['Content']['title'],
                    'url' => $content['Content']['url']
                ];
                $crumbs[] = $crumb;
            }
        }
        return $crumbs;
    }

    /**
     * Content データを取得する
     * @param int $entityId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContent($entityId = null)
    {
        return $this->_Controller->Contents->findByType($this->type, $entityId);
    }

    /**
     * Before render
     *
     * @param Controller $controller
     * @return void
     */
    public function beforeRender()
    {
        $controller = $this->getController();
        if (BcUtil::isAdminSystem(Router::url())) {
            $controller->set('contentsSettings', $this->getConfig('items'));
            // パンくずをセット
            array_unshift($controller->crumbs, ['name' => __d('baser', 'コンテンツ一覧'), 'url' => ['plugin' => null, 'controller' => 'contents', 'action' => 'index']]);
            if ($controller->subMenuElements && !in_array('contents', $controller->subMenuElements)) {
                array_unshift($controller->subMenuElements, 'contents');
            } else {
                $controller->subMenuElements = ['contents'];
            }
            if ($this->useForm && in_array($this->ControllerRequest->action, [$this->editAction, 'admin_edit_alias']) && !empty($this->ControllerRequest->getData('Content'))) {
                // フォームをセット
                $this->settingForm($controller, $this->ControllerRequest->getData('Content.site_id'), $this->ControllerRequest->getData('Content.id'));
                // フォームを読み込む為のイベントを設定
                // 内部で useForm を参照できない為、ここに記述。
                // フォームの設定しかできないイベントになってしまっている。
                // TODO 改善要
                // App::uses('BcContentsEventListener', 'Event');
                // CakeEventManager::instance()->attach(new BcContentsEventListener());
            }
        }

    }

    /**
     * コンテンツ保存フォームを設定する
     *
     * @param Controller $controller
     * @return void
     */
    public function settingForm(Controller $controller, $currentSiteId, $currentContentId = null)
    {

        // コントロールソースを設定
        $options = [];
        if ($controller->name == 'ContentFolders') {
            $options['excludeId'] = $currentContentId;
        }
        $data = $this->ControllerRequest->getData();

        $theme = $this->_Controller->siteConfigs['theme'];
        $site = BcSite::findById($data['Content']['site_id']);
        if ($site->theme) {
            $theme = $site->theme;
        }
        $templates = array_merge(
            BcUtil::getTemplateList('Layouts', '', $theme),
            BcUtil::getTemplateList('Layouts', $this->_Controller->plugin, $theme)
        );
        if ($data['Content']['id'] != 1) {
            $parentTemplate = $this->getParentLayoutTemplate($data['Content']['id']);
            if (in_array($parentTemplate, $templates)) {
                unset($templates[$parentTemplate]);
            }
            array_unshift($templates, ['' => __d('baser', '親フォルダの設定に従う') . '（' . $parentTemplate . '）']);
        }
        $data['Content']['name'] = urldecode($data['Content']['name']);
        if (Configure::read('BcApp.autoUpdateContentCreatedDate')) {
            $data['Content']['modified_date'] = date('Y-m-d H:i:s');
        }
        $controller->set('layoutTemplates', $templates);
        $controller->set('parentContents', $controller->Content->getContentFolderList($currentSiteId, $options));
        $controller->set('authors', $controller->User->getUserList());
        $Sites = TableRegistry::getTableLocator()->get('Sites');
        $site = $controller->Content->find('first', ['conditions' => ['Content.id' => $data['Content']['id']]]);
        if (!is_null($site['Site']['main_site_id'])) {
            $mainSiteId = $site['Site']['main_site_id'];
        } else {
            $mainSiteId = 0;
        }
        $siteList = [0 => ''] + $controller->Content->Site->find('list', ['fields' => ['id', 'display_name']]);
        $controller->set('sites', $siteList);
        $controller->set('mainSiteDisplayName', $controller->siteConfigs['main_site_display_name']);
        $data['Site'] = $site['Site'];
        $controller->set('mainSiteId', $mainSiteId);
        $controller->set('relatedContents', $Sites->getRelatedContents($data['Content']['id']));
        $related = false;
        if (($data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['alias_id']) ||
            $data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['type'] == 'ContentFolder') {
            $related = true;
        }
        $disableEditContent = false;
        $this->ControllerRequest = $this->ControllerRequest->withData($data);;
        if (!BcUtil::isAdminUser() || ($this->ControllerRequest->getData('Sites.relate_main_site') && $this->ControllerRequest->getData('Contents.main_site_content_id') &&
                ($this->ControllerRequest->getData('Contents.alias_id') || $this->ControllerRequest->getData('Contents.type') == 'ContentFolder'))) {
            $disableEditContent = true;
        }
        $currentSiteId = $siteId = $this->ControllerRequest->getData('Sites.id');
        if (is_null($currentSiteId)) {
            $currentSiteId = 0;
        }
        $controller->set('currentSiteId', $currentSiteId);
        $controller->set('disableEditContent', $disableEditContent);
        $controller->set('related', $related);
    }

    /**
     * レイアウトテンプレートを取得する
     *
     * @param $id
     * @return string $parentTemplate|false
     */
    public function getParentLayoutTemplate($id)
    {
        if (!$id) {
            return false;
        }
        // ===========================================================================================
        // 2016/09/22 ryuring
        // PHP 7.0.8 環境にて、コンテンツ一覧追加時、検索インデックス作成のため、BcContentsComponent が
        // 呼び出されるが、その際、モデルのマジックメソッドの戻り値を返すタイミングで処理がストップしてしまう。
        // そのため、ビヘイビアのメソッドを直接実行して対処した。
        // CakePHPも、PHP自体のエラーも発生せず、ただ止まる。PHP7のバグ？PHP側のメモリーを256Mにしても変わらず。
        // ===========================================================================================
        $contents = $this->_Controller->Contents->find('path', ['for' => $id]);
        $contents = array_reverse($contents);
        unset($contents[0]);
        if (!$contents) {
            return false;
        }
        $parentTemplates = Hash::extract($contents, '{n}.Content.layout_template');
        foreach($parentTemplates as $parentTemplate) {
            if ($parentTemplate) {
                break;
            }
        }
        return $parentTemplate;
    }

    /**
     * 登録されているタイプの一覧を取得する
     *
     * @return array
     */
    public function getTypes()
    {
        $types = [];
        foreach($this->getConfig('items') as $key => $value) {
            $types[$key] = $value['title'];
        }
        return $types;
    }

}
