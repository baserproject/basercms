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

namespace BcBlog\Service\Front;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Entity\BlogContent;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\TableRegistry;

/**
 * BlogFrontService
 */
class BlogFrontService implements BlogFrontServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * プレビュー用の view 変数を取得する
     * @param ServerRequest $request
     * @param array $options
     * @return array[]
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(ServerRequest $request, array $options = []): array
    {
        /* @var BlogContentsService $blogContentsService */
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogContent = $blogContentsService->get((int) $request->getParam('entityId'), $options);
        $editLink = null;
        if(BcUtil::loginUser()) {
            $editLink = [
                'prefix' => 'Admin',
                'plugin' => 'BcBlog',
                'controller' => 'BlogContents',
                'action' => 'edit',
                $blogContent->id
            ];
        }
        return [
            'blogContent' => $blogContent,
            // TODO ucmitz posts 未実装
            'posts' => [],
            'single' => false,
            'editLink' => $editLink
        ];
    }

    /**
     * プレビュー用のセットアップをする
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForIndex(Controller $controller): void
    {
        $blogContentsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogContents');
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');

        $vars = $this->getViewVarsForIndex($controller->getRequest());
        /* @var BlogContent $vars['blogContent'] */
        $vars['blogContent'] = $blogContentsTable->patchEntity(
            $vars['blogContent'],
            $controller->getRequest()->getData()
        );
        $vars['blogContent']->content = $contentsTable->saveTmpFiles(
            $controller->getRequest()->getData('content'),
            mt_rand(0, 99999999)
        );
        unset($vars['editLink']);

        $controller->set($vars);
        $controller->viewBuilder()->setTemplate($this->getIndexTemplate($vars['blogContent']));
    }

    /**
     * 一覧用のテンプレート名を取得する
     * @param BlogContent|EntityInterface $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexTemplate($blogContent)
    {
        return 'Blog/' . $blogContent->template . DS . 'index';
    }

}
