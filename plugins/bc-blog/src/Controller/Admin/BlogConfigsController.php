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

namespace BcBlog\Controller\Admin;

use Cake\Event\EventInterface;

/**
 * ブログ設定コントローラー
 *
 * @package Blog.Controller
 */
class BlogConfigsController extends BlogAppController
{
    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'BlogConfigs';

    /**
     * モデル
     *
     * @var array
     */
    public $uses = [
        'User',
        'BcBlog.BlogCategory',
        'BcBlog.BlogConfig',
        'BcBlog.BlogContent'
    ];

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

    /**
     * サブメニューエレメント
     *
     * @var array
     */
    public $subMenuElements = [];

    /**
     * before_filter
     *
     * @return void
     */
    public function beforeFilter(EventInterface$event)
    {
        parent::beforeFilter($event);
        if ($this->params['prefix'] == 'admin') {
            $this->subMenuElements = ['blog_common'];
        }
    }

    /**
     * [ADMIN] サイト基本設定
     *
     * @return void
     */
    //	public function admin_form() {
    //		if (empty($this->request->getData())) {
    //			$this->request = $this->request->withParsedBody($this->BlogConfig->read(null, 1));
    //			$blogContentList = $this->BlogContent->find("list");
    //			$this->set('blogContentList', $blogContentList);
    //			$userList = $this->User->find("list");
    //			$this->set('userList', $userList);
    //		} else {
    //
    //			/* 更新処理 */
    //			if ($this->BlogConfig->save($this->request->getData())) {
    //				$this->BcMessage->setSuccess('ブログ設定を保存しました。');
    //				$this->redirect(array('action' => 'form'));
    //			} else {
    //				$this->BcMessage->setError('入力エラーです。内容を修正してください。');
    //			}
    //		}
    //
    //		/* 表示設定 */
    //		$this->setTitle('ブログ設定');
    //	}

}
