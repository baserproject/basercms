<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Test\TestCase\Identifier\Resolver;

use BaserCore\Identifier\Resolver\PrefixOrmResolver;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class PrefixOrmResolverTest
 * @property PrefixOrmResolver $PrefixOrmResolver
 *
 */
class PrefixOrmResolverTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PrefixOrmResolver = new PrefixOrmResolver();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testFind(): void
    {
        //準備
        UserFactory::make(['id' => 1, 'name' => 'user_test'])->persist();
        $this->PrefixOrmResolver->setConfig('prefix', 'test');

        //正常テスト
        $rs = $this->PrefixOrmResolver->find(['id' => 'test_1']);
        $this->assertEquals('user_test', $rs->name);

        //異常テスト　
        $rs = $this->PrefixOrmResolver->find(['id' => 'user_1']);
        $this->assertNull($rs);
    }
}
