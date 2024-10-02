<?php

namespace BaserCore\Test\TestCase\Entity;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\TestSuite\BcTestCase;

class PermissionTest extends BcTestCase
{
    protected $permissionGroup;
    protected $permission;
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionGroup = PermissionGroupFactory::make()->persist();
        $this->permission = PermissionFactory::make(['permission_group_id' => $this->permissionGroup->id])->persist();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test _getPermissionGroupType
     * @param $type
     * @param $expected
     * @dataProvider permissionGroupDataProvider
     */
    public function testGetPermissionGroupType($type, $expected)
    {
        $this->permissionGroup = PermissionGroupFactory::make(['type' => $type])->persist();
        $this->permission = PermissionFactory::make(['permission_group_id' => $this->permissionGroup->id])->persist();

        $result = $this->execPrivateMethod($this->permission, '_getPermissionGroupType');
        $this->assertEquals($expected, $result);
    }

    public static function permissionGroupDataProvider()
    {
        return [
            ['Admin', 'Admin'],
            [null, null],
        ];
    }

}
