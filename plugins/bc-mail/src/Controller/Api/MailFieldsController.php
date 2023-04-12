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

namespace BcMail\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcMail\Service\MailFieldsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドコントローラー
 */
class MailFieldsController extends BcApiController
{

    /**
     * [API] メールフィールド API 一覧取得
     *
     * @param MailFieldsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(MailFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $mailFields = $message = null;
        try {
            $queryParams = array_merge([
                'contain' => null,
                'status' => 'publish'
            ], $queryParams);
            $mailFields = $this->paginate($service->getIndex($queryParams['mail_content_id'], $queryParams));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'mailFields' => $mailFields,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailFields', 'message']);
    }

    /**
     * [API] メールフィールド API 単一データ取得
     *
     * @param MailFieldsServiceInterface $service
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(MailFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }
        $queryParams = array_merge([
            'status' => 'publish',
            'contain' => null
        ], $queryParams);

        $this->set([
            'mailField' => $service->get($id, $queryParams)
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField']);
    }

}
