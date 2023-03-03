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
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesService;
use Cake\Http\ServerRequest;
use Cake\ORM\ResultSet;

/**
 * MailMessagesAdminService
 */
class MailMessagesAdminService extends MailMessagesService implements MailMessagesAdminServiceInterface
{

    /**
     * メールメッセージ一覧用の View 変数を取得する
     *
     * @param int $mailContentId
     * @param ResultSet $mailMessages
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(int $mailContentId, ResultSet $mailMessages): array
    {
        /** @var MailContentsService $mailContentsService */
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        /** @var MailContent $mailContent */
        $mailContent = $mailContentsService->get($mailContentId);
        return [
            'mailContent' => $mailContent,
            'mailFields' => $mailContent->mail_fields,
            'mailMessages' => $mailMessages
        ];
    }

    /**
     * メールメッセージ詳細画面用 View 変数を取得する
     *
     * @param int $mailContentId
     * @param int $mailMessageId
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForView(int $mailContentId, int $mailMessageId)
    {
        /** @var MailContentsService $mailContentsService */
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        /** @var MailContent $mailContent */
        $mailContent = $mailContentsService->get($mailContentId);
        return [
            'mailContent' => $mailContent,
            'mailMessage' => $this->get($mailMessageId),
            'mailFields' => $mailContent->mail_fields
        ];
    }

    /**
     * CSVダウンロード用の変数を取得する
     *
     * @param int $mailContentId
     * @param ServerRequest $request
     * @return array
     */
    public function getViewVarsForDownloadCsv(int $mailContentId, ServerRequest $request)
    {
        $this->setup($mailContentId);
        return [
            'encoding' => $request->getQuery('encoding'),
            'messages' => $this->MailMessages->convertMessageToCsv($this->getIndex()),
            'contentName' => $request->getAttribute('currentContent')->name,
        ];
    }

}
