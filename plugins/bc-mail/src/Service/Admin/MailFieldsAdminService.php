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

namespace BcMail\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsService;
use Cake\Datasource\EntityInterface;
use Cake\Http\Server;
use Cake\Http\ServerRequest;

/**
 * MailFieldsService
 */
class MailFieldsAdminService extends MailFieldsService implements MailFieldsAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 一覧画面用の View 変数を取得する
     * @param int $mailContentId
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(ServerRequest $request, int $mailContentId)
    {
        /* @var \BcMail\Service\MailContentsService $mailContentsService */
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $mailContentsService->get($mailContentId);
        return [
            'mailContent' => $mailContent,
            'mailFields' =>  $this->getIndex($mailContentId),
            'publishLink' => $this->getPublishLink($mailContent),
            'sortmode' => $request->getQuery('sortmode')
        ];
    }

    /**
     * 新規登録画面用の View 変数を取得する
     * @param int $mailContentId
     * @param EntityInterface $mailField
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForAdd(int $mailContentId, EntityInterface $mailField)
    {
        /* @var \BcMail\Service\MailContentsService $mailContentsService */
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $mailContentsService->get($mailContentId);
        return [
            'mailContent' => $mailContent,
            'mailField' =>  $mailField,
            'publishLink' => $this->getPublishLink($mailContent),
            'autoCompleteOptions' => $this->getAutoCompleteOptions(),
        ];
    }

    /**
     * 編集画面用の View 変数を取得する
     * @param int $mailContentId
     * @param EntityInterface $mailField
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForEdit(int $mailContentId, EntityInterface $mailField)
    {
        /* @var \BcMail\Service\MailContentsService $mailContentsService */
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailField->valid_ex = explode(',', $mailField->valid_ex);
        $mailContent = $mailContentsService->get($mailContentId);
        return [
            'mailContent' => $mailContent,
            'mailField' =>  $mailField,
            'publishLink' => $this->getPublishLink($mailContent),
            'autoCompleteOptions' => $this->getAutoCompleteOptions(),
        ];
    }

    /**
     * 公開用のリンクを取得する
     * @param EntityInterface|MailContent $mailContent
     * @return null|string
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテストは実装しない
     */
    public function getPublishLink(EntityInterface $mailContent)
    {
        /** @var MailContentsAdminService $mailContentsService */
        $mailContentsService = $this->getService(MailContentsAdminServiceInterface::class);
        return $mailContentsService->getPublishLink($mailContent);
    }

}
