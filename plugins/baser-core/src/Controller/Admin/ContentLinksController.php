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

namespace BaserCore\Controller\Admin;

use Cake\Event\EventInterface;

/**
 * Class ContentLinksController
 *
 * リンク コントローラー
 *
 * @package Baser.Controller
 */
class ContentLinksController extends BcAdminAppController
{

    /**
     * コンポーネント
     * @var array
     */
    public $components = ['Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => ['useForm' => true]];

    /**
     * Before Filter
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->BcAuth->allow('view');
    }

    /**
     * コンテンツを登録する
     *
     * @return void
     */
    public function admin_add()
    {
        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $data = $this->ContentLink->save($this->request->getData());
        if (!$data) {
            $this->ajaxError(500, __d('baser', '保存中にエラーが発生しました。'));
            exit;
        }

        $this->BcMessage->setSuccess(sprintf(__d('baser', 'リンク「%s」を追加しました。'), $this->request->getData('Content.title')), true, false);
        echo json_encode($data['Content']);
        exit();
    }

    /**
     * コンテンツを更新する
     *
     * @return void
     */
    public function admin_edit($entityId)
    {
        $this->setTitle(__d('baser', 'リンク編集'));
        if ($this->request->is(['post', 'put'])) {
            if ($this->ContentLink->isOverPostSize()) {
                $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit', $entityId]);
            }
            if ($this->ContentLink->save($this->request->getData())) {
                clearViewCache();
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'リンク「%s」を更新しました。'), $this->request->getData('Content.title')));
                $this->redirect([
                    'plugin' => '',
                    'controller' => 'content_links',
                    'action' => 'edit',
                    $entityId
                ]);
            } else {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        } else {
            // TODO ucmitz 一旦コメントアウト
//            $this->request = $this->request->withParsedBody($this->ContentLink->read(null, $entityId));
            if (!$this->request->getData()) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
            }
        }
    }

    /**
     * コンテンツを削除する
     *
     * @return bool
     */
    public function admin_delete()
    {
        if (empty($this->request->getData('entityId'))) {
            return false;
        }
        if ($this->ContentLink->delete($this->request->getData('entityId'))) {
            return true;
        }
        return false;
    }

    /**
     * コンテンツを表示する
     *
     * @return void
     */
    public function view()
    {
        if (empty($this->request->getParam('entityId'))) {
            $this->notFound();
        }
        $data = $this->ContentLink->find('first', ['conditions' => ['ContentLink.id' => $this->request->getParam('entityId')]]);
        if ($data) {
            $this->set(compact('data'));
        } else {
            $this->notFound();
        }
    }

}
