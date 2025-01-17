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

namespace BcBlog\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Model\Entity\BlogComment;
use BcBlog\Model\Entity\BlogContent;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Model\Table\BlogCommentsTable;
use Cake\Datasource\EntityInterface;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * BlogCommentsService
 *
 * @property BlogCommentsTable $BlogComments
 */
class BlogCommentsService implements BlogCommentsServiceInterface
{

    /**
     * Trait
     */
    use MailerAwareTrait;
    use BcContainerTrait;

    /**
     * BlogComments Table
     * @var BlogCommentsTable|Table
     */
    public BlogCommentsTable|Table $BlogComments;

    /**
     * ブログコメントを初期化する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogComments = TableRegistry::getTableLocator()->get('BcBlog.BlogComments');
    }

    /**
     * ブログコメント一覧データを取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams)
    {
        $options = array_merge([
            'blog_post_id' => null,
            'status' => '',
            'contain' => ['BlogPosts' => ['BlogContents' => ['Contents']]]
        ], $queryParams);

        if (is_null($options['contain'])) {
            $fields = $this->BlogComments->getSchema()->columns();
            $query = $this->BlogComments->find()
                ->contain(['BlogPosts' => ['BlogContents' => ['Contents']]])
                ->select($fields);
        } else {
            $query = $this->BlogComments->find()->contain($options['contain']);
        }

        if (!empty($queryParams['limit'])) {
            $query = $query->limit($queryParams['limit']);
        }
        if (!empty($options['blog_post_id'])) {
            $query = $query->where(['BlogComments.blog_post_id' => $options['blog_post_id']]);
        }
        if (!empty($options['blog_content_id'])) {
            $query = $query->where(['BlogComments.blog_content_id' => $options['blog_content_id']]);
        }
        if ($options['status'] === 'publish') {
            $query->where($this->BlogComments->BlogContents->Contents->getConditionAllowPublish());
        }

        return $query;
    }

    /**
     * ブログコメントの単一データを取得する
     *
     * @param int $id
     * @param array $queryParams
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $queryParams = [])
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);

        $conditions = [];
        if ($queryParams['status'] === 'publish') {
            $conditions = $this->BlogComments->BlogContents->Contents->getConditionAllowPublish();
            $conditions = array_merge($conditions, ['BlogComments.status' => true]);
        }

        return $this->BlogComments->get($id,
            contain: ['BlogPosts' => ['BlogContents' => ['Contents']]],
            conditions: $conditions
        );
    }

    /**
     * ブログコメントの初期値を取得する
     *
     * @return EntityInterface 初期値データ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew()
    {
        return $this->BlogComments->newEntity([
            'name' => 'NO NAME'
        ]);
    }

    /**
     * ブログコメントを登録する
     *
     * @param int $blogContentId
     * @param int $blogPostId
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @unitTest
     */
    public function add(int $blogContentId, int $blogPostId, array $postData)
    {
        $postData = array_merge([
            'url' => '',
            'email' => '',
            'auth_captcha' => null,
            'captcha_id' => null
        ], $postData);

        /** @var BlogContent $blogContent */
        $blogContent = $this->getBlogContent($blogContentId);
        if(!$blogContent->comment_use) throw new BcException(__d('baser_core', 'コメント機能は無効になっています。'));

        // 画像認証を行う
//        if ($blogContent->auth_captcha) {
//            if (!$this->BcCaptcha->check($postData['auth_captcha'], $postData['captcha_id'])) {
//                throw new BcException(__d('baser_core', '画像認証に失敗しました。'));
//            }
//        }

        // Modelのバリデートに引っかからないための対処
        $postData['url'] = str_replace('&#45;', '-', $postData['url']);
        $postData['email'] = str_replace('&#45;', '-', $postData['email']);
        $postData['blog_post_id'] = $blogPostId;
        $postData['blog_content_id'] = $blogContentId;
        if ($blogContent->comment_approve) {
            $postData['status'] = false;
        } else {
            $postData['status'] = true;
        }
        $postData['no'] = $this->BlogComments->getMax('no', ['blog_content_id' => $blogContentId]) + 1;

        $entity = $this->BlogComments->patchEntity($this->getNew(), $postData);
        return $this->BlogComments->saveOrFail($entity);
    }

    /**
     * ブログコンテンツを取得する
     *
     * @param $blogContentId
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getBlogContent($blogContentId)
    {
        return $this->BlogComments->BlogContents->get($blogContentId);
    }

    /**
     * 管理者にコメント通知メールを送信する
     *
     * @param EntityInterface|BlogComment $entity
     * @return void
     * @throws \Throwable
     * @checked
     * @noTodo
     * @unitTest
     */
    public function sendCommentToAdmin(EntityInterface $entity)
    {
        /** @var BlogPostsService $blogPostsService */
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

        /** @var BlogPost $blogPost */
        $blogPost = $blogPostsService->get($entity->blog_post_id);

        /** @var ContentsService $contentService */
        $contentService = $this->getService(ContentsServiceInterface::class);

        $content = $blogPost->blog_content->content;
        $site = $content->site;
        $postUrl = $contentService->getUrl($content->url .  '/archives/' . $blogPost->no, true, $site->use_subdomain);
        try {
            $this->getMailer('BcBlog.BlogComment')->send('sendCommentToAdmin', [$site->title, [
                'blogComment' => $entity,
                'blogPost' => $blogPost,
                'contentTitle' => $content->title,
                'postUrl' => $postUrl,
                'adminUrl' => Router::url([
                    'plugin' => 'BcBlog',
                    'prefix' => 'Admin',
                    'controller' => 'BlogComments',
                    'action' => 'index',
                    $entity->blog_content_id
                ], true)
            ]]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * コメント送信者にコメント通知メールを送信する
     *
     * @param EntityInterface|BlogComment $entity
     * @return void
     * @throws \Throwable
     * @checked
     * @noTodo
     */
    public function sendCommentToContributor(EntityInterface $entity)
    {
        /** @var BlogPostsService $blogPostsService */
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

        /** @var BlogPost $blogPost */
        $blogPost = $blogPostsService->get($entity->blog_post_id, [
            'contain' => [
                'BlogContents' => ['Contents' => ['Sites']],
                'BlogComments'
            ]
        ]);

        // 公開されているコメントがない場合は true を返して終了
        if (!$blogPost->blog_comments) return;

        $content = $blogPost->blog_content->content;
        $site = $content->site;

        /** @var ContentsService $contentService */
        $contentService = $this->getService(ContentsServiceInterface::class);
        $postUrl = $contentService->getUrl($content->url .  '/archives/' . $blogPost->no, true, $site->use_subdomain);

        $sent = [];
        foreach($blogPost->blog_comments as $blogComment) {
            if(!$blogComment->email) continue;
            if (!$blogComment->status) {
                $sent[] = $blogComment->email;
                continue;
            }
            if (in_array($blogComment->email, $sent)) continue;
            try {
                $this->getMailer('BcBlog.BlogComment')->send('sendCommentToUser', [
                    $site->title,
                    $blogComment->email,
                    [
                        'blogComment' => $entity,
                        'blogPost' => $blogPost,
                        'contentTitle' => $content->title,
                        'postUrl' => $postUrl,
                ]]);
            } catch (\Throwable $e) {
                throw $e;
            }
            $sent[] = $blogComment->email;
        }
    }

    /**
     * ブログコメントを公開状態に設定する
     *
     * @param int $id
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(int $id)
    {
        $entity = $this->get($id);
        $entity->status = true;
        return $this->BlogComments->save($entity);
    }

    /**
     * ブログコメントを非公開状態に設定する
     *
     * @param int $id
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(int $id)
    {
        $entity = $this->get($id);
        $entity->status = false;
        return $this->BlogComments->save($entity);
    }

    /**
     * ブログコメントを削除する
     *
     * @param int $id
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function delete(int $id)
    {
        $entity = $this->get($id);
        return $this->BlogComments->delete($entity);
    }

    /**
     * アップロード対象となるフィールドに格納するファイル名を、指定したフィールドの値を利用したファイル名に変更する
     *
     * ### リネーム例
     *  - 元ファイル名が、sample.png
     *  - id フィールドを利用する
     *  - id に 585 が入っている
     *  - nameformat が %08d となっている
     *
     * 結果：00000585.png
     *
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->BlogComments->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

}
