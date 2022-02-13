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

namespace BaserCore\Controller;

use Cake\Utility\Text;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\Page;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Service\PageServiceInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\View\Exception\MissingViewException;
use BaserCore\Service\ContentFolderServiceInterface;
use BaserCore\Controller\Component\BcFrontContentsComponent;

/**
 * PagesController
 * @property PagesTable $Pages
 * @property BcFrontContentsComponent $BcFrontContents
 */
class PagesController extends AppController
{

    /**
     * Trait
     * NOTE: BcAppControllerにもあるので、移行時に取り除く
     */
    use BcContainerTrait;

    /**
     * initialize
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents');
    }

	/**
	 * ビューを表示する
	 * @param PageServiceInterface $pageService
	 * @param ContentFolderServiceInterface $contentFolderService
	 * @return \Cake\Http\Response|void
     * @throws ForbiddenException When a directory traversal attempt.
	 * @throws NotFoundException When the view file could not be found
	 *   or MissingViewException in debug mode.
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function display(PageServiceInterface $pageService, ContentFolderServiceInterface $contentFolderService)
	{
		$path = func_get_args();

		if ($this->request->getParam('Content')->alias_id) {
			$urlTmp = $this->Content->field('url', ['Content.id' => $this->request->getParam('Content')->alias_id]);
		} else {
			$urlTmp = $this->request->getParam('Content')->url;
		}

		if ($this->request->getParam('Content')->alias) {
            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
			$site = $sites->findByUrl($urlTmp);
			if ($site && ($site->alias == $this->request->getParam('Site')->alias)) {
				$urlTmp = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/' . $this->request->getParam('Site')->name . '/', $urlTmp);
			}
		}

		if (isset($urlTmp)) {
			$urlTmp = preg_replace('/^\//', '', $urlTmp);
			$path = explode('/', $urlTmp);
		}
		// <<<

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		if (in_array('..', $path, true) || in_array('.', $path, true)) {
			throw new ForbiddenException();
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		$previewCreated = false;
		if ($this->request->getData()) {
			// POSTパラメータのコードに含まれるscriptタグをそのままHTMLに出力するとブラウザによりXSSと判定される
			// 一度データをセッションに退避する
			if ($this->BcFrontContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->getData('Content.entity_id');
				$this->request = $this->request->withParsedBody($this->Content->saveTmpFiles($this->request->getData(), mt_rand(0, 99999999)));
				$this->Session->write($sessionKey, $this->request->getData());
				$query = [];
				if ($this->request->getQuery()) {
					foreach($this->request->getQuery() as $key => $value) {
						$query[] = $key . '=' . $value;
					}
				}
				$redirectUrl = '/';
				if ($this->request->getPath()) {
					$redirectUrl .= $this->request->getPath();
				}
				if ($query) {
					$redirectUrl .= '?' . implode('&', $query);
				}
				$this->redirect($redirectUrl);
				return;
			}

			if ($this->BcFrontContents->preview === 'draft') {
				$this->request = $this->request->withParsedBody($this->Content->saveTmpFiles($this->request->getData(), mt_rand(0, 99999999)));
				$this->request->withParam('Content.eyecatch', $this->request->getData('Content.eyecatch'));
				$uuid = $this->_createPreviewTemplate($pageService, $this->request->getData());
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
				$previewCreated = true;
			}

		} else {
			// プレビューアクセス
			if ($this->BcFrontContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->getParam('Content.entity_id');
				$previewData = $this->request->getSession()->read($sessionKey);
				$this->request->withParam('Content.eyecatch', $previewData['Content']['eyecatch']);

				if (!is_null($previewData)) {
					$this->request->getSession()->delete($sessionKey);
					$uuid = $this->_createPreviewTemplate($pageService, $previewData);
					$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
					$previewCreated = true;
				}
			}

			// 草稿アクセス
			if ($this->BcFrontContents->preview === 'draft') {
                $data = $pageService->get($this->request->getParam('Content.entity_id'));
				$uuid = $this->_createPreviewTemplate($data, true);
				// TODO ucmitz previewTemplate 不要
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
				$previewCreated = true;
			}
		}

		$page = $pageService->get($this->request->getParam('Content.entity_id'));

		/* @var Page $page */
		$template = $page->page_template;
		if (!$template) {
			$template = $contentFolderService->getParentTemplate($this->request->getParam('Content.id'), 'page');
		}

        $this->set('pageContent', $page->contents);

		try {
			$this->render('/Pages/' . $template);
			if ($previewCreated) {
				@unlink(TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
			}
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	/**
	 * プレビュー用テンプレートを生成する
	 *
	 * 一時ファイルとしてビューを保存
	 * タグ中にPHPタグが入る為、ファイルに保存する必要がある
	 *@param PageServiceInterface $pageService
	 * @param $data
	 * @param bool $isDraft
	 * @return string uuid
	 */
	protected function _createPreviewTemplate($pageService, $data, $isDraft = false)
	{
		if (!$isDraft) {
			// postで送信される前提
			if (!empty($data['Page']['contents_tmp'])) {
				$contents = $data['Page']['contents_tmp'];
			} else {
				$contents = $data['Page']['contents'];
			}
		} else {
			$contents = $data['Page']['draft'];
		}
		$contents = $pageService->addBaserPageTag(
			null,
			$contents,
			$data['Content']['title'],
			$data['Content']['description'],
		);
		$uuid = Text::uuid();
		$path = TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt');
		$file = new File($path);
		$file->open('w');
		$file->append($contents);
		$file->close();
		unset($file);
		@chmod($path, 0666);
		return $uuid;
	}

}
