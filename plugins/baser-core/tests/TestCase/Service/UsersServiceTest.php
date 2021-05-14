<?php
namespace BaserCore\Test\TestCase\Service;
use BaserCore\Service\UsersService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UsersServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UsersService $Users
 */
class UsersServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups'
    ];

    /**
     * @var UsersService|null
     */
    public $Users = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Users = new UsersService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        parent::tearDown();
    }

    /**
     * Test getNew
     */
    public function testGetNew()
    {
        $this->assertEquals(1, $this->Users->getNew()->user_groups[0]->id);
    }

}
