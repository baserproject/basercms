<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Model;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogCategoriesTable;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\MultiSiteBlogScenario;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogCategoryTest
 * @property BlogCategoriesTable $BlogCategoriesTable
 */
class BlogCategoriesTableTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogCategoriesTable = $this->getTableLocator()->get('BcBlog.BlogCategories');
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        // テーブルは getTableLocator() 取得時に初期化済み。CakePHP 5.2 では initialize() を再呼び出しすると
        // 同名アソシエーションの重複設定で例外となるため、再呼び出しはせず初期化済みの状態を検証する。
        $this->assertEquals('blog_categories', $this->BlogCategoriesTable->getTable());
        $this->assertEquals('id', $this->BlogCategoriesTable->getPrimaryKey());
        $this->assertTrue($this->BlogCategoriesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->BlogCategoriesTable->hasBehavior('Tree'));
        $this->assertEquals('BlogPosts', $this->BlogCategoriesTable->getAssociation('BlogPosts')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        // id integer　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity(['id' => 'test']);
        $this->assertNotNull($blogCategory->getErrors()['id']);
        // id allowEmptyString　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity(['id' => '']);
        $errors = $blogCategory->getErrors();
        $this->assertFalse(isset($errors['id']));
        // name maxLength　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => '_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                . '_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                . '_test_blog_category_test_blog_category_test_blog_category_test_blog_category',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['maxLength' => 'カテゴリ名は255文字以内で入力してください。'],
        ], $blogCategory->getErrors());
        // name requirePresence　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['_required' => 'カテゴリ名を入力してください。'],
        ], $blogCategory->getErrors());
        // name notEmptyString　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => '',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['_empty' => 'カテゴリ名を入力してください。'],
        ], $blogCategory->getErrors());
        // name alphaNumericPlus　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => 'test 123',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['alphaNumericPlus' => 'カテゴリ名は半角英数字とハイフン、アンダースコアのみが利用可能です。'],
        ], $blogCategory->getErrors());
        // name duplicateBlogCategory　テスト
        BlogCategoryFactory::make([
            'name' => 'test',
            'blog_content_id' => 1,
            'title' => 'test'
        ])->persist();
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => 'test',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['duplicateBlogCategory' => '入力されたカテゴリ名は既に登録されています。'],
        ], $blogCategory->getErrors());
        // title maxLength　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'title' => '_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                . '_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                . '_test_blog_category_test_blog_category_test_blog_category_test_blog_category',
            'blog_content_id' => 1,
            'name' => 'test2'
        ]);
        $this->assertSame([
            'title' => ['maxLength' => 'カテゴリタイトルは255文字以内で入力してください。'],
        ], $blogCategory->getErrors());
        // title requirePresence　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'blog_content_id' => 1,
            'name' => 'test2'
        ]);
        $this->assertSame([
            'title' => ['_required' => 'カテゴリタイトルを入力してください。'],
        ], $blogCategory->getErrors());
        // title notEmptyString　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => 'test2',
            'blog_content_id' => 1,
            'title' => ''
        ]);
        $this->assertSame([
            'title' => ['_empty' => 'カテゴリタイトルを入力してください。'],
        ], $blogCategory->getErrors());
    }

    /*
	 * validate
	 */
    public function test必須チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        $this->BlogCategory->create([
            'BlogCategory' => [
                'blog_content_id' => 1
            ]
        ]);

        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリ名を入力してください。', current($this->BlogCategory->validationErrors['name']));

        $this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリタイトルを入力してください。', current($this->BlogCategory->validationErrors['title']));
    }

    public function test桁数チェック異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
                'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
            ]
        ]);
        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリ名は255文字以内で入力してください。', current($this->BlogCategory->validationErrors['name']));

        $this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリタイトルは255文字以内で入力してください。', current($this->BlogCategory->validationErrors['title']));
    }

    public function test桁数チェック正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
                'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
            ]
        ]);

        $this->assertTrue($this->BlogCategory->validates());
    }

    public function testその他異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        // 半角チェック
        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => 'テスト',
            ]
        ]);

        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリ名は半角のみで入力してください。', current($this->BlogCategory->validationErrors['name']));

        // 重複チェック
        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => 'release',
            ]
        ]);

        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('入力されたカテゴリ名は既に登録されています。', current($this->BlogCategory->validationErrors['name']));
    }

    /**
     * 同じニックネームのカテゴリがないかチェックする
     * 同じブログコンテンツが条件
     *
     * @dataProvider duplicateBlogCategoryDataProvider
     */
    public function testDuplicateBlogCategory($check, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogCategory->validationParams['blogContentId'] = 1;
        $result = $this->BlogCategory->duplicateBlogCategory($check);
        $this->assertEquals($result, $expected);
    }

    public static function duplicateBlogCategoryDataProvider()
    {
        return [
            [['id' => 0], true],
            [['id' => 1], false],
            [['name' => 'release'], false],
            [['title' => 'プレスリリース'], false],
            [['title' => '親子関係なしカテゴリ'], false],
            [['title' => 'hoge'], true],
        ];
    }

    /**
     * 関連する記事データをカテゴリ無所属に変更し保存する
     */
    public function testBeforeDelete()
    {
        $data = $this->BlogCategoriesTable->newEntity([
            'id' > '1'
        ]);
        $this->BlogCategoriesTable->delete($data);

        //テストメソッドをコール
        $blogCategories = $this->getTableLocator()->get('BcBlog.BlogCategories');
        $result = $blogCategories->find()->where(['id' => 1])->first();
        $this->assertEmpty($result);
    }

    /**
     * カテゴリリストを取得する
     */
    public function testGetCategoryList()
    {
        //データ準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'posted'=> '2015-01-27 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 2, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 2, 'user_id'=>1, 'status' => true])->persist();
        BlogCategoryFactory::make(['id' => 1, 'title' => 'title 1', 'name' => 'name-1', 'blog_content_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'parent_id'=> 1, 'title' => 'title 2', 'name' => 'name-2', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 3, 'parent_id'=> 2, 'title' => 'title 3', 'name' => 'name-3', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 4, 'title' => 'title 4', 'name' => 'name-4', 'blog_content_id' => 2])->persist();

        // 正常:　$blogContentId = null
        $result = $this->BlogCategoriesTable->getCategoryList();
        $this->assertCount(2, $result);

        // 正常:　$blogContentId = 1
        $result = $this->BlogCategoriesTable->getCategoryList(1);
        $this->assertCount(1, $result);

        // 存在しないID
        $result = $this->BlogCategoriesTable->getCategoryList(0, []);
        $this->assertEmpty($result);

        // option depth 2
        $result = $this->BlogCategoriesTable->getCategoryList(1, ['depth' => 2]);
        $this->assertEquals('name-2', $result->toArray()[0]->children->toArray()[0]->name);

        // option type year
        $result = $this->BlogCategoriesTable->getCategoryList(1, ['type' => 'year']);
        $this->assertEquals('name-2', $result['2015'][0]->name);

        // option viewCount true
        $result = $this->BlogCategoriesTable->getCategoryList(1, ['viewCount' => true]);
        $this->assertEquals(1, $result->toArray()[0]->count);

        // option limit true
        $result = $this->BlogCategoriesTable->getCategoryList(1, ['type' => 'year', 'limit' => 1, 'viewCount' => true]);
        $this->assertEquals(1, $result['2015'][0]->count);
    }

    /**
     * test _getCategoryList
     */
    public function test__getCategoryList()
    {
        //データ準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'posted'=> '2015-01-27 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 2, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 2, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 3, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 3, 'user_id'=>1, 'status' => true])->persist();
        BlogCategoryFactory::make(['id' => 1, 'title' => 'title 1', 'name' => 'name-1', 'blog_content_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'parent_id'=> 1, 'title' => 'title 2', 'name' => 'name-2', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 3, 'parent_id'=> 2, 'title' => 'title 3', 'name' => 'name-3', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 4, 'title' => 'title 4', 'name' => 'name-4', 'blog_content_id' => 2])->persist();

        // 正常:　$blogContentId = null
        $fields = [
            'BlogCategories.id',
            'BlogCategories.name',
            'BlogCategories.title',
            'BlogCategories.lft',
            'BlogCategories.rght'
        ];
        $result = $this->execPrivateMethod($this->BlogCategoriesTable, '_getCategoryList',
            [
                'fields' => $fields,
            ]);
        $this->assertCount(2, $result);

        // 正常:　$blogContentId = 1
        $result = $this->execPrivateMethod($this->BlogCategoriesTable, '_getCategoryList',
            [
                'blogContentId' => 1,
                'fields' => $fields,
            ]);
        $this->assertCount(1, $result);

        // 存在しないID
        $result = $this->execPrivateMethod($this->BlogCategoriesTable, '_getCategoryList',
            [
                'blogContentId' => 0,
                'fields' => $fields,
            ]);
        $this->assertEmpty($result);

        // option depth 2
        $result = $this->execPrivateMethod($this->BlogCategoriesTable, '_getCategoryList',
            [
                'blogContentId' => 1,
                'fields' => $fields,
                'depth' => 2,
            ]);
        $this->assertEquals('name-2', $result->toArray()[0]->children->toArray()[0]->name);
        // option viewCount true
        $result = $this->execPrivateMethod($this->BlogCategoriesTable, '_getCategoryList',
            [
                'blogContentId' => 1,
                'fields' => $fields,
                'depth' => 2,
                'viewCount' => true
            ]);
        $this->assertEquals(1, $result->toArray()[0]->count);

    }


    /**
     * アクセス制限としてカテゴリの新規追加ができるか確認する
     */
    public function testHasNewCategoryAddablePermission()
    {
        //データを生成

        //アクセス制限を持っているデータを生成
        UserFactory::make(['id' => 3])->persist();
        UserGroupFactory::make(['id' => 3])->persist();
        UsersUserGroupFactory::make(['id' => 3, 'user_id' => 3, 'user_group_id' => 3])->persist();
        PermissionFactory::make([
            'id' => 3,
            'name' => '新着情報記事管理',
            'user_group_id' => 3,
            'url' => '/baser/admin/bc-blog/*',
            'auth' => true,
            'status' => true
        ])->persist();

        //アクセス制限を持っていないデータを生成
        UserFactory::make(['id' => 2])->persist();
        UserGroupFactory::make(['id' => 2])->persist();
        UsersUserGroupFactory::make(['id' => 2, 'user_id' => 2, 'user_group_id' => 2])->persist();
        PermissionFactory::make([
            'id' => 2,
            'name' => '新着情報記事管理ブロック',
            'user_group_id' => 2,
            'url' => '/baser/admin/bc-blog/*',
            'auth' => false,
            'status' => true
        ])->persist();

        BlogCategoryFactory::make(['id' => 1])->persist();

        //アクセス制限を持っている検証
        $result = $this->BlogCategoriesTable->hasNewCategoryAddablePermission([3], 1);
        $this->assertTrue($result);

        //アクセス制限を持っていない検証
        $result = $this->BlogCategoriesTable->hasNewCategoryAddablePermission([2], 1);
        $this->assertFalse($result);
    }

    /**
     * 子カテゴリを持っているかどうか
     */
    public function testHasChild()
    {
        //データ生成
        $this->loadFixtureScenario(MultiSiteBlogScenario::class);
        //子カテゴリを持っていない場合
        $this->assertFalse($this->BlogCategoriesTable->hasChild(2));
        //子カテゴリを持っている場合
        $this->assertTrue($this->BlogCategoriesTable->hasChild(1));
    }

    /**
     * カテゴリ名よりカテゴリを取得する
     * @dataProvider getByNameDataProvider
     * @param int $blogCategoryId
     * @param string $name
     * @param bool $expects
     */
    public function testGetByName($blogCategoryId, $name, $expects)
    {
        //データ生成
        BlogCategoryFactory::make(['name' => 'child', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['name' => 'name', 'blog_content_id' => 2])->persist();
        $result = $this->BlogCategoriesTable->getByName($blogCategoryId, $name);
        //戻り値を確認
        $this->assertEquals($expects, (bool)$result);
    }

    public static function getByNameDataProvider()
    {
        return [
            [1, 'child', true],
            [1, 'hoge', false],
            [2, 'child', false]
        ];
    }

    /**
     * test beforeCopyEvent
     */
    public function testBeforeCopyEvent()
    {
        BlogCategoryFactory::make(['id' => 1, 'name' => 'test', 'title' => 'title', 'blog_content_id' => 1])->persist();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BcBlog.BlogCategories.beforeCopy', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeCopy';
            $event->setData('data', $data);
        });

        $this->BlogCategoriesTable->copy(1);
        //イベントに入るかどうか確認
        $blogCategories = $this->getTableLocator()->get('BcBlog.BlogCategories');
        $query = $blogCategories->find()->where(['name' => 'beforeCopy_copy']);
        $this->assertEquals(1, $query->count());
    }


    /**
     * test copy
     */
    public function test_copy()
    {
        //準備
        BlogCategoryFactory::make(['id' => 1, 'name' => 'test', 'title' => 'title', 'blog_content_id' => 1])->persist();
        //正常系実行
        $this->BlogCategoriesTable->copy(1);
        $query = $this->BlogCategoriesTable->find()->where(['name' => 'test_copy']);
        $this->assertEquals(1, $query->count());
    }


    /**
     * test AfterCopyEvent
     */
    public function testAfterCopyEvent()
    {
        BlogCategoryFactory::make(['id' => 1, 'name' => 'test', 'title' => 'title', 'blog_content_id' => 1])->persist();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BcBlog.BlogCategories.afterCopy', function (Event $event) {
            $data = $event->getData('data');
            $blogCategories = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
            $data->name = 'afterAdd';
            $blogCategories->save($data);
        });
        $this->BlogCategoriesTable->copy(1);
        //イベントに入るかどうか確認
        $blogCategories = $this->getTableLocator()->get('BcBlog.BlogCategories');
        $query = $blogCategories->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test getParent
     */
    public function test_getParent()
    {
        //データ生成
        BlogCategoryFactory::make(['id' => 11])->persist();
        //テストメソッドをコール
        $rs = $this->BlogCategoriesTable->getParent(11);
        //戻り値を確認
        $this->assertEquals(11, $rs->id);
    }

    /**
     * test getOrderSameParent
     *
     * lft/rght は全ブログ横断の単一フォレストのため、ルート階層では
     * 他ブログのルートカテゴリも含めた順位を返すことを確認する
     */
    public function test_getOrderSameParent()
    {
        // ブログ1・ブログ2 のルートカテゴリが lft 順で交錯するデータ
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'category-a', 'title' => 'A', 'parent_id' => null, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 2, 'name' => 'category-b', 'title' => 'B', 'parent_id' => null, 'lft' => 3, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 1, 'name' => 'category-c', 'title' => 'C', 'parent_id' => null, 'lft' => 5, 'rght' => 8])->persist();
        BlogCategoryFactory::make(['id' => 4, 'blog_content_id' => 1, 'name' => 'child1', 'title' => 'C1', 'parent_id' => 3, 'lft' => 6, 'rght' => 7])->persist();

        // ルート階層：他ブログのルートも含めたグローバル順位を返す
        $this->assertEquals(1, $this->BlogCategoriesTable->getOrderSameParent(1, null));
        $this->assertEquals(2, $this->BlogCategoriesTable->getOrderSameParent(2, null));
        $this->assertEquals(3, $this->BlogCategoriesTable->getOrderSameParent(3, null));

        // id 未指定はルート階層の件数（＝末尾の位置）
        $this->assertEquals(3, $this->BlogCategoriesTable->getOrderSameParent(null, null));

        // 子階層：親配下の順位を返す
        $this->assertEquals(1, $this->BlogCategoriesTable->getOrderSameParent(4, 3));
        $this->assertEquals(1, $this->BlogCategoriesTable->getOrderSameParent(null, 3));

        // 対象親に子が無い場合は false
        $this->assertFalse($this->BlogCategoriesTable->getOrderSameParent(null, 1));

        // リストに存在しない id は null
        $this->assertNull($this->BlogCategoriesTable->getOrderSameParent(4, null));
    }

    /**
     * test moveOffset
     */
    public function test_moveOffset()
    {
        // 同一階層（ルート）の兄弟を 3 件生成（入れ子集合として正しい lft/rght を指定）
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'category-a', 'title' => 'A', 'parent_id' => null, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'category-b', 'title' => 'B', 'parent_id' => null, 'lft' => 3, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 1, 'name' => 'category-c', 'title' => 'C', 'parent_id' => null, 'lft' => 5, 'rght' => 6])->persist();

        // C を 1 つ上に移動（A, C, B の順になる）
        $result = $this->BlogCategoriesTable->moveOffset(3, -1);
        $this->assertNotFalse($result);
        $names = $this->BlogCategoriesTable->find()->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['category-a', 'category-c', 'category-b'], $names);

        // A を 2 つ下に移動（C, B, A の順になる）
        $this->BlogCategoriesTable->moveOffset(1, 2);
        $names = $this->BlogCategoriesTable->find()->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['category-c', 'category-b', 'category-a'], $names);

        // オフセット 0 は並び順を変えず、対象エンティティを返す
        $result = $this->BlogCategoriesTable->moveOffset(2, 0);
        $this->assertNotFalse($result);
        $this->assertEquals(2, $result->id);
        $names = $this->BlogCategoriesTable->find()->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['category-c', 'category-b', 'category-a'], $names);
    }

    /**
     * test moveOffset 親子階層（サブツリーごと移動する）
     */
    public function test_moveOffset_withChildren()
    {
        // ルート parent1(1,4) にその子 child1(2,3)、ルート parent2(5,6) の入れ子集合
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'parent1', 'title' => 'P1', 'parent_id' => null, 'lft' => 1, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'child1', 'title' => 'C1', 'parent_id' => 1, 'lft' => 2, 'rght' => 3])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 1, 'name' => 'parent2', 'title' => 'P2', 'parent_id' => null, 'lft' => 5, 'rght' => 6])->persist();

        // parent1 を 1 つ下へ → 子 child1 も一緒に移動し、parent2, parent1, child1 の順になる
        $this->BlogCategoriesTable->moveOffset(1, 1);
        $names = $this->BlogCategoriesTable->find()->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['parent2', 'parent1', 'child1'], $names);

        // 子が親の直下に留まっている（入れ子集合が壊れていない）ことを確認
        $parent1 = $this->BlogCategoriesTable->get(1);
        $child1 = $this->BlogCategoriesTable->get(2);
        $this->assertEquals(1, $child1->parent_id);
        $this->assertGreaterThan($parent1->lft, $child1->lft);
        $this->assertLessThan($parent1->rght, $child1->rght);
    }

    /**
     * test resetTree
     *
     * 複数ブログ・階層ありのデータが、全てルート直下にフラット化され
     * lft / rght が元の並び順（lft 昇順）の連番で振り直されることを確認する
     */
    public function test_resetTree()
    {
        // blog1: parent1(1,4) > child1(2,3)、blog2: B(5,6)、blog1: C(lft/rght 破損)
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'parent1', 'title' => 'P1', 'parent_id' => null, 'lft' => 1, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'child1', 'title' => 'C1', 'parent_id' => 1, 'lft' => 2, 'rght' => 3])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 2, 'name' => 'category-b', 'title' => 'B', 'parent_id' => null, 'lft' => 5, 'rght' => 6])->persist();
        BlogCategoryFactory::make(['id' => 4, 'blog_content_id' => 1, 'name' => 'category-c', 'title' => 'C', 'parent_id' => null, 'lft' => 7, 'rght' => 7])->persist();

        $this->assertTrue($this->BlogCategoriesTable->resetTree());

        // 全カテゴリがルート直下・元の lft 昇順の連番になっている
        $categories = $this->BlogCategoriesTable->find()->orderBy(['lft'])->all()->toArray();
        $this->assertEquals(['parent1', 'child1', 'category-b', 'category-c'], array_column($categories, 'name'));
        $count = 0;
        foreach($categories as $category) {
            $this->assertNull($category->parent_id);
            $this->assertEquals(++$count, $category->lft);
            $this->assertEquals(++$count, $category->rght);
        }
    }
}
