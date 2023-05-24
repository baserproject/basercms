<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcEditorTemplate\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcEditorTemplate\Model\Table\EditorTemplatesTable;

/**
 * Class EditorTemplatesTable
 *
 * @property EditorTemplatesTable $EditorTemplatesTable
 */
class EditorTemplatesTableTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->EditorTemplatesTable = $this->getTableLocator()->get('BcEditorTemplate.EditorTemplates');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->EditorTemplatesTable);
        parent::tearDown();
    }

    /**
     * validate
     */
    public function test_initialize()
    {
        $this->assertEquals('editor_templates', $this->EditorTemplatesTable->getTable());
        $this->assertEquals('id', $this->EditorTemplatesTable->getPrimaryKey());
        $this->assertTrue($this->EditorTemplatesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->EditorTemplatesTable->hasBehavior('BcUpload'));
    }

    /**
     * validate
     */
    public function test_validationDefault()
    {
        $this->markTestIncomplete('このテストは、未確認。');
        $this->EditorTemplate->create([
            'EditorTemplate' => [
                'name' => '',
                'link' => '',
            ]
        ]);
        $this->assertFalse($this->EditorTemplate->validates());
        $this->assertArrayHasKey('name', $this->EditorTemplate->validationErrors);
        $this->assertEquals('テンプレート名を入力してください。', current($this->EditorTemplate->validationErrors['name']));
    }

}
