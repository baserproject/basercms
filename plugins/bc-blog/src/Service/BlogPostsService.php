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
use BaserCore\Model\Entity\Content;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Model\Table\BlogPostsTable;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\QueryInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * BlogPostsService
 *
 * @property BlogPostsTable $BlogPosts
 */
class BlogPostsService implements BlogPostsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * BlogPostsTable
     * @var BlogPostsTable|Table
     */
    public BlogPostsTable|Table $BlogPosts;

    /**
     * Constructor
     *
     * BlogPosts テーブルを初期化してセットする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogPosts = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
    }

    /**
     * BlogPostsTable のファイルアップロードの設定を実施
     *
     * @param int $blogContentId
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのため、ユニットテストは不要
     */
    public function setupUpload(int $blogContentId): void
    {
        $this->BlogPosts->setupUpload($blogContentId);
    }

    /**
     * 単一データを取得する
     *
     * @param int $id
     * @param array $options
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $options = [])
    {
        $options = array_merge([
            'status' => '',
            'contain' => [
                'BlogContents' => ['Contents' => ['Sites']],
                'BlogCategories',
                'BlogTags'
            ],
            'draft' => null
        ], $options);
        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->BlogPosts->getConditionAllowPublish();
            $conditions = array_merge($conditions, $this->BlogPosts->BlogContents->Contents->getConditionAllowPublish());
        }
        $entity = $this->BlogPosts->get($id,
            conditions: $conditions,
            contain: $options['contain']
        );
        if ($options['draft'] === false) {
            unset($entity->content_draft);
            unset($entity->detail_draft);
        }
        return $entity;
    }

    /**
     * ブログ記事一覧を取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $options = array_merge([
            'num' => null,
            'limit' => null,
            'direction' => 'DESC',    // 並び方向
            'order' => 'posted',    // 並び順対象のフィールド
            'sort' => null,
            'id' => null,
            'no' => null,
            'contentUrl' => null,
            'status' => '',
            'contain' => [
                'Users',
                'BlogCategories',
                'BlogContents',
                'BlogComments',
                'BlogTags',
            ],
            'draft' => null
        ], $queryParams);

        if (!empty($options['num'])) $options['limit'] = $options['num'];
        if (!empty($options['sort'])) $options['order'] = $options['sort'];
        unset($options['num'], $options['sort']);

        if ($options['id'] || $options['no']) $options['contain'][] = 'BlogComments';
        if ($options['contain'] == null)
            $options['contain'] = [];
        $query = $this->BlogPosts->find()->contain($options['contain']);

        if ($options['order']) {
            $query->orderBy($this->createOrder($options['order'], $options['direction']));
            unset($options['order'], $options['direction']);
        }
        if (!empty($options['limit'])) {
            $query->limit($options['limit']);
            unset($options['limit']);
        }
        if (!empty($options)) {
            $query = $this->createIndexConditions($query, $options);
        }

        return $query;
    }

    /**
     * 並び替え設定を生成する
     *
     * @param string $sort
     * @param string $direction
     * @return string
     * @checked
     * @unitTest
     */
    public function createOrder($sort, $direction)
    {
        $order = '';
        if (strtoupper($direction) === 'RANDOM') {
            // TODO ucmitz 未実装
//            $datasource = strtolower(preg_replace('/^Database\/Bc/', '', ConnectionManager::getDataSource($this->useDbConfig)->config['datasource']));
            $datasource = 'mysql';
            switch($datasource) {
                case 'mysql':
                    $order = 'RAND()';
                    break;
                case 'postgres':
                    $order = 'RANDOM()';
                    break;
                case 'sqlite':
                    $order = 'RANDOM()';
                    break;
            }
        } else {
            if (strpos($sort, '.') === false) {
                $sort = 'BlogPosts.' . $sort;
            }
            $order = "{$sort} {$direction}, BlogPosts.id {$direction}";
        }
        return $order;
    }

    /**
     * ページ一覧用の検索条件を生成する
     *
     * @param \Cake\ORM\Query $query
     * @param array $params
     * - title: タイトル
     * - user_id: 作成者
     * - status: 公開状態。publish もしくは 1 の場合、公開期間も加味する。
     * - blog_content_id: ブログコンテンツID
     * - blog_tag_id: ブログタグID
     * - blog_category_id: ブログカテゴリID
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function createIndexConditions(Query $query, array $params)
    {
        foreach($params as $key => $value) {
            if ($value === '') unset($params[$key]);
        }
        if (empty($params)) return $query;

        $params = array_merge([
            'id' => null,
            'title' => null,
            'user_id' => null,
            'status' => null,
            'blog_content_id' => null,
            'blog_tag_id' => null,
            'blog_category_id' => null,
            'site_id' => null,
            'category' => null,
            'keyword' => null,
            'tag' => null,
            'year' => null,
            'month' => null,
            'day' => null,
            'siteId' => null,
            'contentUrl' => null,
            'postId' => null,
            'preview' => false,
            'force' => false
        ], $params);

        if (!empty($params['postId'])) $params['id'] = $params['postId'];
        if (!empty($params['siteId'])) $params['site_id'] = $params['siteId'];
        unset($params['postId'], $params['siteId']);

        // ステータス
        if (($params['status'] === 'publish' || (string)$params['status'] === '1') && !$params['preview']) {
            $conditions = $this->BlogPosts->getConditionAllowPublish();
            $conditions = array_merge($conditions, $this->BlogPosts->BlogContents->Contents->getConditionAllowPublish());
            $query->contain(['BlogContents' => ['Contents']]);
            if($params['draft'] === false) {
                $query->selectAllExcept($this->BlogPosts, ['content_draft', 'detail_draft']);
                $query = $this->selectContains($query);
            }
        } elseif ((string)$params['status'] === '0') {
            $conditions = ['BlogPosts.status' => false];
        } else {
            $conditions = [];
        }
        // ID
        if ($params['id']) $conditions["BlogPosts.id"] = $params['id'];
        // タイトル
        if (!is_null($params['title'])) $conditions['BlogPosts.title LIKE'] = '%' . $params['title'] . '%';
        // ユーザーID
        if (!is_null($params['user_id'])) $conditions['BlogPosts.user_id'] = $params['user_id'];
        // ブログコンテンツID
        if (!is_null($params['blog_content_id'])) $conditions['BlogPosts.blog_content_id'] = $params['blog_content_id'];
        // サイトID
        if (!is_null($params['site_id'])) $conditions['Contents.site_id'] = $params['site_id'];
        // URL
        if ($params['contentUrl']) {
            $query->contain(['BlogContents' => ['Contents']]);
            if (is_array($params['contentUrl'])) {
                $conditions['Contents.url IN'] = $params['contentUrl'];
            } else {
                $conditions['Contents.url'] = $params['contentUrl'];
            }
        }
        // タグ
        if (!is_null($params['blog_tag_id'])) {
            $query->matching('BlogTags', function($q) use ($params) {
                return $q->where(['BlogTags.id' => $params['blog_tag_id']]);
            });
        }
        // ページカテゴリ（子カテゴリも検索条件に入れる）
        if (!is_null($params['blog_category_id'])) {
            $blogCategoryIds = [$params['blog_category_id']];
            $children = $this->BlogPosts->BlogCategories->find('children', for: $params['blog_category_id']);
            if ($children) {
                foreach($children as $child) {
                    $blogCategoryIds[] = $child->id;
                }
            }
            $conditions['BlogPosts.blog_category_id IN'] = $blogCategoryIds;
        }
        // カテゴリ名
        if ($params['category']) {
            $conditions = $this->createCategoryCondition(
                $conditions,
                $params['category'],
                $params['blog_content_id'],
                $params['contentUrl'],
                $params['force']
            );
        }
        // タグ名
        if ($params['tag']) {
            $query = $this->createTagCondition($query, $params['tag']);
        }
        // 年月日
        if ($params['year'] || $params['month'] || $params['day']) {
            $conditions = $this->createYearMonthDayCondition(
                $conditions,
                $params['year'],
                $params['month'],
                $params['day']
            );
        }
        // NO
        if (isset($params['no']) && $params['no']) {
            if (!$params['blog_content_id'] && !$params['contentUrl'] && !$params['force']) {
                trigger_error(__d('baser_core', 'blog_content_id を指定してください。'), E_USER_WARNING);
            }
            $conditions["BlogPosts.no"] = $params['no'];
        }
        // キーワード
        if ($params['keyword']) {
            $conditions = $this->createKeywordCondition($conditions, $params['keyword']);
        }
        return $query->where($conditions);
    }

    /**
     * Contains を select を前提として適用する
     * select を利用した場合、関連テーブルのカラムを指定しないと、取得できないため
     * @param Query $query
     * @param array $contains
     * @return Query
     * @noTodo
     * @checked
     */
    public function selectContains(Query $query, array $contains = [])
    {
        if(!$contains) $contains = $query->getContain();
        if(isset($contains['BlogContents'])) {
            $query->contain(['BlogContents' => function($q) {
                return $q->select($this->BlogPosts->BlogContents);
            }]);
        }
        if(isset($contains['BlogContents']['Contents'])) {
            $query->contain(['BlogContents.Contents' => function($q) {
                return $q->select($this->BlogPosts->BlogContents->Contents);
            }]);
        }
        if(isset($contains['Users'])) {
            $query->contain(['Users' => function($q) {
                return $q->select($this->BlogPosts->Users);
            }]);
        }
        if(isset($contains['BlogComments'])) {
            $query->contain(['BlogComments' => function($q) {
                return $q->select($this->BlogPosts->BlogComments);
            }]);
        }
        if(isset($contains['BlogCategories'])) {
            $query->contain(['BlogCategories' => function($q) {
                return $q->select($this->BlogPosts->BlogCategories);
            }]);
        }
        if(isset($contains['BlogTags'])) {
            $query->contain(['BlogTags' => function($q) {
                return $q->select($this->BlogPosts->BlogTags);
            }]);
        }
        return $query;
    }

    /**
     * カテゴリ条件を生成する
     *
     * @param array $conditions
     * @param string $category
     * @param int $blogContentId
     * @param bool $force
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createCategoryCondition(
        array $conditions,
        string $category,
        int $blogContentId = null,
        array|string $contentUrl = null,
        bool $force = false)
    {
        $categoryConditions = ['BlogCategories.name' => $category];
        if ($blogContentId) {
            $categoryConditions['BlogCategories.blog_content_id'] = $blogContentId;
        } elseif ($contentUrl) {
            $query = $this->BlogPosts->BlogContents->Contents->find()
                ->select(['Contents.entity_id']);

            if (is_array($contentUrl)) {
                $query->where(['Contents.url IN' => $contentUrl]);
            } else {
                $query->where(['Contents.url' => $contentUrl]);
            }

            $entityIds = Hash::extract($query->toArray(), '{n}.entity_id');
            if (count($entityIds) > 1) {
                $categoryConditions['BlogCategories.blog_content_id IN'] = $entityIds;
            } elseif(count($entityIds) === 1) {
                $categoryConditions['BlogCategories.blog_content_id'] = $entityIds[0];
            }
        } elseif (!$force) {
            trigger_error(__d('baser_core', 'blog_content_id を指定してください。'), E_USER_WARNING);
        }

        $categoryData = $this->BlogPosts->BlogCategories->find()->where($categoryConditions)->all()->toArray();
        $categoryIds = Hash::extract($categoryData, '{n}.id');
        if (!$categoryIds) {
            $categoryIds = false;
        } else {
            // 指定したカテゴリ名にぶら下がる子カテゴリを取得
            foreach($categoryIds as $categoryId) {
                $catChildren = $this->BlogPosts->BlogCategories->find('children', for: $categoryId)->all()->toArray();
                if ($catChildren) {
                    $categoryIds = array_merge($categoryIds, Hash::extract($catChildren, '{n}.id'));
                }
            }
        }
        if ($categoryIds !== false) {
            $conditions['BlogPosts.blog_category_id IN'] = $categoryIds;
        } else {
        	$conditions['BlogPosts.blog_category_id'] = false;
        }
        return $conditions;
    }

    /**
     * タグ条件を生成する
     *
     * @param Query $query
     * @param mixed $tag タグ（配列可）
     * @return QueryInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createTagCondition(Query $query, $tag): QueryInterface
    {
        if (!is_array($tag)) $tag = [$tag];
        foreach($tag as $key => $value) {
            $tag[$key] = rawurldecode($value);
        }
        $query->matching('BlogTags', function($q) use ($tag) {
            return $q->where(['BlogTags.name IN' => $tag]);
        });
        return $query;
    }

    /**
     * キーワード条件を生成する
     *
     * @param array $conditions
     * @param string $keyword
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createKeywordCondition($conditions, $keyword)
    {
        $keyword = str_replace('　', ' ', $keyword);
        if (strpos($keyword, ' ') !== false) {
            $keywords = explode(" ", $keyword);
        } else {
            $keywords = [$keyword];
        }
        foreach($keywords as $key => $value) {
            $value = h(rawurldecode($value));
            $conditions['and'][$key]['or'][] = ['BlogPosts.title LIKE' => "%{$value}%"];
            $conditions['and'][$key]['or'][] = ['BlogPosts.content LIKE' => "%{$value}%"];
            $conditions['and'][$key]['or'][] = ['BlogPosts.detail LIKE' => "%{$value}%"];
        }
        return $conditions;
    }

    /**
     * 年月日条件を生成する
     *
     * @param array $conditions
     * @param int $year
     * @param int $month
     * @param int $day
     * @return array
     * @checked
     * @unitTest
     */
    public function createYearMonthDayCondition($conditions, $year, $month, $day)
    {
        $driver = ConnectionManager::get('default')->config()['driver'];
        switch($driver) {
            case Mysql::class:
                if ($year) $conditions["YEAR(BlogPosts.posted)"] = $year;
                if ($month) $conditions["MONTH(BlogPosts.posted)"] = $month;
                if ($day) $conditions["DAY(BlogPosts.posted)"] = $day;
                break;
            case Postgres::class:
                if ($year) $conditions["date_part('year',BlogPosts.posted) = "] = $year;
                if ($month) $conditions["date_part('month',BlogPosts.posted) = "] = $month;
                if ($day) $conditions["date_part('day',BlogPosts.posted) = "] = $day;
                break;
            case Sqlite::class:
                if ($year) $conditions["strftime('%Y',BlogPosts.posted)"] = (string)$year;
                if ($month) $conditions["strftime('%m',BlogPosts.posted)"] = sprintf('%02d', $month);
                if ($day) $conditions["strftime('%d',BlogPosts.posted)"] = sprintf('%02d', $day);
                break;
        }
        return $conditions;
    }

    /**
     * 初期データ用のエンティティを取得
     *
     * @param int $userId
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $blogContentId, int $userId)
    {
        return $this->BlogPosts->newEntity([
            'user_id' => $userId,
            'posted' => \Cake\I18n\DateTime::now(),
            'status' => false,
            'blog_content_id' => $blogContentId
        ]);
    }

    /**
     * 新規登録
     *
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で {0} 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        if (!isset($postData['blog_content_id']) || empty($postData['blog_content_id'])) {
            throw new BcException(__d('baser_core',
                'blog_content_id を指定してください。',
            ));
        }
        $postData['no'] = $this->BlogPosts->getMax('no', ['BlogPosts.blog_content_id' => $postData['blog_content_id']]) + 1;
        $blogPost = $this->BlogPosts->patchEntity($this->BlogPosts->newEmptyEntity(), $postData);
        return $this->BlogPosts->saveOrFail($blogPost);
    }

    /**
     * ブログ記事を更新する
     *
     * POSTデータのサイズが設定ファイルで定義されたpost_max_sizeを超えた場合は例外処理される
     *
     * @param EntityInterface|BlogPost $post
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $post, array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $blogPost = $this->BlogPosts->patchEntity($post, $postData);
        return $this->BlogPosts->saveOrFail($blogPost);
    }

    /**
     * 公開状態を取得する
     *
     * @param EntityInterface $data モデルデータ
     * @return boolean 公開状態
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためテスト不要
     */
    public function allowPublish(EntityInterface $post)
    {
        return $this->BlogPosts->allowPublish($post);
    }

    /**
     * コントロールソースを取得する
     *
     * blog_category_id / user_id  / blog_tag_id を対象とする
     *
     * @param string $field
     * @param array $options
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, array $options = [])
    {
        switch($field) {
            case 'blog_category_id':
                $options = array_merge([
                    'blogContentId' => '',
                    'empty' => ''
                ], $options);
                /* @var \BcBlog\Service\BlogCategoriesServiceInterface $blogCategoriesService */
                $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);
                $catOption = [];
                if (!empty($options['blogContentId'])) $catOption = ['blogContentId' => $options['blogContentId']];
                $categories = $blogCategoriesService->getControlSource('parent_id', $catOption);
                if ($options['empty']) {
                    if ($categories) {
                        $categories = ['' => $options['empty']] + $categories;
                    } else {
                        $categories = ['' => $options['empty']];
                    }
                }
                return $categories;
            case 'user_id':
                return $this->BlogPosts->Users->getUserList($options);
            case 'blog_tag_id':
                return $this->BlogPosts->BlogTags->find('list')->toArray();
        }
        return false;
    }

    /**
     * 記事を公開状態に設定する
     *
     * 公開期間指定は初期化する
     *
     * @param int $id
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(int $id)
    {
        /* @var \BcBlog\Model\Entity\BlogPost $blogPost */
        $blogPost = $this->get($id);
        $blogPost->status = true;
        $blogPost->publish_begin = null;
        $blogPost->publish_end = null;
        return $this->BlogPosts->save($blogPost);
    }

    /**
     * 記事を非公開状態に設定する
     *
     * 公開期間指定は初期化する
     *
     * @param int $id
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(int $id)
    {
        /* @var \BcBlog\Model\Entity\BlogPost $blogPost */
        $blogPost = $this->get($id);
        $blogPost->status = false;
        $blogPost->publish_begin = null;
        $blogPost->publish_end = null;
        return $this->BlogPosts->save($blogPost);
    }

    /**
     * ブログ記事を削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $blogPost = $this->BlogPosts->get($id);
        $this->setupUpload($blogPost->blog_content_id);
        return $this->BlogPosts->delete($blogPost);
    }

    /**
     * ブログ記事をコピーする
     *
     * @param int $id
     * @return false|mixed
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップする
     */
    public function copy(int $id)
    {
        return $this->BlogPosts->copy($id);
    }

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitlesById(array $ids): array
    {
        return $this->BlogPosts->find('list')->select(['id', 'title'])->where(['BlogPosts.id IN' => $ids])->toArray();
    }

    /**
     * 一括処理
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
        $db = $this->BlogPosts->getConnection();
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

    /**
     * カテゴリ別記事一覧を取得
     *
     * @param string $category
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByCategory($category, array $options = [])
    {
        $options['category'] = $category;
        return $this->getIndex($options);
    }

    /**
     * 著者別記事一覧を取得
     *
     * @param int $userId
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByAuthor(int $userId, array $options = [])
    {
        $options['user_id'] = $userId;
        return $this->getIndex($options);
    }

    /**
     * タグ別記事一覧を取得
     *
     * @param string $tag
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByTag(string $tag, array $options = [])
    {
        $options['tag'] = $tag;
        return $this->getIndex($options);
    }

    /**
     * 日付別記事一覧を取得
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByDate(string $year, string $month, string $day, array $options = [])
    {
        if (!$year && !$month && !$day) throw new NotFoundException();
        $options = array_merge($options, [
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
        return $this->getIndex($options);
    }

    /**
     * 前の記事を取得する
     *
     * @param BlogPost $post ブログ記事
     * @return BlogPost|EntityInterface|null
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getPrevPost(BlogPost $post)
    {
        if (is_null($post->posted) || !$post->id) return null;
        $order = 'BlogPosts.posted DESC, BlogPosts.id DESC';
        // 投稿日が年月日時分秒が同一のデータの対応のため、投稿日が同じでIDが大きいデータを検索
        $conditions = array_merge_recursive([
            'BlogPosts.id <' => $post->id,
            'BlogPosts.posted' => $post->posted,
            'BlogPosts.blog_content_id' => $post->blog_content_id
        ], $this->BlogPosts->getConditionAllowPublish());
        $prevPost = $this->BlogPosts->find()
            ->where($conditions)
            ->order($order)
            ->first();

        if (!empty($prevPost)) return $prevPost;

        // 投稿日が新しいデータを取得
        $conditions = array_merge_recursive([
            'BlogPosts.posted <' => $post->posted,
            'BlogPosts.blog_content_id' => $post->blog_content_id,
        ], $this->BlogPosts->getConditionAllowPublish());
        return $this->BlogPosts->find()
            ->where($conditions)
            ->order($order)
            ->first();
    }

    /**
     * 次の記事を取得する
     *
     * @param BlogPost $post ブログ記事
     * @return BlogPost|EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNextPost(BlogPost $post)
    {
        if (is_null($post->posted) || !$post->id) return null;
        $order = 'BlogPosts.posted, BlogPosts.id';
        // 投稿日が年月日時分秒が同一のデータの対応のため、投稿日が同じでIDが小さいデータを検索
        $conditions = array_merge_recursive([
            'BlogPosts.id >' => $post->id,
            'BlogPosts.posted' => $post->posted,
            'BlogPosts.blog_content_id' => $post->blog_content_id
        ], $this->BlogPosts->getConditionAllowPublish());
        $nextPost = $this->BlogPosts->find()
            ->where($conditions)
            ->order($order)
            ->first();

        if (!empty($nextPost)) return $nextPost;

        // 投稿日が新しいデータを取得
        $conditions = array_merge_recursive([
            'BlogPosts.posted >' => $post->posted,
            'BlogPosts.blog_content_id' => $post->blog_content_id,
        ], $this->BlogPosts->getConditionAllowPublish());
        return $this->BlogPosts->find()
            ->where($conditions)
            ->order($order)
            ->first();
    }

    /**
     * 関連するブログ記事を取得する
     *
     * @param BlogPost $post
     * @param array $options
     * @return array|Query
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getRelatedPosts(BlogPost $post, $options = [])
    {
        if (empty($post->blog_tags)) return [];

        $options = array_merge([
            'limit' => 5,
            'order' => 'BlogPosts.posted DESC'
        ], $options);

        $tagNames = [];
        foreach($post->blog_tags as $tag) {
            $tagNames[] = rawurldecode($tag['name']);
        }
        $tags = $this->BlogPosts->BlogTags->find()->where(['BlogTags.name IN' => $tagNames])->contain('BlogPosts')->all()->toArray();

        $ids = array_unique(Hash::extract($tags, '{n}.blog_posts.{n}.id'));
        if (!$ids) return [];
        return $this->BlogPosts->find()->where(array_merge([
            ['BlogPosts.id IN ' => $ids],
            ['BlogPosts.id <>' => $post->id],
            'BlogPosts.blog_content_id' => $post->blog_content_id
        ], $this->BlogPosts->getConditionAllowPublish()))
            ->order($options['order'])
            ->limit($options['limit']);
    }

    /**
     * ブログ記事のURLを取得する
     *
     * @param Content $content
     * @param BlogPost $post
     * @param $full
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUrl(Content $content, BlogPost $post, $full)
    {
        /** @var SitesServiceInterface $sitesService */
        $sitesService = $this->getService(SitesServiceInterface::class);
        $site = $sitesService->findByUrl($content->url);
        /** @var ContentsServiceInterface $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $contentUrl = $contentsService->getUrl(rawurldecode($content->url), $full, !empty($site->use_subdomain), false);
        $no = ($post->name)?: $post->no;
        return $contentUrl . 'archives/' . $no;
    }

}
