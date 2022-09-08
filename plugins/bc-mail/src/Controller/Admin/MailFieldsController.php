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

use Cake\Event\EventInterface;

/**
 * メールフィールドコントローラー
 *
 * @package Mail.Controller
 * @property BcContentsComponent $BcContents
 * @property MailField $MailField
 * @property MailContent $MailContent
 * @property MailMessage $MailMessage
 */
class MailFieldsController extends MailAppController
{

    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'MailFields';

    /**
     * モデル
     *
     * @var array
     */
    public $uses = ['BcMail.MailField', 'BcMail.MailContent', 'BcMail.MailMessage'];

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents' => ['type' => 'BcMail.MailContent']];

    /**
     * サブメニューエレメント
     *
     * @var string
     */
    public $subMenuElements = [];

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->_checkEnv();
        $this->MailContent->recursive = -1;
        $mailContentId = $this->request->param('pass.0');
        $this->mailContent = $this->MailContent->read(null, $mailContentId);
        $this->request->param('Content', $this->BcContents->getContent($mailContentId)['Content']);
        if ($this->request->param('Content.status')) {
            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $site = $sites->findById($this->request->param('Content.site_id'))->first();
            $this->set(
                'publishLink',
                $this->Content->getUrl(
                    $this->request->param('Content.url'),
                    true,
                    $site->useSubDomain
                )
            );
        }
    }

    /**
     * プラグインの環境をチェックする
     */
    protected function _checkEnv()
    {
        $savePath = WWW_ROOT . 'files' . DS . "mail" . DS . 'limited';
        if (!is_dir($savePath)) {
            $Folder = new Folder();
            $Folder->create($savePath, 0777);
            if (!is_dir($savePath)) {
                $this->BcMessage->setError(
                    'ファイルフィールドを利用している場合、フォームより送信したファイルフィールドのデータは公開された状態となっています。URLを直接閲覧すると参照できてしまいます。参照されないようにするためには、' . WWW_ROOT . 'files/mail/ に書き込み権限を与えてください。'
                );
            }
            $File = new File($savePath . DS . '.htaccess');
            $htaccess = "Order allow,deny\nDeny from all";
            $File->write($htaccess);
            $File->close();
            if (!file_exists($savePath . DS . '.htaccess')) {
                $this->BcMessage->setError(
                    'ファイルフィールドを利用している場合、フォームより送信したファイルフィールドのデータは公開された状態となっています。URLを直接閲覧すると参照できてしまいます。参照されないようにするためには、' . WWW_ROOT . 'files/mail/limited/ に書き込み権限を与えてください。'
                );
            }
        }
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
     * [ADMIN] メールフィールド一覧
     *
     * @param int $mailContentId
     * @return void
     */
    public function admin_index($mailContentId)
    {
        if (!$mailContentId || !$this->mailContent) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(['controller' => 'mail_contents', 'action' => 'index']);
        }

        $conditions = $this->_createAdminIndexConditions($mailContentId);
        $this->set(
            'datas',
            $this->MailField->find(
                'all',
                ['conditions' => $conditions, 'order' => 'MailField.sort']
            )
        );

        $this->_setAdminIndexViewData();

        if ($this->request->is('ajax') || !empty($this->query['ajax'])) {
            $this->render('ajax_index');
            return;
        }
        $this->subMenuElements = ['mail_fields'];
        $this->pageTitle = sprintf(
            __d('baser', '%s｜メールフィールド一覧'),
            $this->request->param('Content.title')
        );
        $this->setHelp('mail_fields_index');
    }

    /**
     * 一覧の表示用データをセットする
     *
     * @return void
     */
    protected function _setAdminIndexViewData()
    {
        /* セッション処理 */
        if ($this->getRequest()->getParam('named.sortmode')) {
            $this->Session->write(
                'SortMode.MailField',
                $this->getRequest()->getParam('named.sortmode')
            );
        }

        /* 並び替えモード */
        if (!$this->Session->check('SortMode.MailField')) {
            $this->set('sortmode', 0);
        } else {
            $this->set('sortmode', $this->Session->read('SortMode.MailField'));
        }
    }

    /**
     * [ADMIN] メールフィールド追加
     *
     * @param int $mailContentId
     * @return void
     */
    public function admin_add($mailContentId)
    {
        if (!$mailContentId || !$this->mailContent) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(['controller' => 'mail_contents', 'action' => 'index']);
        }

        if (!$this->request->getData()) {
            $this->request = $this->request->withParsedBody($this->_getDefaultValue());
        } else {

            /* 登録処理 */
            $data = $this->request->getData();
            if (is_array($data['MailField']['valid_ex'])) {
                $data['MailField']['valid_ex'] = implode(',', $data['MailField']['valid_ex']);
            }
            $data['MailField']['mail_content_id'] = $mailContentId;
            $data['MailField']['no'] = $this->MailField->getMax(
                'no',
                ['MailField.mail_content_id' => $mailContentId]
            ) + 1;
            $data['MailField']['sort'] = $this->MailField->getMax('sort') + 1;
            $data['MailField']['source'] = $this->MailField->formatSource($data['MailField']['source']);
            $this->MailField->create($data);
            if ($this->MailField->validates()) {
                $ret = $this->MailMessage->addMessageField(
                    $this->mailContent['MailContent']['id'],
                    $data['MailField']['field_name']
                );
                if ($ret) {
                    // データを保存
                    if ($this->MailField->save(null, false)) {
                        $this->BcMessage->setSuccess(
                            sprintf(
                                __d('baser', '新規メールフィールド「%s」を追加しました。'),
                                $data['MailField']['name']
                            )
                        );
                        $this->redirect(
                            ['controller' => 'mail_fields', 'action' => 'index', $mailContentId]
                        );
                        return;
                    }
                    $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
                } else {
                    $this->BcMessage->setError(
                        __d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。')
                    );
                }
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }

        $this->subMenuElements = ['mail_fields'];
        $this->pageTitle = sprintf(
            __d('baser', '%s｜新規メールフィールド登録'),
            $this->request->param('Content.title')
        );
        $this->setHelp('mail_fields_form');
        $this->render('form');
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param int $mailContentId
     * @param int $id
     * @return void
     */
    public function admin_edit($mailContentId, $id)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }

        if (empty($this->request->getData())) {
            $data = $this->MailField->read(null, $id);
            $data['MailField']['valid_ex'] = explode(',', $data['MailField']['valid_ex']);
            $this->request = $this->request->withParsedBody($data);
        } else {
            $old = $this->MailField->read(null, $id);
            $data = $this->request->getData();
            if (is_array($data['MailField']['valid_ex'])) {
                $data['MailField']['valid_ex'] = implode(',', $data['MailField']['valid_ex']);
            }
            $data['MailField']['source'] = $this->MailField->formatSource($data['MailField']['source']);

            $this->MailField->set($data);
            if ($this->MailField->validates()) {
                if ($old['MailField']['field_name'] != $data['MailField']['field_name']) {
                    $ret = $this->MailMessage->renameMessageField(
                        $mailContentId,
                        $old['MailField']['field_name'],
                        $data['MailField']['field_name']
                    );
                } else {
                    $ret = true;
                }
                if ($ret) {
                    /* 更新処理 */
                    if ($this->MailField->save(null, false)) {
                        $this->BcMessage->setSuccess(
                            sprintf(__d('baser', 'メールフィールド「%s」を更新しました。'), $data['MailField']['name'])
                        );
                        $this->redirect(['action' => 'index', $mailContentId]);
                        return;
                    }
                    $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
                } else {
                    $this->BcMessage->setError(
                        __d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。')
                    );
                }
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }

        /* 表示設定 */
        $this->subMenuElements = ['mail_fields'];
        $this->pageTitle = sprintf(
            __d('baser', '%s｜メールフィールド編集'),
            $this->request->param('Content.title')
        );
        $this->setHelp('mail_fields_form');
        $this->render('form');
    }

    /**
     * [ADMIN] 削除処理（Ajax）
     *
     * @param int $mailContentId
     * @param int $id
     * @return void
     */
    public function admin_ajax_delete($mailContentId, $id = null)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        // メッセージ用にデータを取得
        $data = $this->MailField->read(null, $id);
        $field = Hash::get($data, 'MailField');

        /* 削除処理 */
        if (!$field || !$this->MailMessage->delMessageField($mailContentId, $field['field_name'])) {
            $this->ajaxError(
                500,
                __d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。')
            );
            exit;
        }

        if (!$this->MailField->delete($id)) {
            exit;
        }

        $this->MailField->saveDbLog(
            sprintf(
                __d('baser', 'メールフィールド「%s」 を削除しました。'),
                $field['name']
            )
        );
        exit(true);
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param int $mailContentId
     * @param int $id
     * @return void
     */
    public function admin_delete($mailContentId, $id = null)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'admin_index']);
        }

        // メッセージ用にデータを取得
        $mailField = $this->MailField->read(null, $id);

        /* 削除処理 */
        if ($this->MailMessage->delMessageField($mailContentId, $mailField['MailField']['field_name'])) {
            if ($this->MailField->delete($id)) {
                $this->BcMessage->setSuccess(
                    sprintf(
                        __d('baser', 'メールフィールド「%s」を削除しました。'),
                        $mailField['MailField']['name']
                    )
                );
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        } else {
            $this->BcMessage->setError(
                __d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。')
            );
        }

        $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * 一括削除
     *
     * @param array $ids
     * @return boolean
     */
    protected function _batch_del($ids)
    {
        if ($ids) {
            foreach ($ids as $id) {
                // メッセージ用にデータを取得
                $data = $this->MailField->read(null, $id);
                $field = $data['MailField'];
                /* 削除処理 */
                if (!$this->MailMessage->delMessageField($field['mail_content_id'], $field['field_name'])) {
                    continue;
                }
                if (!$this->MailField->delete($id)) {
                    continue;
                }
                $this->MailField->saveDbLog(
                    sprintf(
                        __d('baser', 'メールフィールド「%s」 を削除しました。'),
                        $field['name']
                    )
                );
            }
        }

        return true;
    }

    /**
     * フォームの初期値を取得する
     *
     * @return array
     */
    protected function _getDefaultValue()
    {
        $data['MailField']['type'] = 'text';
        $data['MailField']['use_field'] = 1;
        $data['MailField']['no_send'] = 0;
        return $data;
    }

    /**
     * フィールドデータをコピーする
     *
     * @param int $mailContentId
     * @param int $Id
     * @return void
     */
    public function admin_ajax_copy($mailContentId, $id)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id || !$mailContentId) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        $result = $this->MailField->copy($id);
        if (!$result) {
            $this->ajaxError(
                500,
                __d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。')
            );
            return;
        }
        $this->MailMessage->construction($mailContentId);
        $this->set('data', $result);
    }

    /**
     * メッセージCSVファイルをダウンロードする
     *
     * @param int $mailContentId
     * @return void
     */
    public function admin_download_csv($mailContentId)
    {
        $mailContentId = (int)$mailContentId;
        if (!$mailContentId || !$this->mailContent || !is_int($mailContentId)) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(['controller' => 'mail_contents', 'action' => 'index']);
        }
        $this->MailMessage->alias = 'MailMessage' . $mailContentId;
        $this->MailMessage->schema(true);
        $this->MailMessage->cacheSources = false;
        $this->MailMessage->setUseTable($mailContentId);
        $messages = $this->MailMessage->convertMessageToCsv(
            $mailContentId,
            $this->MailMessage->find(
                'all',
                ['order' => $this->MailMessage->alias . '.created DESC']
            )
        );
        $this->set('encoding', $this->request->getQuery('encoding'));
        $this->set('messages', $messages);
        $this->set('contentName', $this->request->param('Content.name'));
    }

    /**
     * 並び替えを更新する [AJAX]
     *
     * @param int $mailContentId
     * @return bool|void
     * @access    public
     */
    public function admin_ajax_update_sort($mailContentId)
    {
        if (!$mailContentId) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            return;
        }

        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            return;
        }

        $sorted = $this->MailField->changeSort(
            $this->request->getData('id'),
            $this->request->getData('offset'),
            $this->_createAdminIndexConditions($mailContentId)
        );
        if (!$sorted) {
            $this->ajaxError(500, $this->MailField->validationErrors);
            return;
        }
        exit(true);
    }

    /**
     * 管理画面ページ一覧の検索条件を取得する
     *
     * @param integer $mailContentId
     * @return array
     */
    protected function _createAdminIndexConditions($mailContentId)
    {
        return ['MailField.mail_content_id' => $mailContentId];
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param string $blogContentId
     * @param string $blogPostId beforeFilterで利用
     * @param string $blogCommentId
     * @return void
     */
    public function admin_ajax_unpublish($mailContentId, $id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            return;
        }
        if (!$this->_changeStatus($id, false)) {
            $this->ajaxError(500, $this->MailField->validationErrors);
            return;
        }
        exit(true);
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     *
     * @param string $blogContentId
     * @param string $blogPostId beforeFilterで利用
     * @param string $blogCommentId
     * @return void
     */
    public function admin_ajax_publish($mailContentId, $id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            return;
        }
        if (!$this->_changeStatus($id, true)) {
            $this->ajaxError(500, $this->MailField->validationErrors);
            return;
        }
        exit(true);
    }

    /**
     * 一括公開
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_publish($ids)
    {
        if (!$ids) {
            return true;
        }
        foreach ($ids as $id) {
            $this->_changeStatus($id, true);
        }
        return true;
    }

    /**
     * 一括非公開
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_unpublish($ids)
    {
        if (!$ids) {
            return true;
        }

        foreach ($ids as $id) {
            $this->_changeStatus($id, false);
        }
        return true;
    }

    /**
     * ステータスを変更する
     *
     * @param int $id
     * @param boolean $status
     * @return boolean
     */
    protected function _changeStatus($id, $status)
    {
        $statusTexts = [0 => __d('baser', '無効'), 1 => __d('baser', '有効')];
        $data = $this->MailField->find(
            'first',
            ['conditions' => ['MailField.id' => $id], 'recursive' => -1]
        );
        $data['MailField']['use_field'] = $status;
        $this->MailField->set($data);

        if (!$this->MailField->save()) {
            return false;
        }

        $statusText = $statusTexts[$status];
        $this->MailField->saveDbLog(
            sprintf(
                __d('baser', 'メールフィールド「%s」 の設定を %s に変更しました。'),
                $data['MailField']['name'],
                $statusText
            )
        );
        return true;
    }
}
