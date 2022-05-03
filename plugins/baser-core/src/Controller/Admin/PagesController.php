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

namespace BaserCore\Controller\Admin;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Vendor\CKEditorStyleParser;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use Cake\Utility\Inflector;

/**
 * PagesController
 */
class PagesController extends BcAdminAppController
{

	/**
	 * コンポーネント
	 *
	 * @var array
	 * @deprecated useViewCache 5.0.0 since 4.0.0
	 *    CakePHP3では、ビューキャッシュは廃止となるため、別の方法に移行する
	 */
	// TODO ucmitz 未移行
	/* >>>
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail', 'BcContents' => ['useForm' => true, 'useViewCache' => true]];
    <<< */

    /**
     * initialize
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents');
    }

	/**
	 * beforeFilter
	 *
	 * @return void
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);
        if (BcSiteConfig::get('editor') && BcSiteConfig::get('editor') !== 'none') {
            $this->viewBuilder()->addHelpers([BcSiteConfig::get('editor'), 'BaserCore.BcGooglemaps', 'BaserCore.BcText', 'BaserCore.BcFreeze']);
        }
	}

	/**
	 * [ADMIN] 固定ページ情報編集
     * @param int $id (page_id)
	 * @param PagesServiceInterface $pageService
	 * @param ContentsServiceInterface $contentService
	 * @param SitesServiceInterface $siteService
	 * @param SiteConfigsServiceInterface $siteConfigService
	 * @return void
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function edit($id, PagesServiceInterface $pageService, ContentsServiceInterface $contentService, SitesServiceInterface $siteService, SiteConfigsServiceInterface $siteConfigService)
	{
		if (!$id && empty($this->request->getData())) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['controller' => 'contents', 'action' => 'index']);
		}
        $page = $pageService->get($id);
		if ($this->request->is(['patch', 'post', 'put'])) {
			if (BcUtil::isOverPostSize()) {
				$this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
				$this->redirect(['action' => 'edit', $id]);
			}
			// EVENT Pages.beforeEdit
			$event = $this->dispatchEvent('beforeEdit', [
				'request' => $this->request,
			]);
			if ($event !== false) {
				$this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
			}
            try {
                // contents_tmpをcontentsに反映
                $this->request = $this->request->withData('Pages.contents', $this->request->getData('Pages.contents_tmp'));
                $page = $pageService->update($page, $this->request->getData('Pages'));

				// 完了メッセージ
				$site = $siteService->findById($page->content->site_id)->first();
				$url = $contentService->getUrl($page->content->url, true, $site->useSubDomain);
				// EVENT Pages.afterEdit
				$this->dispatchEvent('afterEdit', [
					'request' => $this->request,
				]);
                $this->BcMessage->setSuccess(sprintf(__d('baser', "固定ページ「%s」を更新しました。\n%s"), $page->content->title, rawurldecode($url)));
				// 同固定ページへリダイレクト
				return $this->redirect(['action' => 'edit', $id]);
            }  catch (\Exception $e) {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
		} else {
			$this->request = $this->request->withData("Pages", $page);
			if (!$this->request->getData()) {
				$this->BcMessage->setError(__d('baser', '無効な処理です。'));
				$this->redirect(['controller' => 'contents', 'action' => 'index']);
			}
		}

		// エディタオプション
		$editorOptions = ['editorDisableDraft' => false];
        $editorStyles = $siteConfigService->getValue('editor_styles');
		if ($editorStyles) {
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet' => 'default',
				'editorStyles' => [
					'default' => $CKEditorStyleParser->parse($editorStyles)
				]
			]);
		}
		// ページテンプレートリスト
		$theme = [Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'))];
		$site = $siteService->findById($page->content->site_id)->first();
		if (!empty($site) && $site->theme && $site->theme != $theme[0]) {
			$theme[] = $site->theme;
		}
		$pageTemplateList = $pageService->getPageTemplateList($page->content->id, $theme);
        $editor = $siteConfigService->getValue('editor');
        $editor_enter_br = $siteConfigService->getValue('editor_enter_br');
		$this->set(compact('editorOptions', 'pageTemplateList', 'page', 'editor', 'editor_enter_br'));
		$this->render('form');
	}
}
