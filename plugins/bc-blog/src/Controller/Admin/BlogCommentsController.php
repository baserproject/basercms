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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ブログコメントコントローラー
 *
 * @package Blog.Controller
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 */
class BlogCommentsController extends BlogAdminAppController
{

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = [
        'BcAuth',
        'Cookie',
        'BcAuthConfigure',
        'RequestHandler',
        'BcEmail',
        'Security',
        'BcCaptcha',
        'BcContents' => ['type' => 'BcBlog.BlogContent']
    ];

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->BcAuth->allow('add', 'captcha', 'smartphone_add', 'smartphone_captcha', 'get_token');

        if (BcUtil::isAdminSystem()) {
            $this->subMenuElements = ['blog_posts'];
            $this->request = $this->request->withParam('Content', $this->BcContents->getContent(
                $this->request->getParam('pass.0')
            )['Content']);
            $this->Security->enabled = true;
            $this->Security->requireAuth('add');
        }

        if (!empty($this->getRequest->getParams('pass.1'))) {
            $dbDatas = $this->BlogPost->find(
                'first',
                [
                    'conditions' => ['BlogPost.id' => $this->getRequest->getParams('pass.1')]
                ]
            );
            if (!$dbDatas) {
                $this->notFound();
            }
            $this->blogPost = ['BlogPost' => $dbDatas['BlogPost']];
            $this->blogContent = ['BlogContent' => $dbDatas['BlogContent']];
        } elseif (!empty($this->getRequest->getParams('pass.0'))) {
            if (!in_array($this->request->action, ['captcha', 'smartphone_captcha', 'get_token'])) {
                $dbDatas = $this->BlogPost->BlogContent->find(
                    'first',
                    [
                        'conditions' => [
                            'BlogContent.id' => $this->getRequest->getParams('pass.0')
                        ]
                    ]
                );
                $this->blogContent = ['BlogContent' => $dbDatas['BlogContent']];
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
        if (!empty($this->blogContent)) {
            $this->set('blogContent', $this->blogContent);
        }
    }

    /**
     * [ADMIN] ブログを一覧表示する
     *
     * @return void
     */
    public function admin_index($blogContentId, $blogPostId = null)
    {
        if (!$blogContentId || empty($this->blogContent['BlogContent'])) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect('/admin');
        }

        /* 検索条件 */
        if ($blogPostId) {
            $conditions['BlogComment.blog_post_id'] = $blogPostId;
            $this->pageTitle = sprintf(
                __d('baser', '%s｜コメント一覧'),
                $this->blogPost['BlogPost']['name']
            );
        } else {
            $conditions['BlogComment.blog_content_id'] = $blogContentId;
            $this->pageTitle = sprintf(
                __d('baser', '%s｜コメント一覧'),
                $this->request->getAttribute('currentContent')->title
            );
        }

        /* 画面情報設定 */
        $default = [
            'named' => [
                'num' => $this->siteConfigs['admin_list_num']
            ]
        ];
        $this->setViewConditions(
            'BlogPost',
            ['group' => $blogContentId, 'default' => $default]
        );

        // データを取得
        $this->paginate = [
            'conditions' => $conditions,
            'fields' => [],
            'order' => 'BlogComment.created DESC',
            'limit' => $this->passedArgs['num']
        ];

        $dbDatas = $this->paginate('BlogComment');
        $this->set('dbDatas', $dbDatas);
        $this->setHelp('blog_comments_index');
    }

    /**
     * [ADMIN] 一括削除
     *
     * @param int $blogContentId
     * @param int $blogPostId
     * @param int $id
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
     * [ADMIN] 削除処理(ajax)
     *
     * @param int $blogContentId
     * @param int $blogPostId
     * @param int $id
     * @return void
     */
    public function admin_ajax_delete($blogContentId, $blogPostId, $id = null)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        if (!$this->_del($id)) {
            exit();
        }
        exit(true);
    }

    /**
     * 削除処理
     *
     * @param int $blogContentId
     * @param int $blogPostId
     * @param int $id
     * @return bool
     */
    protected function _del($id = null)
    {
        /* 削除処理 */
        if (!$this->BlogComment->delete($id)) {
            return false;
        }

        $this->BlogComment->saveDbLog(
            sprintf(
                __d('baser', '記事「%s」へのコメントを削除しました。'),
                $this->blogPost['BlogPost']['name'] ?? $this->request->getAttribute('currentContent')->title
            )
        );
        return true;
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param string $blogContentId
     * @param string $blogPostId beforeFilterで利用
     * @param string $blogCommentId
     * @return void
     */
    public function admin_ajax_unpublish($blogContentId, $blogPostId, $id)
    {
        $this->_checkSubmitToken();

        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        if (!$this->_changeStatus($id, false)) {
            $this->ajaxError(500, $this->BlogComment->validationErrors);
            exit();
        }

        clearViewCache();
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
    public function admin_ajax_publish($blogContentId, $blogPostId, $id)
    {
        $this->_checkSubmitToken();

        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        if (!$this->_changeStatus($id, true)) {
            $this->ajaxError(500, $this->BlogComment->validationErrors);
            exit();
        }

        clearViewCache();
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
        if ($ids) {
            foreach ($ids as $id) {
                $this->_changeStatus($id, true);
            }
        }
        clearViewCache();
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
        if ($ids) {
            foreach ($ids as $id) {
                $this->_changeStatus($id, false);
            }
        }
        clearViewCache();
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
        $data = $this->BlogComment->find(
            'first',
            [
                'conditions' => [
                    'BlogComment.id' => $id
                ],
                'recursive' => -1
            ]
        );
        $data['BlogComment']['status'] = $status;
        $this->BlogComment->set($data);

        if (!$this->BlogComment->save()) {
            return false;
        }

        $statusTexts = [0 => __d('baser', '非公開状態'), 1 => __d('baser', '公開状態')];
        $this->BlogComment->saveDbLog(
            sprintf(
                __d('baser', '記事「%s」へのコメントを %s に設定しました。'),
                $this->blogPost['BlogPost']['name'] ?? $this->request->getAttribute('currentContent')->title,
                $statusTexts[$status]
            )
        );
        return true;
    }

    /**
     * [AJAX] ブログコメントを登録する
     *
     * @param string $blogContentId
     * @param string $blogPostId
     * @return void | bool
     */
    public function add($blogContentId, $blogPostId)
    {
        Configure::write('debug', 0);

        if (!$this->request->getData() || !$blogContentId || !$blogPostId) {
            $this->notFound();
            return;
        }

        if (empty($this->blogContent)) {
            $this->notFound();
            return;
        }

        if (!$this->blogContent['BlogContent']['comment_use']) {
            $this->notFound();
            return;
        }

        // 画像認証を行う
        $captchaResult = true;
        if ($this->blogContent['BlogContent']['auth_captcha']) {
            $captchaResult = $this->BcCaptcha->check(
                $this->request->getData('BlogComment.auth_captcha'),
                $this->request->getData('BlogComment.captcha_id')
            );
            if (!$captchaResult) {
                $this->set('dbData', false);
                return false;
            }
            $this->request = $this->request->withData('BlogComment.auth_captcha', null);
        }

        $result = $this->BlogComment->add(
            $this->request->getData(),
            $blogContentId,
            $blogPostId,
            $this->blogContent['BlogContent']['comment_approve']
        );
        if (!$result || !$captchaResult) {
            $this->set('dbData', false);
            return;
        }

        $content = $this->BlogPost->BlogContent->Content->findByType(
            'BcBlog.BlogContent',
            $this->blogContent['BlogContent']['id']
        );
        $this->request = $this->request->withData('Content',  $content['Content']);
        $this->_sendCommentAdmin(
            $blogPostId,
            $this->request->getData()
        );
        // コメント承認機能を利用していない場合は、公開されているコメント投稿者にアラートを送信
        if (!$this->blogContent['BlogContent']['comment_approve']) {
            $this->_sendCommentContributor(
                $blogPostId,
                $this->request->getData()
            );
        }
        $this->set('dbData', $result['BlogComment']);
    }

    /**
     * [AJAX] ブログコメントを登録する
     *
     * @param string $blogContentId
     * @param string $blogPostId
     * @return void
     */
    public function smartphone_add($blogContentId, $blogPostId)
    {
        $this->setAction('add', $blogContentId, $blogPostId);
    }

    /**
     * 認証用のキャプチャ画像を表示する
     *
     * @return void
     */
    public function captcha($token = null)
    {
        $this->BcCaptcha->render($token);
        exit();
    }

    /**
     * [SMARTPHONE] 認証用のキャプチャ画像を表示する
     *
     * @return void
     */
    public function smartphone_captcha($token = null)
    {
        $this->BcCaptcha->render($token);
        exit();
    }

    /**
     * コメント送信用にAjax経由でトークンを取得するアクション
     */
    public function get_token()
    {
        $this->_checkReferer();
        $this->autoRender = false;
        return $this->getToken();
    }
}
