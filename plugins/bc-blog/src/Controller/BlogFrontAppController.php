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

namespace BcBlog\Controller;

use BaserCore\Controller\BcFrontAppController;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * ブログコントローラー基底クラス
 */
class BlogFrontAppController extends BcFrontAppController
{

    /**
     * Before Render
     * @param EventInterface $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        if (isset($this->RequestHandler) && $this->RequestHandler->prefers('json')) return;
        $this->viewBuilder()->setClassName('BcBlog.BlogFrontApp');
    }

    /**
     * コメントを管理者メールへメール送信する
     *
     * @param int $postId
     * @param array $data
     * @return boolean
     */
    protected function _sendCommentAdmin($postId, $data)
    {
        if (!$postId || !$data || empty($this->siteConfigs['email'])) {
            return false;
        }

        $data = array_merge(
            $data,
            $this->BlogPost->find(
                'first',
                [
                    'conditions' => ['BlogPost.id' => $postId],
                    'recursive' => 0
                ]
            )
        );
        $data['SiteConfig'] = $this->siteConfigs;
        return $this->sendMail(
            $this->siteConfigs['email'],
            sprintf(
                __d('baser', '【%s】コメントを受け付けました'),
                $this->siteConfigs['name']
            ),
            $data,
            [
                'template' => 'BcBlog.blog_comment_admin',
                'agentTemplate' => false
            ]
        );
    }

    /**
     * コメント投稿者にアラートメールを送信する
     *
     * @param int $postId
     * @param array $data
     * @return boolean
     */
    protected function _sendCommentContributor($postId, $data)
    {
        if (!$postId || !$data || empty($this->siteConfigs['email'])) {
            return false;
        }

        $_data = $this->BlogPost->find(
            'first', [
                'conditions' => [
                    'BlogPost.id' => $postId
                ],
                'recursive' => 1
            ]
        );

        // 公開されているコメントがない場合は true を返して終了
        if (empty($_data['BlogComment'])) {
            return true;
        }

        $blogComments = $_data['BlogComment'];
        unset($_data['BlogComment']);
        $data = array_merge($_data, $data);

        $data['SiteConfig'] = $this->siteConfigs;
        $sended = [];
        foreach($blogComments as $blogComment) {
            if (!$blogComment['email'] || !$blogComment['status']) {
                $sended[] = $blogComment['email'];
                continue;
            }
            if (in_array($blogComment['email'], $sended)) {
                $sended[] = $blogComment['email'];
                continue;
            }
            if ($blogComment['email'] === $data['BlogComment']['email']) {
                $sended[] = $blogComment['email'];
                continue;
            }
            $result = $this->sendMail(
                $blogComment['email'],
                sprintf(__('【%s】コメントが投稿されました'), $this->siteConfigs['name']),
                $data,
                [
                    'template' => 'BcBlog.blog_comment_contributor',
                    'agentTemplate' => false
                ]
            );
            $sended[] = $blogComment['email'];
        }

        return $result ?? true;
    }

}
