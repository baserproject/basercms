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

namespace BcCustomContent\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcCustomContent\Service\CustomLinksServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomLinksController
 */
class CustomLinksController extends BcApiController
{

    /**
     * 一覧取得API
     *
     * @param CustomLinksServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(CustomLinksServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }

        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'contain' => null,
            'status' => 'publish'
        ], $queryParams);

        $this->set([
            'customLinks' => $this->paginate(
                $service->getIndex($queryParams['custom_table_id'], $queryParams)
            )
        ]);
        $this->viewBuilder()->setOption('serialize', ['customLinks']);
    }

    /**
     * 単一データAPI
     *
     * @param CustomLinksServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomLinksServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $customLink = $message = null;
        try {
            $customLink = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        }

        $this->set([
            'customLink' => $customLink,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customLink']);
    }

}
