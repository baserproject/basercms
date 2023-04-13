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

namespace BcMail\Controller\Admin;

use BaserCore\Error\BcException;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcMail\Service\Admin\MailMessagesAdminService;
use BcMail\Service\Admin\MailMessagesAdminServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\Response;

/**
 * 受信メールコントローラー
 *
 */
class MailMessagesController extends MailAdminAppController
{

    /**
     * initialize
     * @return void
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', [
            'entityVarName' => 'mailContent'
        ]);
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $mailContentId = $this->request->getParam('pass.0');
        if (!$mailContentId) throw new BcException(__d('baser_core', '不正なURLです。'));
        /* @var ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $request = $contentsService->setCurrentToRequest(
            'BcMail.MailContent',
            $mailContentId,
            $this->getRequest()
        );
        if (!$request) throw new BcException(__d('baser_core', 'コンテンツデータが見つかりません。'));
        $this->setRequest($request);
    }

    /**
     * [ADMIN] 受信メール一覧
     *
     * @param MailMessagesAdminService $service
     * @param int $mailContentId
     * @return void
     * @checked
     * @noTodo
     */
    public function index(MailMessagesAdminServiceInterface $service, int $mailContentId)
    {
        $this->setViewConditions('MailMessage', [
            'group' => $mailContentId,
            'default' => [
                'query' => [
                    'limit' => BcSiteConfig::get('admin_list_num'),
                    'sort' => 'created',
                    'direction' => 'desc',
                ]]]);
        $service->setup($mailContentId);
        $this->set($service->getViewVarsForIndex(
            $mailContentId,
            $this->paginate($service->getIndex($this->getRequest()->getQueryParams()))
        ));
    }

    /**
     * [ADMIN] 受信メール詳細
     *
     * @param MailMessagesAdminService $service
     * @param int $mailContentId
     * @param int $messageId
     * @return void
     * @checked
     * @noTodo
     */
    public function view(
        MailMessagesAdminServiceInterface $service,
        int $mailContentId,
        int $messageId
    )
    {
        $service->setup($mailContentId);
        $this->set($service->getViewVarsForView($mailContentId, $messageId));
    }

    /**
     * [ADMIN] 受信メール削除
     *
     * @param MailMessagesServiceInterface $service
     * @param MailContentsServiceInterface $mailContentsService
     * @param int $mailContentId
     * @param int $id
     * @return void|Response
     * @checked
     * @noTodo
     */
    public function delete(
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        int $mailContentId,
        int $id
    )
    {
        $this->request->allowMethod(['post', 'delete']);
        $mailContent = $mailContentsService->get($mailContentId);
        try {
            $service->setup($mailContentId);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core',
                    '{0} への受信データ NO「{1}」 を削除しました。',
                    $mailContent->content->title,
                    $id
                ));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * メールフォームに添付したファイルを開く
     */
    public function attachment(MailMessagesServiceInterface $service)
    {
        $args = func_get_args();
        $mailContentId = $args[1];
        unset($args[0], $args[1]);
        $file = implode('/', $args);
        $service->MailMessages->setup($mailContentId);
        $settings = $service->MailMessages->getBehavior('BcUpload')->getSettings();
        $filePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $file;
        $ext = BcUtil::decodeContent(null, $file);
        $mineType = 'application/octet-stream';
        if ($ext !== 'gif' && $ext !== 'jpg' && $ext !== 'png') {
            Header("Content-disposition: attachment; filename=" . $file);
        } else {
            $mineType = 'image/' . $ext;
        }
        Header(sprintf('Content-type: %s; name=%s', $mineType, $file));
        echo file_get_contents($filePath);
        exit();
    }

    /**
     * メッセージCSVファイルをダウンロードする
     *
     * @param int $mailContentId
     * @return void
     */
    public function download_csv(MailMessagesAdminServiceInterface $service, int $mailContentId)
    {
        $this->set($service->getViewVarsForDownloadCsv($mailContentId, $this->getRequest()));
    }

}
