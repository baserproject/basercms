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

use Cake\Core\App;
use Cake\Cache\Cache;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use BaserCore\Utility\BcUtil;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcAgent;

/**
 * Class BcAgentTest
 *
 * @package Baser.Test.Case.Lib
 */
class BcAgentTest extends BcTestCase
{
    /**
     * @var BcAgent
     */
    public $agent;

    /**
     * set up
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        // config
        $this->config =  [
            'alias' => 's',
            'prefix' => 'smartphone',
            'autoRedirect' => true,
            'autoLink' => true,
            'agents' => [
                'iPhone',            // Apple iPhone
                'iPod',                // Apple iPod touch
                'Android',            // 1.5+ Android
                'dream',            // Pre 1.5 Android
                'CUPCAKE',            // 1.5+ Android
                'blackberry9500',    // Storm
                'blackberry9530',    // Storm
                'blackberry9520',    // Storm v2
                'blackberry9550',    // Storm v2
                'blackberry9800',    // Torch
                'webOS',            // Palm Pre Experimental
                'incognito',        // Other iPhone browser
                'webmate'            // Other iPhone browser
            ]
        ];
        $this->agent = new BcAgent('smartphone', $this->config);
        Configure::write("BcApp.smartphone", true);
    }

    /**
     * test _setConfig
     */
    public function test_setConfig(): void
    {
        $agent = new BcAgent('smartphone', $this->config);
        $this->assertEquals($this->config['agents'][0], $agent->decisionKeys[0]);
    }

    /**
     * test _getDefaultConfig
     *
     */
    public function test_getDefaultConfig(): void
    {
        $agent = new BcAgent('smartphone', []);
        $this->assertFalse($agent->sessionId);
    }

    /**
     * test getDetectorRegex
     */
    public function testGetDetectorRegex(): void
    {
        $expectRegex = '/' . implode('|', $this->config['agents']) . '/i';
        $this->assertEquals($expectRegex, $this->agent->getDetectorRegex());
        $this->assertNotEquals('', $this->agent->getDetectorRegex());
    }

    /**
     * test BcAbstractDetector find
     * @param string $name 名前
     *
     * @dataProvider findDataProvider
     */
    public function testFind($name): void
    {
        $result = $this->agent->find($name);
        if (!is_null($result)) {
            $this->assertEquals($name, $result->name, '設定を正しく読み込めません');
        } else {
            $this->assertNull($result, '存在しないユーザーエージェント名で設定が読み込まれています');
        }
    }

    public function findDataProvider(): array
    {
        return [
            ['mobile'],
            ['smartphone'],
            ['hoge'],
        ];
    }

    /**
     * test BcAbstractDetector findAll
     */
    public function testFindAll(): void
    {
        $result = $this->agent->findAll();

        $mobile = new BcAgent('mobile', [
            'alias' => 'm',
            'prefix' => 'mobile',
            'autoRedirect' => true,
            'autoLink' => true,
            'agents' => [
                'Googlebot-Mobile',
                'Y!J-SRD',
                'Y!J-MBS',
                'DoCoMo',
                'SoftBank',
                'Vodafone',
                'J-PHONE',
                'UP.Browser'
            ],
            'sessionId' => true
        ]);

        $expect = [
            $mobile,
            $this->agent
        ];

        $this->assertEquals($expect, $result, '設定ファイルに存在するすべてのインスタンスを正しく返すことができません');
    }

    /**
     * test BcAbstractDetector findCurrent
     * @param string $agent ユーザーエージェント名
     * @param string $expect 期待値
     *
     * @dataProvider findCurrentDataProvider
     */
    public function testFindCurrent($agent, $expect): void
    {
        $_SERVER["HTTP_USER_AGENT"] = $agent;
        $result = $this->agent->findCurrent();

        if (!is_null($result)) {
            $this->assertEquals($expect, $result->name, '設定を正しく読み込めません');
        } else {
            $this->assertNull($result, '存在しないユーザーエージェント名で設定が読み込まれています');
        }
    }

    public function findCurrentDataProvider(): array
    {
        return [
            ['Googlebot-Mobile', 'mobile'],
            ['DoCoMo', 'mobile'],
            ['iPhone', 'smartphone'],
            ['hoge', null],
        ];
    }

    /**
     * test isMatchDecisionKey
     * @param bool $expect 期待値
     * @param string $userAgent ユーザーエージェントの文字列
     *
     * @dataProvider isMatchDecisionKeyDataProvider
     */
    public function testIsMatchDecisionKey($expect, $userAgent): void
    {
        $_SERVER['HTTP_USER_AGENT'] = $userAgent;
        $this->assertEquals($expect, $this->agent->isMatchDecisionKey());
    }

    public function isMatchDecisionKeyDataProvider(): array
    {
        return [
            [true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'],
            [true, 'iPod'],
            [true, 'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19'],
            [true, 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; LT28at Build/6.1.C.1.111) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30'],
            [false, 'DoCoMo']
        ];
    }
}
