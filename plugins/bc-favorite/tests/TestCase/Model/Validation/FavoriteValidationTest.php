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
use BaserCore\Test\Scenario\InitAppScenario;
use BcFavorite\Model\Validation\FavoriteValidation;
use BaserCore\TestSuite\BcTestCase;
use BcFavorite\Test\Scenario\FavoritesScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class FavoriteValidationTest
 * @property FavoriteValidation $FavoriteValidation
 */
class FavoriteValidationTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

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
        if ($isAdmin) {
            $this->loadFixtureScenario(InitAppScenario::class);
            $this->loginAdmin($this->getRequest('/'), $id);
        }
        $this->assertEquals($expected, $this->FavoriteValidation->isPermitted($url));
    }

    public static function isPermittedDataProvider()
    {
        return [
            [true, 1, '/baser/admin/users/index', true],
            [false, null, '/baser/admin/users/index', false],
        ];
    }
}
