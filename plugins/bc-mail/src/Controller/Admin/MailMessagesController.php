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
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        $response = parent::beforeFilter($event);
        if($response) return $response;
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
     * @unitTest
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
     * @unitTest
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
     * @unitTest
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
     * @param MailMessagesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function attachment(MailMessagesServiceInterface $service)
    {
        $this->disableAutoRender();
        $args = func_get_args();
        $mailContentId = $args[1];
        unset($args[0], $args[1]);
        $file = implode('/', $args);
        $service->MailMessages->setup($mailContentId);
        $settings = $service->MailMessages->getBehavior('BcUpload')->getSettings();
        $basePath = realpath(WWW_ROOT . 'files' . DS . $settings['saveDir']);
        $filePath = realpath($basePath . DS . $file);

        // basePath配下でない場合は表示しない
        if (strpos($filePath, $basePath) !== 0) {
            $this->notFound();
        }

        $ext = BcUtil::decodeContent(null, $file);
        if ($ext !== 'gif' && $ext !== 'jpg' && $ext !== 'png') {
            $isImage = false;
            $isDownload = true;
            $mineType = 'application/octet-stream';
        } else {
            $isImage = true;
            $isDownload = false;
            $mineType = 'image/' . $ext;
        }

        $response = $this->getResponse()
            ->withHeader('Content-type', sprintf('%s; name=%s', $mineType, $file))
            ->withFile($filePath, [
                'name' => $file,
                'download' => $isDownload,
            ]);
        if(!$isImage) {
            $response = $response->withHeader('Content-disposition', sprintf('attachment; filename=%s', $file));
        }

        $this->setResponse($response);
    }

    /**
     * メッセージCSVファイルをダウンロードする
     *
     * @param int $mailContentId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download_csv(MailMessagesAdminServiceInterface $service, int $mailContentId)
    {
        $this->set($service->getViewVarsForDownloadCsv($mailContentId, $this->getRequest()));
    }

}
