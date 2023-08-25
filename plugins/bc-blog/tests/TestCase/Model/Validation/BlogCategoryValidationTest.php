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

namespace BcBlog\Test\TestCase\Model\Validation;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogCategoriesTable;
use BcBlog\Model\Validation\BlogCategoryValidation;
use BcBlog\Test\Factory\BlogCategoryFactory;

/**
 * Class BlogCategoryTest
 *
 * @property BlogCategoryValidation $BlogCategoryValidation
 */
class BlogCategoryValidationTest extends BcTestCase
{

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogCategoryValidation = new BlogCategoryValidation();
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
     * Test duplicateBlogCategory
     *
     * @param $value
     * @param $context
     * @param $insertData
     * @param $expected
     * @return void
     * @dataProvider duplicateBlogCategoryDataProvider
     */
    public function testDuplicateBlogCategory($value, $context, $insertData, $expected): void
    {
        if ($insertData) {
            BlogCategoryFactory::make($insertData)->persist();
        }
        $this->assertEquals($expected, $this->BlogCategoryValidation::duplicateBlogCategory($value, $context));
    }

    public function duplicateBlogCategoryDataProvider(): array
    {
        return [
            [
                'test',
                [
                    'providers' => ['table' => new BlogCategoriesTable()],
                    'data' => ['id' => 100, 'blog_content_id' => 1],
                    'field' => 'name',
                    'newRecord' => true
                ],
                null,
                true
            ],
            [
                'test',
                [
                    'providers' => ['table' => new BlogCategoriesTable()],
                    'data' => ['id' => 100, 'blog_content_id' => 1],
                    'field' => 'name',
                    'newRecord' => true
                ],
                [
                    'id' => 100,
                    'name' => 'test',
                    'title' => 'test',
                    'blog_content_id' => 1
                ],
                false
            ],
            [
                'test',
                [
                    'providers' => ['table' => new BlogCategoriesTable()],
                    'data' => ['id' => 100, 'blog_content_id' => 1],
                    'field' => 'name',
                    'newRecord' => false
                ],
                [
                    'id' => 100,
                    'name' => 'test',
                    'title' => 'test',
                    'blog_content_id' => 1
                ],
                true
            ],
        ];
    }

}
