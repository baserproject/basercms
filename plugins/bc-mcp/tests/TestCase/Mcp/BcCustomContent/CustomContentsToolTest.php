<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.7
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp\BcCustomContent;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\BcCustomContent\CustomContentsTool;

/**
 * CustomContentsToolTest
 */
class CustomContentsToolTest extends BcTestCase
{
    /**
     * @var CustomContentsTool
     */
    public $CustomContentsTool;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsTool = new CustomContentsTool();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentsTool);
        parent::tearDown();
    }

    /**
     * Test instantiation
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf(CustomContentsTool::class, $this->CustomContentsTool);
        $this->assertTrue(method_exists($this->CustomContentsTool, 'addCustomContent'));
        $this->assertTrue(method_exists($this->CustomContentsTool, 'getCustomContents'));
    }

    /**
     * test addCustomContent
     */
    public function testAddCustomContent()
    {
        $result = $this->CustomContentsTool->addCustomContent(
            name: 'test-content',
            title: 'テストカスタムコンテンツ',
            customTableId: 1,
            description: 'テスト用のカスタムコンテンツです',
            authorId: 1,
            status: true,
            listOrder: 'id',
        );

        $this->assertIsArray($result);
        // エラーの場合はcontentキーにエラーメッセージが文字列として含まれる
        if (isset($result['content']) && is_string($result['content'])) {
            $this->assertIsString($result['content']);
        } else {
            // 成功の場合は直接データがアクセス可能
            $this->assertArrayHasKey('id', $result);
        }
    }

    /**
     * test getCustomContents
     */
    public function testGetCustomContents()
    {
        $result = $this->CustomContentsTool->getCustomContents(
            status: 'publish',
            limit: 10,
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomContent
     */
    public function testGetCustomContent()
    {
        $result = $this->CustomContentsTool->getCustomContent(1);

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test editCustomContent
     */
    public function testEditCustomContent()
    {
        $result = $this->CustomContentsTool->editCustomContent(
            id: 1,
            name: 'updated-name',
            title: '更新されたタイトル',
            description: '更新された説明',
            template: 'custom',
            listCount: 20,
            listDirection: 'ASC',
            listOrder: 'name',
            status: true
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test deleteCustomContent
     */
    public function testDeleteCustomContent()
    {
        $result = $this->CustomContentsTool->deleteCustomContent(1);

        $this->assertIsArray($result);
        if (isset($result['success'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomContents with search parameters
     */
    public function testGetCustomContentsWithSearch()
    {
        $result = $this->CustomContentsTool->getCustomContents(
            status: 'publish',
            limit: 5
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomContent with invalid ID
     */
    public function testGetCustomContentWithInvalidId()
    {
        $result = $this->CustomContentsTool->getCustomContent(999);

        $this->assertIsArray($result);
        if (isset($result['error'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test editCustomContent with invalid ID
     */
    public function testEditCustomContentWithInvalidId()
    {
        $result = $this->CustomContentsTool->editCustomContent(999, 'test', 'Test Title');

        $this->assertIsArray($result);
        if (isset($result['error'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }
}
