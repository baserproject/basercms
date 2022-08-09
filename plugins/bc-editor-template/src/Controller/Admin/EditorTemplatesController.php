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

namespace BcEditorTemplate\Controller\Admin;

use Cake\Event\EventInterface;
use BaserCore\Controller\Admin\BcAdminAppController;

/**
 * Class EditorTemplatesController
 *
 * エディタテンプレートコントローラー
 *
 * エディタテンプレートの管理を行う
 *
 * @package Baser.Controller
 */
class EditorTemplatesController extends BcAdminAppController
{

    /**
     * コントローラー名
     *
     * @var string
     */
    public $name = 'EditorTemplates';

    /**
     * サブメニュー
     *
     * @var array
     */
    public $subMenuElements = ['site_configs', 'editor_templates'];

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        return // TODO : 一時措置
        parent::beforeFilter();
        if (BcSiteConfig::get('editor') && BcSiteConfig::get('editor') !== 'none') {
            $this->helpers[] = BcSiteConfig::get('editor');
        }
    }

    /**
     * [ADMIN] 一覧
     */
    public function admin_index()
    {
        $this->setTitle(__d('baser', 'エディタテンプレート一覧'));
        $this->setHelp('editor_templates_index');
        $this->set('datas', $this->EditorTemplate->find('all'));
    }

    /**
     * [ADMIN] 新規登録
     */
    public function admin_add()
    {
        $this->setTitle(__d('baser', 'エディタテンプレート新規登録'));
        $this->setHelp('editor_templates_form');

        if (!$this->request->is(['post', 'put'])) {
            $this->render('form');
            return;
        }

        if ($this->EditorTemplate->isOverPostSize()) {
            $this->BcMessage->setError(
                __d(
                    'baser',
                    '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                    ini_get('post_max_size')
                )
            );
            $this->redirect(['action' => 'add']);
        }
        $this->EditorTemplate->create($this->request->data);
        $result = $this->EditorTemplate->save();
        if (!$result) {
            $this->BcMessage->setError(__d('baser', '保存中にエラーが発生しました。'));
            $this->render('form');
            return;
        }

        // EVENT EditorTemplates.afterAdd
        $this->dispatchLayerEvent('afterAdd', [
            'data' => $result
        ]);
        $this->BcMessage->setInfo(__d('baser', '保存完了'));
        $this->redirect(['action' => 'index']);
//		$this->render('form');
    }

    /**
     * [ADMIN] 編集
     *
     * @param int $id
     */
    public function admin_edit($id)
    {
        $this->setTitle(__d('baser', 'エディタテンプレート編集'));
        $this->setHelp('editor_templates_form');

        if (!$this->request->is(['post', 'put'])) {
            $this->request->data = $this->EditorTemplate->read(null, $id);
            $this->render('form');
            return;
        }

        if ($this->EditorTemplate->isOverPostSize()) {
            $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
            $this->redirect(['action' => 'edit', $id]);
        }
        $this->EditorTemplate->set($this->request->data);
        $result = $this->EditorTemplate->save();
        if (!$result) {
            $this->BcMessage->setError(__d('baser', '保存中にエラーが発生しました。'));
            $this->render('form');
            return;
        }

        // EVENT EditorTemplates.afterEdit
        $this->dispatchLayerEvent('afterEdit', [
            'data' => $result
        ]);
        $this->BcMessage->setInfo(__d('baser', '保存完了'));
        $this->redirect(['action' => 'index']);
//		$this->render('form');
    }

    /**
     * [ADMIN] 削除
     *
     * @param int $id
     */
    public function admin_delete($id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $data = $this->EditorTemplate->read(null, $id);
        if ($this->EditorTemplate->delete($id)) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'エディタテンプレート「%s」を削除しました。'), $data['EditorTemplate']['name']));
        } else {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * [ADMIN AJAX] 削除
     * @param int $id
     */
    public function admin_ajax_delete($id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $data = $this->EditorTemplate->read(null, $id);
        if ($this->EditorTemplate->delete($id)) {
            $this->EditorTemplate->saveDbLog(sprintf(__d('baser', 'エディタテンプレート「%s」を削除しました。'), $data['EditorTemplate']['name']));
            exit(true);
        }
        exit();
    }

    /**
     * [ADMIN] CKEditor用テンプレート用のjavascriptを出力する
     */
    public function admin_js()
    {
        header('Content-Type: text/javascript; name="editor_templates.js"');
        $this->layout = 'empty';
        $this->set('templates', $this->EditorTemplate->find('all'));
    }

}
