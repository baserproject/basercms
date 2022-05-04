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

use BaserCore\Service\PagesFrontServiceInterface;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BaserCore\Model\Entity\Page;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Service\PagesServiceInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\View\Exception\MissingViewException;
use BaserCore\Service\ContentFoldersServiceInterface;
use BaserCore\Controller\Component\BcFrontContentsComponent;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * PagesController
 * @property PagesTable $Pages
 * @property BcFrontContentsComponent $BcFrontContents
 */
class PagesController extends BcFrontAppController
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
        $this->loadModel('BaserCore.Contents');
    }

	/**
	 * ビューを表示する
	 * @param PagesServiceInterface $pageService
	 * @param ContentFoldersServiceInterface $contentFolderService
	 * @return \Cake\Http\Response|void
     * @throws ForbiddenException When a directory traversal attempt.
	 * @throws NotFoundException When the view file could not be found
	 *   or MissingViewException in debug mode.
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function display(PagesFrontServiceInterface $pageService, ContentFoldersServiceInterface $contentFolderService)
	{
		$path = func_get_args();

		if ($this->request->getParam('Content')->alias_id) {
			$urlTmp = $this->Contents->find()->where(['Contents.id' => $this->request->getParam('Content')->alias_id])->first()->url;
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

		$page = $pageService->get($this->request->getParam('Content.entity_id'));

		/* @var Page $page */
		$template = $page->page_template;
		if (!$template) {
			$template = $contentFolderService->getParentTemplate($this->request->getParam('Content.id'), 'page');
		}

        $this->set($pageService->getViewVarsForDisplay($page, $this->getRequest()));

		try {
			$this->render('/Pages/' . $template);
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
}
