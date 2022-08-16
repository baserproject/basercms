<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcFavorite\Test\TestCase\Model\Validation;

use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BcFavorite\Model\Validation\FavoriteValidation;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class FavoriteValidationTest
 * @package BcFavorite\Test\TestCase\Model\Validation
 * @property FavoriteValidation $FavoriteValidation
 */
class FavoriteValidationTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
         'plugin.BaserCore.Users',
         'plugin.BaserCore.UsersUserGroups',
         'plugin.BaserCore.UserGroups',
         'plugin.BaserCore.Plugins',
         'plugin.BaserCore.Permissions',
    ];

    /**
     * Test subject
     *
     * @var FavoriteValidation
     */
    public $FavoriteValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->FavoriteValidation = new FavoriteValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FavoriteValidation);
        parent::tearDown();
    }

    /**
     * test isPermitted
     *
     * @return void
     * @dataProvider isPermittedDataProvider
     */
    public function testIsPermitted($isAdmin, $id, $url, $expected): void
    {
        if($isAdmin) {
            $this->loginAdmin($this->getRequest('/'), $id);
        }
        $this->assertEquals($expected, $this->FavoriteValidation->isPermitted($url, $this->getService(PermissionServiceInterface::class)));
    }

    public function isPermittedDataProvider()
    {
        return [
            [true, 1, '/baser/admin/users/index', true],
            [true, 2, '/baser/admin/users/index', false],
            [true, 2, '/baser/admin/pages/index', true],
            [false, null, '/baser/admin/users/index', false],
        ];
    }
}
