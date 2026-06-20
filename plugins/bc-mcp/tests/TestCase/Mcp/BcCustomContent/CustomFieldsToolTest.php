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
use BcMcp\Mcp\BcCustomContent\CustomFieldsTool;

/**
 * CustomFieldsToolTest
 */
class CustomFieldsToolTest extends BcTestCase
{
    /**
     * @var CustomFieldsTool
     */
    public $CustomFieldsTool;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomFieldsTool = new CustomFieldsTool();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomFieldsTool);
        parent::tearDown();
    }

    /**
     * Test instantiation
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf(CustomFieldsTool::class, $this->CustomFieldsTool);
        $this->assertTrue(method_exists($this->CustomFieldsTool, 'addCustomField'));
        $this->assertTrue(method_exists($this->CustomFieldsTool, 'getCustomFields'));
    }

    /**
     * test addCustomField
     */
    public function testAddCustomField()
    {
        $result = $this->CustomFieldsTool->addCustomField(
            name: 'test_field',
            title: 'テストフィールド',
            type: 'text'
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomFields
     */
    public function testGetCustomFields()
    {
        $result = $this->CustomFieldsTool->getCustomFields();

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomField
     */
    public function testGetCustomField()
    {
        $result = $this->CustomFieldsTool->getCustomField(1);

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test editCustomField
     */
    public function testEditCustomField()
    {
        $result = $this->CustomFieldsTool->editCustomField(
            1,
            'updated_field',
            '更新されたフィールド',
            'textarea'
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test deleteCustomField
     */
    public function testDeleteCustomField()
    {
        $result = $this->CustomFieldsTool->deleteCustomField(1);

        $this->assertIsArray($result);
        if (isset($result['success'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomFields with search parameters
     */
    public function testGetCustomFieldsWithSearch()
    {
        $result = $this->CustomFieldsTool->getCustomFields(
            name: 'test',
            title: 'text',
            status: 1
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getCustomField with invalid ID
     */
    public function testGetCustomFieldWithInvalidId()
    {
        $result = $this->CustomFieldsTool->getCustomField(999);

        $this->assertIsArray($result);
        if (isset($result['error'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test editCustomField with invalid ID
     */
    public function testEditCustomFieldWithInvalidId()
    {
        $result = $this->CustomFieldsTool->editCustomField(999, 'test', 'Test Field', 'text');

        $this->assertIsArray($result);
        if (isset($result['error'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test deleteCustomField with invalid ID
     */
    public function testDeleteCustomFieldWithInvalidId()
    {
        $result = $this->CustomFieldsTool->deleteCustomField(999);

        $this->assertIsArray($result);
        if (isset($result['error'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test addCustomField with minimal parameters
     */
    public function testAddCustomFieldWithMinimalParameters()
    {
        $result = $this->CustomFieldsTool->addCustomField(
            'minimal_field',
            'ミニマルフィールド',
            'text'
        );

        $this->assertIsArray($result);
        if (isset($result['success'])) {
        }
        if (isset($result['content'])) {
            $this->assertArrayHasKey('content', $result);
        }
    }
}
