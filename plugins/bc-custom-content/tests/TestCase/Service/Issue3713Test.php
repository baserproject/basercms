<?php
namespace BcCustomContent\Test\TestCase\Service;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;

class Issue3713Test extends BcTestCase
{
    use BcContainerTrait;

    public function testSharedTitleColumnDeletion(): void
    {
        $database = $this->getService(BcDatabaseServiceInterface::class);
        $tables = $this->getService(CustomTablesServiceInterface::class);
        $links = $this->getService(CustomLinksServiceInterface::class);
        $tables->create([
            'id' => 1,
            'name' => 'issue3713',
            'title' => 'Isolated synthetic table',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0,
        ]);
        $tableName = 'custom_entry_1_issue3713';
        $this->assertTrue($database->columnExists($tableName, 'title'));
        $validation = $links->CustomLinks->getValidator('default')->validate([
            'custom_table_id' => 1,
            'name' => 'title',
            'title' => 'Collision',
        ]);
        $this->assertArrayNotHasKey('name', $validation);
        CustomFieldFactory::make([
            'id' => 101,
            'name' => 'title',
            'title' => 'Collision',
            'type' => 'text',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 101,
            'custom_table_id' => 1,
            'custom_field_id' => 101,
            'name' => 'title',
            'title' => 'Collision',
        ])->persist();
        $this->assertTrue($links->delete(101));
        $this->assertTrue($database->columnExists($tableName, 'title'));
        $this->assertFalse($links->CustomLinks->exists(['id' => 101]));
        $database->dropTable($tableName);
    }
}
