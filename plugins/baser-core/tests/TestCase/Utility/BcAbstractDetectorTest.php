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

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcAgent;
use Cake\Core\Configure;

/**
 * Class BcAbstractDetector
 *
 */
class BcAbstractDetectorTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test find
     */
    public function testFind()
    {
        Configure::write("BcApp.smartphone", true);

        //Configureにnameがある場合、
        $rs = BcAgent::find('smartphone');
        $this->assertEquals('smartphone', $rs->name);
        $this->assertEquals('device', $rs->type);

        //Configureにnameがない場合、
        $this->assertNull(BcAgent::find('test'));
    }

    /**
     * test findAll
     */
    public function testFindAll()
    {
        Configure::write("BcEnv.isInstalled", false);
        Configure::write("BcApp.smartphone", true);
        Configure::write("BcApp.BcAgent", true);

        //isInstalled=false
        $agents = BcAgent::findAll();
        $this->assertCount(0, $agents);

        //isInstalled=true
        Configure::write("BcEnv.isInstalled", true);
        $agents = BcAgent::findAll();
        $this->assertCount(2, $agents);
    }

    /**
     * test findCurrent
     * @param string $agent ユーザーエージェント名
     * @param string $expect 期待値
     *
     * @dataProvider findCurrentDataProvider
     */
    public function testFindCurrent($agent, $expect)
    {
        $_SERVER["HTTP_USER_AGENT"] = $agent;
        $result = BcAgent::findCurrent();
        if (is_null($expect)) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expect, $result->name);
        }
    }

    public static function findCurrentDataProvider(): array
    {
        return [
            ['Googlebot-Mobile', 'mobile'],
            ['DoCoMo', 'mobile'],
            ['iPhone', 'smartphone'],
            ['hoge', null],
        ];
    }
}
