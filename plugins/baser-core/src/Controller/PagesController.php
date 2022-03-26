<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
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
use BaserCore\Model\Entity\Page;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Service\PageServiceInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\View\Exception\MissingViewException;
use BaserCore\Service\ContentFolderServiceInterface;
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
		$page = $pageService->get($this->request->getParam('Content.entity_id'));

		/* @var Page $page */
		$template = $page->page_template;
		if (!$template) {
			$template = $contentFolderService->getParentTemplate($this->request->getParam('Content.id'), 'page');
		}

        $this->set('page', $page);

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
