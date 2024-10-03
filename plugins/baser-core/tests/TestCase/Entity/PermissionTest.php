<?php

namespace BaserCore\Test\TestCase\Entity;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\TestSuite\BcTestCase;

class PermissionTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
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
        PermissionGroupFactory::make(['id' => 1,'type' => $type])->persist();
        $permission = PermissionFactory::make(['permission_group_id' => 1])->persist();

        $result = $this->execPrivateMethod($permission, '_getPermissionGroupType');
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
