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

use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * 記事コントローラー
 *
 * @package Blog.Controller
 * @property BlogPost $BlogPost
 * @property BlogCategory $BlogCategory
 * @property BlogContent $BlogContent
 * @property BcContentsComponent $BcContents
 */
class BlogPostsController extends BlogAdminAppController
{
    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'BlogPosts';

    /**
     * モデル
     *
     * @var array
     */
    public $uses = ['BcBlog.BlogPost', 'BcBlog.BlogCategory', 'BcBlog.BlogContent'];

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = [
        'BcAuth',
        'Cookie',
        'BcAuthConfigure',
        'BcEmail',
        'BcContents' => ['type' => 'BcBlog.BlogContent']
    ];

    /**
     * サブメニューエレメント
     *
     * @var array
     */
    public $subMenuElements = [];

    /**
     * ブログコンテンツデータ
     *
     * @var array
     */
    public $blogContent;

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if ($this->request->getParam('pass.0')) {
            $content = $this->BcContents->getContent(
                $this->request->getParam('pass.0')
            );
            if (!$content) {
                $this->notFound();
            }
            $this->request = $this->request->withParam('Content',  $content['Content']);
            $this->request = $this->request->withParam('Site',  $content['Site']);
            $this->BlogContent->recursive = -1;
            $this->blogContent = $this->BlogContent->read(
                null,
                $this->request->getParam('pass.0')
            );
            $this->BlogPost->setupUpload($this->blogContent['BlogContent']['id']);
            if ($this->request->getParam('prefix') === 'Admin') {
                $this->subMenuElements = ['blog_posts'];
            }
            if (!empty($this->siteConfigs['editor']) && $this->siteConfigs['editor'] !== 'none') {
                $this->helpers[] = $this->siteConfigs['editor'];
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
        $this->set('blogContent', $this->blogContent);
    }

    /**
     * [ADMIN] 一覧表示
     *
     * @param int $blogContentId
     * @return void
     */
    public function admin_index($blogContentId)
    {
        if (!$blogContentId || !$this->blogContent) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(
                [
                    'plugin' => '',
                    'controller' => 'contents',
                    'action' => 'index'
                ]
            );
        }

        $default = ['named' => [
            'num' => $this->siteConfigs['admin_list_num'],
            'sort' => 'no',
            'direction' => 'desc',
        ]];
        $this->setViewConditions(
            'BlogPost',
            ['group' => $blogContentId, 'default' => $default]
        );

        $joins = [];
        if (!empty($this->request->getData('BlogPost.blog_tag_id'))) {
            $db = ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
            $joins = [
                [
                    'table' => $db->config['prefix'] . 'blog_posts_blog_tags',
                    'alias' => 'BlogPostsBlogTag',
                    'type' => 'inner',
                    'conditions' => ['BlogPostsBlogTag.blog_post_id = BlogPost.id']
                ],
                [
                    'table' => $db->config['prefix'] . 'blog_tags',
                    'alias' => 'BlogTag',
                    'type' => 'inner',
                    'conditions' => [
                        'BlogTag.id = BlogPostsBlogTag.blog_tag_id',
                        'BlogTag.id' => $this->request->getData('BlogPost.blog_tag_id')
                    ]
                ]
            ];
        }

        $conditions = $this->_createAdminIndexConditions(
            $blogContentId,
            $this->request->getData()
        );
        if (strpos($this->passedArgs['sort'], '.') === false) {
            $order = 'BlogPost.' . $this->passedArgs['sort'];
        }
        if ($order && $this->passedArgs['direction']) {
            $order .= ' ' . $this->passedArgs['direction'];
        }
        $options = [
            'conditions' => $conditions,
            'joins' => $joins,
            'order' => $order,
            'limit' => $this->passedArgs['num'],
            'recursive' => 2
        ];

        // EVENT BlogPosts.searchIndex
        $event = $this->dispatchEvent('searchIndex', [
            'options' => $options
        ]);
        if ($event !== false) {
            if ($event->getResult() === null || $event->getResult() === true) {
                $options = $event->getData('options');
            } else {
                $options = $event->getResult();
            }
        }

        $this->BlogPost->BlogContent->unbindModel(
            ['hasMany' => ['BlogPost', 'BlogCategory']]
        );
        $this->BlogPost->BlogCategory->unbindModel(
            ['hasMany' => ['BlogPost']]
        );
        $this->BlogPost->User->unbindModel(
            ['hasMany' => ['Favorite'], 'belongsTo' => ['UserGroup']]
        );

        $this->paginate = $options;
        $this->set('posts', $this->paginate('BlogPost'));

        $this->_setAdminIndexViewData();

        if ($this->RequestHandler->isAjax() || !empty($this->request->getQuery('ajax'))) {
            $this->render('ajax_index');
            return;
        }

        if ($this->request->getAttribute('currentContent')->status) {
            $this->set(
                'publishLink',
                $this->Content->getUrl(
                    $this->request->getAttribute('currentContent')->url,
                    true,
                    $this->request->getAttribute('currentSite')->use_subdomain
                )
            );
        }
        $this->pageTitle = sprintf(
            __d('baser', '%s｜記事一覧'),
            strip_tags(
                $this->request->getAttribute('currentContent')->title
            )
        );
        $this->setSearch('blog_posts_index');
        $this->setHelp('blog_posts_index');
    }

    /**
     * 一覧の表示用データをセットする
     *
     * @return void
     */
    protected function _setAdminIndexViewData()
    {
        $user = $this->BcAuth->user();
        $allowOwners = [];
        if (!empty($user)) {
            $allowOwners = ['', $user['user_group_id']];
        }
        $this->set('allowOwners', $allowOwners);
        $this->set('users', $this->BlogPost->User->getUserList());
    }

    /**
     * ページ一覧用の検索条件を生成する
     *
     * @param int $blogContentId
     * @param array $data
     * @return array $conditions
     */
    protected function _createAdminIndexConditions($blogContentId, $data)
    {
        unset($data['ListTool']);
        $name = $blogCategoryId = '';
        if (isset($data['BlogPost']['name'])) {
            $name = $data['BlogPost']['name'];
        }

        unset($data['BlogPost']['name']);
        unset($data['_Token']);
        if (isset($data['BlogPost']['status']) && $data['BlogPost']['status'] === '') {
            unset($data['BlogPost']['status']);
        }
        if (isset($data['BlogPost']['user_id']) && $data['BlogPost']['user_id'] === '') {
            unset($data['BlogPost']['user_id']);
        }
        if (!empty($data['BlogPost']['blog_category_id'])) {
            $blogCategoryId = $data['BlogPost']['blog_category_id'];
        }
        unset($data['BlogPost']['blog_category_id']);

        $conditions = ['BlogPost.blog_content_id' => $blogContentId];

        // CSVの場合はHABTM先のテーブルの条件を直接設定できない為、タグに関連するポストを抽出して条件を生成
        $db = ConnectionManager::getDataSource($this->BlogPost->useDbConfig);

        if ($db->config['datasource'] === 'Database/BcCsv') {
            if (!empty($data['BlogPost']['blog_tag_id'])) {
                $blogTags = $this->BlogPost->BlogTag->read(
                    null,
                    $data['BlogPost']['blog_tag_id']
                );
                if ($blogTags) {
                    $conditions['BlogPost.id'] = Hash::extract(
                        $blogTags,
                        '{n}.BlogPost.id'
                    );
                }
            }
        }

        unset($data['BlogPost']['blog_tag_id']);

        // ページカテゴリ（子カテゴリも検索条件に入れる）
        if ($blogCategoryId) {
            $blogCategoryIds = [$blogCategoryId];
            $children = $this->BlogCategory->children($blogCategoryId);
            if ($children) {
                foreach ($children as $child) {
                    $blogCategoryIds[] = $child['BlogCategory']['id'];
                }
            }
            $conditions['BlogPost.blog_category_id'] = $blogCategoryIds;
        } else {
            unset($data['BlogPost']['blog_category_id']);
        }

        if (isset($data['BlogPost']['status'])) {
            $conditions['BlogPost.status'] = $data['BlogPost']['status'];
        }
        if (isset($data['BlogPost']['user_id'])) {
            $conditions['BlogPost.user_id'] = $data['BlogPost']['user_id'];
        }

        if ($name) {
            $conditions['BlogPost.name LIKE'] = '%' . $name . '%';
        }

        return $conditions;
    }

    /**
     * [ADMIN] 登録処理
     *
     * @param int $blogContentId
     * @return void
     */
    public function admin_add($blogContentId)
    {
        if (!$blogContentId || !$this->blogContent) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(
                ['controller' => 'blog_contents', 'action' => 'index']
            );
        }

        if ($this->request->is(['post', 'put'])) {
            if (BcUtil::isOverPostSize()) {
                $this->BcMessage->setError(
                    __d(
                        'baser',
                        '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                        ini_get('post_max_size')
                    )
                );
                $this->redirect(['action' => 'add', $blogContentId]);
            }

            $this->request = $this->request->withData('BlogPost.blog_content_id',  $blogContentId);
            $this->request = $this->request->withData('BlogPost.posts_date', str_replace(
                '/',
                '-',
                $this->request->getData('BlogPost.posts_date')
            ));

            if (!BcUtil::isAdminUser()) {
                $user = $this->BcAuth->user();
                $this->request = $this->request->withData('BlogPost.user_id', $user['id']);
            }

            // EVENT BlogPosts.beforeAdd
            $event = $this->dispatchEvent('beforeAdd', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                if ($event->getResult() === true) {
                    $this->request = $this->request->withParsedBody($event->getData('data'));
                } else {
                    $this->request = $this->request->withParsedBody($event->getResult());
                }
            }

            // データを保存
            try {
                if ($this->BlogPost->saveAll($this->request->getData())) {
                    clearViewCache();
                    $id = $this->BlogPost->getLastInsertId();
                    $this->BcMessage->setSuccess(
                        sprintf(
                            __d('baser', '記事「%s」を追加しました。'),
                            $this->request->getData('BlogPost.name')
                        )
                    );

                    // 下のBlogPost::read()で、BlogTagデータ無しのキャッシュを作ってしまわないように
                    // recursiveを設定
                    $this->BlogPost->recursive = 1;

                    // EVENT BlogPosts.afterAdd
                    $this->dispatchEvent('afterAdd', [
                        'data' => $this->BlogPost->read(null, $id)
                    ]);

                    // 編集画面にリダイレクト
                    $this->redirect(['action' => 'edit', $blogContentId, $id]);
                } else {
                    $this->BcMessage->setError(
                        __d('baser', 'エラーが発生しました。内容を確認してください。')
                    );
                }
            } catch (Exception $e) {
                if ($e->getCode() === "23000") {
                    $this->BcMessage->setError(
                        __d('baser', '同時更新エラーです。しばらく経ってから保存してください。')
                    );
                } else {
                    $this->BcMessage->setError(
                        __d('baser', 'データベース処理中にエラーが発生しました。')
                    );
                }
            }
        } else {
            $this->request = $this->request->withParsedBody($this->BlogPost->getDefaultValue(
                $this->BcAuth->user()
            ));
        }

        // 表示設定
        $user = $this->BcAuth->user();
        $categories = $this->BlogPost->getControlSource(
            'blog_category_id',
            [
                'blogContentId' => $this->blogContent['BlogContent']['id'],
                'userGroupId' => $user['user_group_id'],
                'postEditable' => true,
                'empty' => __d('baser', '指定しない')
            ]
        );

        $editorOptions = ['editorDisableDraft' => true];
        if (!empty($this->siteConfigs['editor_styles'])) {
            App::uses('CKEditorStyleParser', 'Vendor');
            $CKEditorStyleParser = new CKEditorStyleParser();
            $editorStyles = [
                'default' => $CKEditorStyleParser->parse(
                    $this->siteConfigs['editor_styles']
                )
            ];
            $editorOptions = array_merge($editorOptions, [
                'editorStylesSet' => 'default',
                'editorStyles' => $editorStyles
            ]);
        }
        $this->set(
            'hasNewCategoryAddablePermission',
            $this->BlogPost->BlogCategory->hasNewCategoryAddablePermission(
                $user['user_group_id'],
                $blogContentId
            )
        );
        $this->set(
            'hasNewTagAddablePermission',
            $this->BlogPost->BlogTag->hasNewTagAddablePermission(
                $user['user_group_id'],
                $blogContentId
            )
        );
        $this->set('editable', true);
        $this->set('categories', $categories);
        $this->set('previewId', 'add_' . mt_rand(0, 99999999));
        $this->set('editorOptions', $editorOptions);
        $this->set('users', $this->BlogPost->User->getUserList());
        $this->pageTitle = sprintf(
            __d('baser', '%s｜新規記事登録'),
            $this->request->getAttribute('currentContent')->title
        );
        $this->setHelp('blog_posts_form');
        $this->render('form');
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param int $blogContentId
     * @param int $id
     * @return void
     */
    public function admin_edit($blogContentId, $id)
    {
        if (!$blogContentId || !$id) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(
                [
                    'plugin' => 'blog',
                    'admin' => true,
                    'controller' => 'blog_posts',
                    'action' => 'index',
                    $blogContentId]
            );
        }

        $this->BlogPost->recursive = 2;
        if ($this->request->is(['post', 'put'])) {
            if (BcUtil::isOverPostSize()) {
                $this->BcMessage->setError(
                    __d(
                        'baser',
                        '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                        ini_get('post_max_size')
                    )
                );
                $this->redirect(['action' => 'edit', $blogContentId, $id]);
            }
            if (!empty($this->request->getData('BlogPost.posts_date'))) {
                $this->request = $this->request->withData('BlogPost.posts_date', str_replace(
                    '/',
                    '-',
                    $this->request->getData('BlogPost.posts_date')
                ));
            }

            if (!BcUtil::isAdminUser()) {
                $this->request = $this->request->withData('BlogPost.user_id', $this->BlogPost->field(
                    'user_id',
                    [
                        'BlogPost.id' => $id,
                        'BlogPost.blog_content_id' => $blogContentId
                    ]
                ));
            }

            // EVENT BlogPosts.beforeEdit
            $event = $this->dispatchEvent('beforeEdit', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                if ($event->getResult() === true) {
                    $this->request = $this->request->withParsedBody($event->getData('data'));
                } else {
                    $this->request = $this->request->withParsedBody($event->getResult());
                }
            }

            // データを保存
            if ($this->BlogPost->saveAll($this->request->getData())) {
                clearViewCache();
                $this->BcMessage->setSuccess(
                    sprintf(
                        __d('baser', '記事「%s」を更新しました。'),
                        $this->request->getData('BlogPost.name')
                    )
                );

                // EVENT BlogPosts.afterEdit
                $this->dispatchEvent('afterEdit', [
                    'data' => $this->BlogPost->read(null, $id)
                ]);

                $this->redirect(['action' => 'edit', $blogContentId, $id]);
            } else {
                $this->BcMessage->setError(
                    __d('baser', 'エラーが発生しました。内容を確認してください。')
                );
            }
        } else {
            $this->request = $this->request->withParsedBody($this->BlogPost->find('first', [
                'conditions' => [
                    'BlogPost.id' => $id,
                    'BlogPost.blog_content_id' => $blogContentId
                ]
            ]));
            if (!$this->request->getData()) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(
                    [
                        'plugin' => 'blog',
                        'admin' => true,
                        'controller' => 'blog_posts',
                        'action' => 'index',
                        $blogContentId
                    ]
                );
            }
        }

        // 表示設定
        $user = $this->BcAuth->user();
        $blogCategoryId = '';

        if ($this->request->getData('BlogPost.blog_category_id')) {
            $blogCategoryId = $this->request->getData('BlogPost.blog_category_id');
        }

        $categories = $this->BlogPost->getControlSource('blog_category_id', [
            'blogContentId' => $this->blogContent['BlogContent']['id'],
            'blogCategoryId' => $blogCategoryId,
            'userGroupId' => $user['user_group_id'],
            'empty' => __d('baser', '指定しない')
        ]);

        if ($this->request->getData('BlogPost.status')) {
            $this->set(
                'publishLink',
                $this->Content->getUrl(
                    sprintf(
                        "%sarchives/%s",
                        $this->request->getAttribute('currentContent')->url,
                        $this->request->getData('BlogPost.no')
                    ),
                    true,
                    $this->request->getAttribute('currentSite')->use_subdomain
                )
            );
        }

        $editorOptions = ['editorDisableDraft' => false];
        if (!empty($this->siteConfigs['editor_styles'])) {
            App::uses('CKEditorStyleParser', 'Vendor');
            $CKEditorStyleParser = new CKEditorStyleParser();
            $editorStyles = [
                'default' => $CKEditorStyleParser->parse(
                    $this->siteConfigs['editor_styles']
                )
            ];
            $editorOptions = array_merge($editorOptions, [
                'editorStylesSet' => 'default',
                'editorStyles' => $editorStyles
            ]);
        }
        $this->set(
            'hasNewCategoryAddablePermission',
            $this->BlogPost->BlogCategory->hasNewCategoryAddablePermission(
                $user['user_group_id'],
                $blogContentId
            )
        );
        $this->set(
            'hasNewTagAddablePermission',
            $this->BlogPost->BlogTag->hasNewTagAddablePermission(
                $user['user_group_id'],
                $blogContentId
            )
        );
        $this->set('categories', $categories);
        $this->set('previewId', $this->request->getData('BlogPost.id'));
        $this->set('users', $this->BlogPost->User->getUserList());
        $this->set('editorOptions', $editorOptions);
        $this->pageTitle = sprintf(
            __d('baser', '%s｜記事編集'),
            $this->request->getAttribute('currentContent')->title
        );
        $this->setHelp('blog_posts_form');
        $this->render('form');
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param int $blogContentId
     * @param int $id
     * @return void
     */
    public function admin_ajax_delete($blogContentId, $id = null)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        // 削除実行
        if ($this->_del($id)) {
            clearViewCache();
            exit(true);
        }

        exit();
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
                $this->_del($id);
            }
        }
        return true;
    }

    /**
     * データを削除する
     *
     * @param int $id
     * @return boolean
     */
    protected function _del($id)
    {
        // 削除実行
        if (!$this->BlogPost->delete($id)) {
            return false;
        }

        // メッセージ用にデータを取得
        $post = $this->BlogPost->read(null, $id);

        $this->BlogPost->saveDbLog(sprintf(__d('baser', '%s を削除しました。'), $post['BlogPost']['name']));
        return true;
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param int $blogContentId
     * @param int $id
     * @return void
     */
    public function admin_delete($blogContentId, $id = null)
    {
        $this->_checkSubmitToken();
        if (!$blogContentId || !$id) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(['controller' => 'blog_contents', 'action' => 'index']);
            return;
        }

        // 削除実行
        if (!$this->BlogPost->delete($id)) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            $this->redirect(['action' => 'index', $blogContentId]);
            return;
        }

        // メッセージ用にデータを取得
        $post = $this->BlogPost->read(null, $id);
        clearViewCache();
        $this->BcMessage->setSuccess(
            sprintf(
                __d('baser', '%s を削除しました。'),
                $post['BlogPost']['name']
            )
        );
        $this->redirect(['action' => 'index', $blogContentId]);
    }

    /**
     * 外部データインポート
     * WordPressのみ対応（2.2.3のみ検証済）
     *
     * @return void
     * @todo 未実装
     */
    //	public function admin_import() {
    //		// 入力チェック
    //		$check = true;
    //		$message = '';
    //		if (!isset($this->request->getData('Import.blog_content_id')) || !$this->request->getData('Import.blog_content_id')) {
    //			$message .= '取り込み対象のブログを選択してください<br />';
    //			$check = false;
    //		}
    //		if (!isset($this->request->getData('Import.user_id')) || !$this->request->getData('Import.user_id')) {
    //			$message .= '記事の投稿者を選択してください<br />';
    //			$check = false;
    //		}
    //		if (!isset($this->request->getData('Import.file.tmp_name'))) {
    //			$message .= 'XMLデータを選択してください<br />';
    //			$check = false;
    //		}
    //		if ($this->request->getData('Import.file.type') != 'text/xml') {
    //			$message .= 'XMLデータを選択してください<br />';
    //			$check = false;
    //		} else {
    //
    //			// XMLデータを読み込む
    //			$xml = new Xml($this->request->getData('Import.file.tmp_name'));
    //
    //			$_posts = Xml::toArray($xml);
    //
    //			if (!isset($_posts['Rss']['Channel']['Item'])) {
    //				$message .= 'XMLデータが不正です<br />';
    //				$check = false;
    //			} else {
    //				$_posts = $_posts['Rss']['Channel']['Item'];
    //			}
    //		}
    //
    //		// 送信内容に問題がある場合には元のページにリダイレクト
    //		if (!$check) {
    //			$this->BcMessage->setError($message);
    //			$this->redirect(array('controller' => 'blog_configs', 'action' => 'form'));
    //		}
    //
    //		// カテゴリ一覧の取得
    //		$blogCategoryList = $this->BlogCategory->find('list', array('conditions' => array('blog_content_id' => $this->request->getData('Import.blog_content_id'))));
    //		$blogCategoryList = array_flip($blogCategoryList);
    //
    //		// ポストデータに変換し１件ずつ保存
    //		$count = 0;
    //		foreach ($_posts as $_post) {
    //			if (!$_post['Encoded'][0]) {
    //				continue;
    //			}
    //			$post = array();
    //			$post['blog_content_id'] = $this->request->getData('Import.blog_content_id');
    //			$post['no'] = $this->BlogPost->getMax('no', array('BlogPost.blog_content_id' => $this->request->getData('Import.blog_content_id'))) + 1;
    //			$post['name'] = $_post['title'];
    //			$_post['Encoded'][0] = str_replace("\n", "<br />", $_post['Encoded'][0]);
    //			$encoded = explode('<!--more-->', $_post['Encoded'][0]);
    //			$post['content'] = $encoded[0];
    //			if (isset($encoded[1])) {
    //				$post['detail'] = $encoded[1];
    //			} else {
    //				$post['detail'] = '';
    //			}
    //			if (isset($_post['Category'])) {
    //				$_post['category'] = $_post['Category'][0];
    //			} elseif (isset($_post['category'])) {
    //				$_post['category'] = $_post['category'];
    //			} else {
    //				$_post['category'] = '';
    //			}
    //			if (isset($blogCategoryList[$_post['category']])) {
    //				$post['blog_category_no'] = $blogCategoryList[$_post['category']];
    //			} else {
    //				$no = $this->BlogCategory->getMax('no', array('BlogCategory.blog_content_id' => $this->request->getData('Import.blog_content_id'))) + 1;
    //				$this->BlogCategory->create(array('name' => $_post['category'], 'blog_content_id' => $this->request->getData('Import.blog_content_id'), 'no' => $no));
    //				$this->BlogCategory->save();
    //				$post['blog_category_id'] = $this->BlogCategory->getInsertID();
    //				$blogCategoryList[$_post['category']] = $post['blog_category_id'];
    //			}
    //
    //			$post['user_id'] = $this->request->getData('Import.user_id');
    //			$post['status'] = 1;
    //			$post['posts_date'] = $_post['post_date'];
    //
    //			$this->BlogPost->create($post);
    //			if ($this->BlogPost->save()) {
    //				$count++;
    //			}
    //		}
    //
    //		$this->BcMessage->setInfo($count . ' 件の記事を取り込みました');
    //		$this->redirect(array('controller' => 'blog_configs', 'action' => 'form'));
    //	}

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param string $blogContentId
     * @param string $blogPostId beforeFilterで利用
     * @param string $blogCommentId
     * @return void
     */
    public function admin_ajax_unpublish($blogContentId, $id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_changeStatus($id, false)) {
            $this->ajaxError(500, $this->BlogPost->validationErrors);
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
    public function admin_ajax_publish($blogContentId, $id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        if (!$this->_changeStatus($id, true)) {
            $this->ajaxError(500, $this->BlogPost->validationErrors);
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
        $statusTexts = [0 => __d('baser', '非公開状態'), 1 => __d('baser', '公開状態')];
        $data = $this->BlogPost->find(
            'first',
            ['conditions' => ['BlogPost.id' => $id], 'recursive' => -1]
        );
        $data['BlogPost']['status'] = $status;
        $data['BlogPost']['publish_begin'] = '';
        $data['BlogPost']['publish_end'] = '';
        unset($data['BlogPost']['eye_catch']);
        $this->BlogPost->set($data);

        if (!$this->BlogPost->save()) {
            return false;
        }

        $this->BlogPost->saveDbLog(
            sprintf(
                __d('baser', 'ブログ記事「%s」 を %s に設定しました。'),
                $data['BlogPost']['name'],
                $statusTexts[$status]
            )
        );
        return true;
    }

    /**
     * [ADMIN] コピー
     *
     * @param int $id
     * @return void
     */
    public function admin_ajax_copy($blogContentId, $id = null)
    {
        $this->_checkSubmitToken();
        if (!$this->BlogPost->copy($id)) {
            $this->ajaxError(500, $this->BlogPost->validationErrors);
            return;
        }

        // タグ情報を取得するため読み込みなおす
        $this->BlogPost->recursive = 1;
        $this->setViewConditions(
            'BlogPost',
            ['action' => 'admin_index']
        );
        $this->_setAdminIndexViewData();
        $this->set('data', $this->BlogPost->read());
    }
}
