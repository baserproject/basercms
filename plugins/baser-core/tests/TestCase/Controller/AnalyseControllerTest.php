<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.3.2
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Controller\AnalyseController;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class AnalyseControllerTest
 */
class AnalyseControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    /**
     * test beforeFilter - デバッグモードが無効な場合は 404 になること
     *
     * 全ソースのパス・クラス・メソッド・開発メモを JSON で返す開発用の機能であり、
     * 未認証で到達できるため、本番環境（debug=false）では公開しない。
     */
    public function test_beforeFilter_notFoundWhenDebugOff()
    {
        $debug = Configure::read('debug');
        try {
            $controller = new AnalyseController($this->getRequest('/baser-core/analyse/index.json'));
            Configure::write('debug', false);
            try {
                $controller->beforeFilter(new Event('beforeFilter'));
                $this->fail('debug=false で拒否されていません');
            } catch (NotFoundException $e) {
                $this->assertEquals('見つかりませんでした。', $e->getMessage());
            }
            Configure::write('debug', true);
            $controller = new AnalyseController($this->getRequest('/baser-core/analyse/index.json'));
            $controller->beforeFilter(new Event('beforeFilter'));
            $this->assertTrue(true);
        } finally {
            Configure::write('debug', $debug);
        }
    }

}
