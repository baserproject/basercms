<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller.Component
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcContentsComponent
 *
 * 階層コンテンツと連携したフォーム画面を作成する為のコンポーネント
 *
 * 《役割》
 * - コンテンツ一覧へのパンくずを自動追加
 * - フロントエンドでコンテンツデータを設定
 *        Controller / View にて、$this->request->getParam('Content') で参照できる
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
     * @param Controller $controller Controller with components to initialize
     * @return void
     */
    public function initialize(Controller $controller)
    {
        $this->_Controller = $controller;
        $controller->uses[] = 'Content';
        if (!$this->type) {
            if ($controller->plugin) {
                $this->type = $controller->plugin . '.' . $controller->modelClass;
            } else {
                $this->type = $controller->modelClass;
            }
        }
        if (!BcUtil::isAdminSystem()) {
            // フロントエンド設定
            $this->setupFront();
        } else {
            // 管理システム設定
            $this->setupAdmin();
        }
    }

    /**
     * 管理システム設定
     */
    public function setupAdmin()
    {
        $items = Configure::read('BcContents.items');
        $createdSettings = [];
        foreach($items as $name => $settings) {
            foreach($settings as $type => $setting) {
                $setting['plugin'] = $name;
                $setting['type'] = $type;
                $createdSettings[$type] = $setting;
            }
        }
        $this->settings['items'] = $createdSettings;
    }

    /**
     * フロントエンドのセットアップ
     */
    public function setupFront()
    {
        $controller = $this->_Controller;
        // プレビュー時のデータセット
        if (!empty($controller->request->query['preview'])) {
            $this->preview = $this->_Controller->request->query['preview'];
            if (!empty($controller->request->getData('Content'))) {
                $controller->request = $controller->request->withParam('Content', $controller->request->getData('Content'));
                $controller->Security->validatePost = false;
                $controller->Security->csrfCheck = false;
            }
        }

        // 表示設定
        if (!empty($controller->request->getParam('Content'))) {
            // レイアウトテンプレート設定
            $controller->layout = $controller->request->params['Content']['layout_template'];
            if (!$controller->layout) {
                $controller->layout = $this->getParentLayoutTemplate($controller->request->params['Content']['id']);
            }
            // パンくず
            $controller->crumbs = $this->getCrumbs($controller->request->params['Content']['id']);
            // 説明文
            $controller->set('description', $controller->request->params['Content']['description']);
            // タイトル
            $controller->pageTitle = $controller->request->params['Content']['title'];
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
        $contents = $this->_Controller->Content->Behaviors->Tree->getPath($this->_Controller->Content, $id, [], -1);
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
     *
     * @param int $entityId
     * @return array
     */
    public function getContent($entityId = null)
    {
        return $this->_Controller->Content->findByType($this->type, $entityId);
    }

    /**
     * Before render
     *
     * @param Controller $controller
     * @return void
     */
    public function beforeRender(Controller $controller)
    {
        parent::beforeRender($controller);
        if (BcUtil::isAdminSystem()) {
            $controller->set('contentsSettings', $this->settings['items']);
            // パンくずをセット
            array_unshift($controller->crumbs, ['name' => __d('baser', 'コンテンツ一覧'), 'url' => ['plugin' => null, 'controller' => 'contents', 'action' => 'index']]);
            if ($controller->subMenuElements && !in_array('contents', $controller->subMenuElements)) {
                array_unshift($controller->subMenuElements, 'contents');
            } else {
                $controller->subMenuElements = ['contents'];
            }
            if ($this->useForm && in_array($controller->request->action, [$this->editAction, 'admin_edit_alias']) && !empty($controller->request->getData('Content'))) {
                // フォームをセット
                $this->settingForm($controller, $controller->request->getData('Content.site_id'), $controller->request->getData('Content.id'));
                // フォームを読み込む為のイベントを設定
                // 内部で useForm を参照できない為、ここに記述。
                // フォームの設定しかできないイベントになってしまっている。
                // TODO 改善要
                App::uses('BcContentsEventListener', 'Event');
                CakeEventManager::instance()->attach(new BcContentsEventListener());
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
        $data = $controller->request->data;

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
        $Site = ClassRegistry::init('Site');
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
        $controller->set('relatedContents', $Site->getRelatedContents($data['Content']['id']));
        $related = false;
        if (($data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['alias_id']) ||
            $data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['type'] == 'ContentFolder') {
            $related = true;
        }
        $disableEditContent = false;
        $controller->request->data = $data;
        if (!BcUtil::isAdminUser() || ($controller->request->getData('Site.relate_main_site') && $controller->request->getData('Content.main_site_content_id') &&
                ($controller->request->getData('Content.alias_id') || $controller->request->getData('Content.type') == 'ContentFolder'))) {
            $disableEditContent = true;
        }
        $currentSiteId = $siteId = $controller->request->getData('Site.id');
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
        $contents = $this->_Controller->Content->Behaviors->Tree->getPath($this->_Controller->Content, $id);
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
        foreach($this->settings['items'] as $key => $value) {
            $types[$key] = $value['title'];
        }
        return $types;
    }

}
