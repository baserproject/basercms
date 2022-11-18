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

use BaserCore\Controller\Admin\BcAdminAppController;
use Cake\Event\EventInterface;

/**
 * 受信メールコントローラー
 *
 * @package Mail.Controller
 */
class MailMessagesController extends BcAdminAppController
{

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents'];

    /**
     * メールコンテンツデータ
     *
     * @var array
     */
    public $mailContent;

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $content = $this->BcContents->getContent($this->request->param('pass.0'));
        if (!$content) {
            $this->notFound();
        }
        $this->mailContent = $this->MailContent->read(null, $this->request->param('pass.0'));
        App::uses('MailMessage', 'BcMail.Model');
        $this->MailMessage = new MailMessage();
        $this->MailMessage->setup($this->mailContent['MailContent']['id']);
        $mailContentId = $this->request->param('pass.0');
        $content = $this->BcContents->getContent($mailContentId);
        $this->request->param('Content', $content['Content']);
    }

    /**
     * beforeRender
     *
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->set('mailContent', $this->mailContent);
    }

    /**
     * [ADMIN] 受信メール一覧
     *
     * @param int $mailContentId
     * @return void
     */
    public function admin_index($mailContentId)
    {
        $default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
        $this->setViewConditions('MailMessage', ['default' => $default]);
        $this->paginate = [
            'fields' => [],
            'order' => 'MailMessage.created DESC',
            'limit' => $this->passedArgs['num']
        ];
        $messages = $this->paginate('MailMessage');
        $mailFields = $this->MailMessage->mailFields;

        $this->set(compact('messages', 'mailFields'));

        if ($this->RequestHandler->isAjax() || !empty($this->request->getQuery('ajax'))) {
            $this->render('ajax_index');
            return;
        }

        $this->pageTitle = sprintf(
            __d('baser', '%s｜受信メール一覧'),
            $this->request->param('Content.title')
        );
        $this->setHelp('mail_messages_index');
    }

    /**
     * [ADMIN] 受信メール詳細
     *
     * @param int $mailContentId
     * @param int $messageId
     * @return void
     */
    public function admin_view($mailContentId, $messageId)
    {
        if (!$mailContentId || !$messageId) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->notFound();
        }
        $message = $this->MailMessage->find('first', [
            'conditions' => ['MailMessage.id' => $messageId],
            'order' => 'created DESC'
        ]);
        $mailFields = $this->MailMessage->mailFields;
        $this->set(compact('message', 'mailFields'));
        $this->pageTitle = sprintf(
            __d('baser', '%s｜受信メール詳細'),
            $this->request->param('Content.title')
        );
    }

    /**
     * [ADMIN] 受信メール一括削除
     *
     * @param int $mailContentId
     * @param int $messageId
     * @return bool
     */
    protected function _batch_del($ids)
    {
        if ($ids) {
            foreach ($ids as $id) {
                $this->_del($id);
            }
        }
        return true;
    }

    /**
     * [ADMIN] 受信メール削除　(ajax)
     *
     * @param int $mailContentId
     * @param int $messageId
     * @return void
     */
    public function admin_ajax_delete($mailContentId, $messageId)
    {
        $this->_checkSubmitToken();
        if (!$messageId) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_del($messageId)) {
            exit;
        }

        exit(true);
    }

    /**
     * 受信メール削除　
     *
     * @param int $mailContentId
     * @param int $messageId
     * @return bool
     */
    protected function _del($id = null)
    {
        if (!$this->MailMessage->delete($id)) {
            return false;
        }

        $message = sprintf(__d('baser', '受信データ NO「%s」 を削除しました。'), $id);
        $this->MailMessage->saveDbLog($message);
        return true;
    }

    /**
     * [ADMIN] 受信メール削除
     *
     * @param int $mailContentId
     * @param int $messageId
     * @return void
     */
    public function admin_delete($mailContentId, $messageId)
    {
        $this->_checkSubmitToken();
        if (!$mailContentId || !$messageId) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->notFound();
        }
        if (!$this->MailMessage->delete($messageId)) {
            $this->BcMessage->setError(
                __d('baser', 'データベース処理中にエラーが発生しました。')
            );
        } else {
            $this->BcMessage->setSuccess(
                sprintf(
                    __d('baser', '%s への受信データ NO「%s」 を削除しました。'),
                    $this->mailContent['Content']['title'],
                    $messageId
                )
            );
        }
        $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * メールフォームに添付したファイルを開く
     */
    public function admin_attachment()
    {
        $args = func_get_args();
        unset($args[0]);
        $file = implode('/', $args);
        $settings = $this->MailMessage->getBehavior('BcUpload')->BcUpload['MailMessage']->settings;
        $filePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $file;
        $ext = decodeContent(null, $file);
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
}
