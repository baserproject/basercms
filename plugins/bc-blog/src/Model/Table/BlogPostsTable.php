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

namespace BcBlog\Model\Table;

use ArrayObject;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Content;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Utility\BcUtil;
use Cake\Core\Plugin;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * 記事モデル
 *
 * @property BlogContentsTable $BlogContents
 * @property BlogCategoriesTable $BlogCategories
 * @property BlogTagsTable $BlogTags
 * @property UsersTable $Users
 */
class BlogPostsTable extends BlogAppTable
{
    /**
     * 検索テーブルへの保存可否
     *
     * @var boolean
     */
    public $searchIndexSaving = true;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('blog_posts');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('BlogTags', [
            'className' => 'BcBlog.BlogTags',
            'foreignKey' => 'blog_post_id',
            'targetForeignKey' => 'blog_tag_id',
            'through' => 'BcBlog.BlogPostsBlogTags',
            'joinTable' => 'blog_posts_blog_tags',
        ]);
        $this->hasMany('BlogComments', [
            'className' => 'BcBlog.BlogComments',
            'order' => 'created',
            'foreignKey' => 'blog_post_id',
            'dependent' => true
        ]);
        $this->belongsTo('BlogCategories', [
            'className' => 'BcBlog.BlogCategories',
            'foreignKey' => 'blog_category_id',
        ]);
        $this->belongsTo('BlogContents', [
            'className' => 'BcBlog.BlogContents',
            'foreignKey' => 'blog_content_id',
        ]);
        $this->belongsTo('Users', [
            'className' => 'BaserCore.Users',
            'foreignKey' => 'user_id',
        ]);
        $this->addBehavior('BaserCore.BcUpload', [
            'subdirDateFormat' => 'Y/m/',
            'fields' => [
                'eye_catch' => [
                    'type' => 'image',
                    'namefield' => 'no',
                    'nameformat' => '%08d'
                ]
            ]
        ]);
        if (Plugin::isLoaded('BcSearchIndex')) {
            $this->addBehavior('BcSearchIndex.BcSearchIndexManager');
        }
    }

    /**
     * デフォルトのバリデーションルールを設定
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->allowEmptyString('name')
            ->maxLength('name', 255, __d('baser_core', 'スラッグは255文字以内で入力してください。'))
            ->add('name', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるスラッグです。')
                ]])
            ->regex('name', '/\D/', __d('baser_core', '数値だけのスラッグを登録することはできません。'));
        $validator
            ->scalar('title')
            ->maxLength('title', 255, __d('baser_core', 'タイトルは255文字以内で入力してください。'))
            ->requirePresence('title', 'update', __d('baser_core', 'タイトルを入力してください。'))
            ->notEmptyString('title', __d('baser_core', 'タイトルを入力してください。'));
        $validator
            ->scalar('content')
            ->add('content', [
                'containsScript' => [
                    'rule' => ['containsScript'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '概要欄でスクリプトの入力は許可されていません。')
                ]
            ]);
        $validator
            ->scalar('detail')
            ->maxLengthBytes('detail', 16777215, __d('baser_core', '本稿欄に保存できるデータ量を超えています。'))
            ->add('detail', [
                'containsScript' => [
                    'rule' => ['containsScript'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '本稿欄でスクリプトの入力は許可されていません。')
                ]
            ]);
        $validator
            ->scalar('detail_draft')
            ->maxLengthBytes('detail_draft', 16777215, __d('baser_core', '草稿欄に保存できるデータ量を超えています。'))
            ->add('detail_draft', [
                'containsScript' => [
                    'rule' => ['containsScript'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '草稿欄でスクリプトの入力は許可されていません。')
                ]
            ]);
        $validator
            ->add('publish_begin', [
                'dateTime' => [
                    'rule' => ['dateTime'],
                    'message' => __d('baser_core', '公開開始日の形式が不正です。')
                ]
            ])
            ->allowEmptyDateTime('publish_begin')
            ->add('publish_begin', [
                'checkDateRange' => [
                    'rule' => ['checkDateRange', ['publish_begin', 'publish_end']],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '公開期間が不正です。')
                ]
            ]);
        $validator
            ->add('publish_end', [
                'dateTime' => [
                    'rule' => ['dateTime'],
                    'message' => __d('baser_core', '公開終了日の形式が不正です。')
                ]
            ])
            ->allowEmptyDateTime('publish_end');
        $validator
            ->add('posted', [
                'dateTime' => [
                    'rule' => ['dateTime'],
                    'message' => __d('baser_core', '投稿日の形式が不正です。')
                ]
            ])
            ->notEmptyDateTime('posted', __d('baser_core', '投稿日を入力してください。'));;
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id', __d('baser_core', '投稿者を選択してください。'));
        $validator
            ->allowEmptyString('eye_catch')
            ->add('eye_catch', [
                'fileCheck' => [
                    'rule' => ['fileCheck', BcUtil::convertSize(ini_get('upload_max_filesize'))],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'ファイルのアップロード制限を超えています。')
                ]
            ])
            ->add('eye_catch', [
                'fileExt' => [
                    'rule' => ['fileExt', ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png']],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '許可されていないファイルです。')
                ]
            ]);
        return $validator;
    }

    /**
     * Before Save
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!Plugin::isLoaded('BcSearchIndex') || !$this->searchIndexSaving) {
            return;
        }
        // 検索用テーブルに登録
        if ($entity->exclude_search
            || empty($entity->blog_content->content)
            || !empty($entity->blog_content->content->exclude_search)
        ) {
            $this->setExcluded();
        }
    }

    /**
     * アップロードビヘイビアの設定
     *
     * @param int $id ブログコンテンツID
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupUpload($id)
    {
        $sizes = ['thumb', 'mobile_thumb'];
        $data = $this->BlogContents->find()->where(['BlogContents.id' => $id])->first();
        $blogContent = $this->BlogContents->constructEyeCatchSize($data);

        $imagecopy = [];
        foreach($sizes as $size) {
            if (!isset($blogContent->{'eye_catch_size_' . $size . '_width'}) || !isset($blogContent->{'eye_catch_size_' . $size . '_height'})) {
                continue;
            }
            $imagecopy[$size] = ['suffix' => '__' . $size];
            $imagecopy[$size]['width'] = $blogContent->{'eye_catch_size_' . $size . '_width'};
            $imagecopy[$size]['height'] = $blogContent->{'eye_catch_size_' . $size . '_height'};
        }

        $bcUpload = $this->getBehavior('BcUpload');
        $settings = $bcUpload->getSettings();
        if (empty($settings['saveDir']) || !preg_match('/^' . preg_quote("blog" . DS . $blogContent->id, '/') . '\//', $settings['saveDir'])) {
            $settings['saveDir'] = "blog" . DS . $blogContent->id . DS . "blog_posts";
        }

        $settings['fields']['eye_catch']['imagecopy'] = $imagecopy;
        $bcUpload->setSettings($settings);
    }

    /**
     * ブログの月別一覧を取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param array $options オプション
     * @return array 月別リストデータ
     * @checked
     * @noTodo
     */
    public function getPostedDates($blogContentId = null, $options = [])
    {
        $options = array_merge([
            'category' => false,
            'limit' => false,
            'viewCount' => false,
            'type' => 'month' // month Or year
        ], $options);

        $conditions = [];
        if ($blogContentId) $conditions = ['BlogPosts.blog_content_id' => $blogContentId];
        $conditions = array_merge($conditions, $this->getConditionAllowPublish());

        // 毎秒抽出条件が違うのでキャッシュしない
        $posts = $this->find()
            ->contain(['BlogCategories'])
            ->where($conditions)
            ->order(['BlogPosts.posted DESC'])
            ->all();

        $postedDates = [];
        $counter = 0;
        foreach($posts as $post) {
            $year = date('Y', strtotime($post->posted));
            $month = date('m', strtotime($post->posted));
            if ($options['type'] === 'year') {
                $key = $year;
            } else {
                $key = $year . $month;
            }
            if ($options['category'] && $post->blog_category) $key .= '-' . $post->blog_category->id;
            if (!isset($postedDates[$key])) {
                $postedDate = [
                    'year' => $year,
                    'month' => ($options['type'] === 'month')? $month : null,
                    'count' => ($options['viewCount'])? 1 : null
                ];
                if ($options['category'] && $post->blog_category) $postedDate['category'] = $post->blog_category;
                $postedDates[$key] = $postedDate;
                $counter++;
            } else {
                if (!$options['viewCount']) continue;
                $postedDates[$key]['count']++;
            }
            if ($options['limit'] !== false && $options['limit'] <= $counter) break;
        }
        return $postedDates;
    }

    /**
     * カレンダー用に指定した月で記事の投稿がある日付のリストを取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param int $year 年
     * @param int $month 月
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEntryDates($blogContentId, $year, $month)
    {
        $entryDates = $this->find()
            ->select(['BlogPosts.posted'])
            ->where($this->_getEntryDatesConditions($blogContentId, $year, $month))
            ->all();
        $entryDates = Hash::extract($entryDates->toArray(), '{n}.posted');
        foreach($entryDates as $key => $entryDate) {
            $entryDates[$key] = date('Y-m-d', strtotime($entryDate));
        }
        return $entryDates;
    }

    /**
     * 投稿者の一覧を取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param array $options オプション
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAuthors(int $blogContentId, array $options)
    {
        $options = array_merge([
            'viewCount' => false
        ], $options);
        $users = $this->Users->find()
            ->order(['Users.id'])
            ->select([
                'Users.id',
                'Users.name',
                'Users.real_name_1',
                'Users.real_name_2',
                'Users.nickname'
            ]);
        $availableUsers = [];
        foreach($users as $user) {
            $count = $this->find()->where(array_merge([
                'BlogPosts.user_id' => $user->id,
                'BlogPosts.blog_content_id' => $blogContentId
            ], $this->getConditionAllowPublish()))->count();
            if ($count) {
                if ($options['viewCount']) $user->count = $count;
                $availableUsers[] = $user;
            }
        }
        return $availableUsers;
    }

    /**
     * 指定した月の記事が存在するかチェックする
     *
     * @param int $blogContentId
     * @param int $year
     * @param int $month
     * @return    boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function existsEntry(int $blogContentId, int $year, int $month): bool
    {
        if ($this->find()
            ->select(['BlogPosts.id'])
            ->where($this->_getEntryDatesConditions($blogContentId, $year, $month))
            ->count()
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 年月を指定した検索条件を生成
     * データベースごとに構文が違う
     *
     * @param int $blogContentId
     * @param int $year
     * @param int $month
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getEntryDatesConditions($blogContentId, $year, $month)
    {
        $driver = $this->getConnection()->config()['driver'];
        $conditions = [];
        switch($driver) {
            case Mysql::class:
                if (!empty($year)) {
                    $conditions["YEAR(`BlogPosts`.`posted`)"] = $year;
                } else {
                    $conditions["YEAR(`BlogPosts`.`posted`)"] = date('Y');
                }
                if (!empty($month)) {
                    $conditions["MONTH(`BlogPosts`.`posted`)"] = $month;
                } else {
                    $conditions["MONTH(`BlogPosts`.`posted`)"] = date('m');
                }
                break;
            case Postgres::class:
                if (!empty($year)) {
                    $conditions["date_part('year', BlogPosts.posted) ="] = $year;
                } else {
                    $conditions["date_part('year', BlogPosts.posted) ="] = date('Y');
                }
                if (!empty($month)) {
                    $conditions["date_part('month', BlogPosts.posted) ="] = $month;
                } else {
                    $conditions["date_part('month', BlogPosts.posted) ="] = date('m');
                }
                break;
            case Sqlite::class:
                if (!empty($year)) {
                    $conditions["strftime('%Y',BlogPosts.posted)"] = $year;
                } else {
                    $conditions["strftime('%Y',BlogPosts.posted)"] = date('Y');
                }
                if (!empty($month)) {
                    $conditions["strftime('%m',BlogPosts.posted)"] = sprintf('%02d', $month);
                } else {
                    $conditions["strftime('%m',BlogPosts.posted)"] = date('m');
                }
                break;
        }
        return array_merge_recursive($conditions, ['BlogPosts.blog_content_id' => $blogContentId], $this->getConditionAllowPublish());
    }

    /**
     * 公開状態を取得する
     *
     * 期限が設定されている場合、期限外では公開状態が非公開となる
     *
     * @param array $data モデルデータ
     * @return boolean 公開状態
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allowPublish($post)
    {
        if (!$post->status) {
            return false;
        }

        // 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
        $currentTime = time();
        if (($post->publish_begin && $post->publish_begin->getTimestamp() > $currentTime) ||
            ($post->publish_end && $post->publish_end->getTimestamp() < $currentTime)
        ) {
            return false;
        }

        return true;
    }

    /**
     * 公開状態の記事を取得する
     *
     * @param array $options
     * @return array
     */
    public function getPublishes($options)
    {
        if (!empty($options['conditions'])) {
            $options['conditions'] = array_merge($this->getConditionAllowPublish(), $options['conditions']);
        } else {
            $options['conditions'] = $this->getConditionAllowPublish();
        }
        // 毎秒抽出条件が違うのでキャッシュしない
        $datas = $this->find('all', $options);
        return $datas;
    }

    /**
     * After Validate
     */
    public function afterValidate()
    {
        parent::afterValidate();
        if (empty($this->data['BlogPost']['blog_content_id'])) {
            throw new BcException('blog_content_id が指定されていません。');
        }
        if (empty($this->validationErrors) && empty($this->data['BlogPost']['id'])) {
            $this->data['BlogPost']['no'] = $this->getMax('no', ['BlogPost.blog_content_id' => $this->data['BlogPost']['blog_content_id']]) + 1;
        }
    }

    /**
     * afterSave
     *
     * @param boolean $created
     * @param array $options
     * @checked
     */
    public function afterSave($created, $options = [])
    {
        // 検索用テーブルへの登録・削除
        // ucmitz 未実装
        // >>>
//        if ($this->searchIndexSaving && empty($this->data['BlogPost']['exclude_search'])) {
//            $this->saveSearchIndex($this->createSearchIndex($this->data));
//        } else {
//            if (!empty($this->data['BlogPost']['id'])) {
//                $this->deleteSearchIndex($this->data['BlogPost']['id']);
//            } elseif (!empty($this->id)) {
//                $this->deleteSearchIndex($this->id);
//            } else {
//                $this->cakeError('Not found pk-value in BlogPost.');
//            }
//        }
        // <<<
    }

    /**
     * 検索用データを生成する
     *
     * @param array $data
     * @return array|false
     * @checked
     * @noTodo
     */
    public function createSearchIndex($post)
    {
        /* @var Content $content */
        $content = $this->BlogContents->Contents->findByType('BcBlog.BlogContent', $post->blog_content_id);
        if (!$content) {
            return false;
        }

        $status = $post->status;
        $publishBegin = $post->publish_begin;
        $publishEnd = $post->publish_end;
        // コンテンツのステータスを優先する
        if (!$content->status) {
            $status = false;
        }

        if ($publishBegin) {
            if ((!empty($content->publish_begin) && $content->publish_begin > $publishBegin)) {
                // コンテンツの公開開始の方が遅い場合
                $publishBegin = $content->publish_begin;
            } elseif (!empty($content->publish_end) && $content->publish_end < $publishBegin) {
                // 記事の公開開始より、コンテンツの公開終了が早い場合
                $publishBegin = $content->publish_end;
            }
        } else {
            if (!empty($content->publish_begin)) {
                // 記事の公開開始が定められていない
                $publishBegin = $content->publish_begin;
            }
        }
        if ($publishEnd) {
            if (!empty($content->publish_end) && $content->publish_end < $publishEnd) {
                // コンテンツの公開終了の方が早い場合
                $publishEnd = $content->publish_end;
            } elseif (!empty($content->publish_begin) && $content->publish_begin < $publishEnd) {
                // 記事の公開終了より、コンテンツの公開開始が早い場合
                $publishEnd = $content->publish_begin;
            }
        } else {
            if (!empty($content->publish_end)) {
                // 記事の公開終了が定められていない
                $publishEnd = $content->publish_end;
            }
        }

        return [
            'type' => __d('baser_core', 'ブログ'),
            'model_id' => $post->id,
            'content_filter_id' => !empty($post->blog_category_id)? $post->blog_category_id : '',
            'content_id' => $content->id,
            'site_id' => $content->site_id,
            'title' => $post->title,
            'detail' => $post->content . ' ' . $post->detail,
            'url' => $content->url . 'archives/' . $post->no,
            'status' => $status,
            'publish_begin' => $publishBegin,
            'publish_end' => $publishEnd
        ];
    }

    /**
     * コピーする
     *
     * @param int $id
     * @param array $data
     * @return mixed page Or false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id = null, $data = [])
    {
        if ($id) $data = $this->find()->where(['BlogPosts.id' => $id])->contain('BlogTags')->first();
        $oldData = clone $data;

        // EVENT BlogPosts.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $data,
            'id' => $id,
        ]);
        if ($event !== false) {
            $data = $event->getResult() === true || is_null($event->getResult())? $event->getData('data') : $event->getResult();
        }

        $data->user_id = BcUtil::loginUser()['id'];
        if ($data->name) $data->name .= '_copy';
        $data->title .= '_copy';
        $data->no = $this->getMax('no', ['BlogPosts.blog_content_id' => $data->blog_content_id]) + 1;
        $data->status = false;
        $data->posted = FrozenTime::now();
        $data->id = null;
        $data->created = null;
        $data->modified = null;
        // 一旦退避(afterSaveでリネームされてしまうのを避ける為）
        $eyeCatch = $data->eye_catch;
        $data->eye_catch = null;
        $arrayData = $data->toArray();
        if (!empty($arrayData['blog_tags'])) {
            $blogTagIds = [];
            foreach($arrayData['blog_tags'] as $tag) {
                $blogTagIds[] = $tag['id'];
            }
            unset($arrayData['blog_tags']);
            $arrayData['blog_tags']['_ids'] = $blogTagIds;
        }

        try {
            $result = $this->saveOrFail($this->patchEntity($this->newEmptyEntity(), $arrayData));
            if ($eyeCatch) {
                $result->eye_catch = $eyeCatch;
                $this->renameToBasenameFields($result, true);
                $result = $this->save($result);
            }

            // EVENT BlogPosts.afterCopy
            $this->dispatchLayerEvent('afterCopy', [
                'id' => $result->id,
                'data' => $result,
                'oldId' => $id,
                'oldData' => $oldData,
            ]);
            return $result;
        } catch (PersistenceFailedException $e) {
            if ($e->getEntity()->getError('name')) {
                $data->eye_catch = $eyeCatch;
                return $this->copy(null, $data);
            } else {
                return false;
            }
        } catch (BcException $e) {
            throw $e;
        }
    }

    /**
     * プレビュー用のデータを生成する
     *
     * @param array $data
     */
    public function createPreviewData($data)
    {
        $post['BlogPost'] = $data['BlogPost'];
        if (isset($post['BlogPost']['detail_tmp'])) {
            $post['BlogPost']['detail'] = $post['BlogPost']['detail_tmp'];
        }

        if ($data['BlogPost']['blog_category_id']) {
            $blogCategory = $this->BlogCategory->find('first', [
                'conditions' => ['BlogCategory.id' => $data['BlogPost']['blog_category_id']],
                'recursive' => -1
            ]);
            $post['BlogCategory'] = $blogCategory['BlogCategory'];
        }

        if ($data['BlogPost']['user_id']) {
            $author = $this->User->find('first', [
                'conditions' => ['User.id' => $data['BlogPost']['user_id']],
                'recursive' => -1
            ]);
            $post['User'] = $author['User'];
        }

        if (!empty($data['BlogTag']['BlogTag'])) {
            $tags = $this->BlogTag->find('all', [
                'conditions' => ['BlogTag.id' => $data['BlogTag']['BlogTag']],
                'recursive' => -1
            ]);
            if ($tags) {
                $tags = Hash::extract($tags, '{n}.BlogTag');
                $post['BlogTag'] = $tags;
            }
        }

        // BlogPostキーのデータは作り直しているため、元データは削除して他のモデルキーのデータとマージする
        unset($data['BlogPost']);
        unset($data['BlogTag']); // プレビュー時に、フロントでの利用データの形式と異なるため削除
        $post = Hash::merge($data, $post);

        return $post;
    }

    /**
     * Before Find
     *
     * @param array $options
     * @return array
     * @checked
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary)
    {
        // ================================================================
        // 日付等全く同じ値のレコードが複数存在する場合の並び替え処理を安定する為、
        // IDが order に入っていない場合、IDを追加する
        // PostgreSQLの場合、max min count sum を利用している際に、order を
        // 指定するとエラーとなってしまうので、追加するのは最小限にする
        // ================================================================
        // TODO ucmitz 未実装
        return;
        $idRequire = false;
        if (!empty($options['order']) && isset($options['order'][0]) && $options['order'][0] !== false) {
            $idRequire = true;
            if (is_array($options['order'])) {
                foreach($options['order'] as $key => $value) {
                    if (strpos($value, ',') !== false) {
                        $orders = explode(',', $value);
                        foreach($orders as $order) {
                            if (strpos($order, 'BlogPost.id') !== false) {
                                $idRequire = false;
                            }
                        }
                    } else {
                        if (strpos($key, 'BlogPost.id') !== false) {
                            $idRequire = false;
                        }
                    }
                }
            } else {
                if (strpos('BlogPost.id', $options['sort']) === false) {
                    $idRequire = false;
                }
            }
        }
        if ($idRequire) {
            $options['order']['BlogPost.id'] = 'DESC';
        }
        return $options;
    }

    /**
     * 公開状態の記事をNOより取得する
     *
     * @param int $blogContentId
     * @param int|string $no
     * @return array|\Cake\Datasource\EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPublishByNo(int $blogContentId, mixed $no, bool $preview = false)
    {
        $conditions = ['BlogPosts.blog_content_id' => $blogContentId];
        if (!$preview) {
            $conditions = array_merge($conditions, $this->getConditionAllowPublish());
        }
        if (is_numeric($no)) {
            $conditions = array_merge_recursive(
                $conditions,
                ['BlogPosts.no' => $no]
            );
        } else {
            $conditions = array_merge_recursive(
                $conditions,
                ['BlogPosts.name' => rawurldecode($no)]
            );
        }
        $entity = $this->find()->where($conditions)
            ->contain([
                'BlogContents' => ['Contents' => ['Sites']],
                'BlogCategories',
                'BlogTags',
                'BlogComments',
                'Users'
            ])
            ->first();
        if($entity) {
            unset($entity->content_draft);
            unset($entity->detail_draft);
        }
        return $entity;
    }

}
