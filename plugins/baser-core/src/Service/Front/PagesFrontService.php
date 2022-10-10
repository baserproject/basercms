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

namespace BaserCore\Service\Front;

use BaserCore\Model\Entity\Page;
use BaserCore\Model\Validation\BcValidation;
use BaserCore\Service\PagesService;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PagesFrontService
 */
class PagesFrontService extends PagesService implements PagesFrontServiceInterface
{

    /**
     * 固定ページ用のデータを取得する
     * @param EntityInterface $page
     * @param ServerRequest $request
     * @return array
     * @noTodo
     * @checked
     */
    public function getViewVarsForView(EntityInterface $page, ServerRequest $request): array
    {
        if ($request->is(['patch', 'post', 'put'])) {
            $pageArray = $request->getData();
            if($request->getQuery('preview') === 'draft') {
                $pageArray['contents'] = $pageArray['draft'];
            }
            if (!BcValidation::containsScript($pageArray['contents'])) {
                throw new NotFoundException(__d('baser', '本文欄でスクリプトの入力は許可されていません。'));
            }
            $page = $this->Pages->patchEntity($page, $pageArray);
            $page->content = $this->Contents->saveTmpFiles($request->getData('content'), mt_rand(0, 99999999));
            $editLink = null;
        } else {
            $editLink = $this->getEditLink($request);
        }
        return [
            'page' => $page,
            'editLink' => $editLink
        ];
    }

    /**
     * プレビューのためのセットアップを行う
     * @param Controller $controller
     * @noTodo
     * @checked
     */
    public function setupPreviewForView(Controller $controller): void
    {
        /* @var Page $page */
        $page = $this->get(
            $controller->getRequest()->getAttribute('currentContent')->entity_id,
            ['status' => 'publish']
        );
        $controller->viewBuilder()->setTemplate(
            $this->getPageTemplate($page)
        );
        $controller->set($this->getViewVarsForView($page, $controller->getRequest()));
    }

}
