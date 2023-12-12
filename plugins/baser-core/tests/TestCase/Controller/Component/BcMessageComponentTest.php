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

namespace BaserCore\Test\TestCase\Controller\Component;

use BaserCore\Model\Table\DblogsTable;
use BaserCore\Test\Scenario\DblogsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use BaserCore\Controller\Component\BcMessageComponent;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcMessageTestController
 *
 * @property BcMessageComponent $BcMessage
 */
class BcMessageTestController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
    }
}

/**
 * BaserCore\Controller\BcMessageComponent Test Case
 * @property BcMessageComponent $BcMessage
 */
class BcMessageComponentTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(DblogsScenario::class);
        $this->getRequest();
        $this->Controller = new BcMessageTestController($this->getRequest());
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->BcMessage = new BcMessageComponent($this->ComponentRegistry);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($_SESSION);
        parent::tearDown();
    }

    /**
     * Test set
     * @return void
     */
    public function testSet()
    {
        // notice message
        $this->BcMessage->set('test');
        $this->assertEquals('test', $_SESSION['Flash']['flash'][0]['message']);
        $this->assertEquals('notice-message', $_SESSION['Flash']['flash'][0]['params']['class']);

        // multi message
        $this->BcMessage->set('test');
        $this->assertEquals(2, count($_SESSION['Flash']['flash']));
        $_SESSION['Flash'] = null;

        // alert message
        $this->BcMessage->set('test', true);
        $this->assertEquals('alert-message', $_SESSION['Flash']['flash'][0]['params']['class']);
        $_SESSION['Flash'] = null;

        // save db log
        /* @var DblogsTable $dblogs */
        $this->BcMessage->set('test', true, true);
        $dblogs = $this->getTableLocator()->get('BaserCore.Dblogs');
        $dblog = $dblogs->find()->all()->last();
        $this->assertEquals('test', $dblog->message);
        unset($_SESSION['Flash']);

        // not flash message
        $this->BcMessage->set('test', false, false, false);
        $this->assertEmpty($_SESSION);
    }

    /**
     * setSuccess
     */
    public function testSetSuccess()
    {
        $this->BcMessage->setSuccess('test');
        $this->assertEquals('notice-message', $_SESSION['Flash']['flash'][0]['params']['class']);
    }

    /**
     * setError
     */
    public function testSetError()
    {
        $this->BcMessage->setError('test');
        $this->assertEquals('alert-message', $_SESSION['Flash']['flash'][0]['params']['class']);
    }

    /**
     * setWarning
     */
    public function testSetWarning()
    {
        $this->BcMessage->setWarning('test');
        $this->assertEquals('warning-message', $_SESSION['Flash']['flash'][0]['params']['class']);
    }

    /**
     * setInfo
     */
    public function testSetInfo()
    {
        $this->BcMessage->setInfo('test');
        $this->assertEquals('info-message', $_SESSION['Flash']['flash'][0]['params']['class']);
    }

}
