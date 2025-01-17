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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\PagesScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcSearchBoxHelper;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcSearchBoxHelperTest
 * @property BcSearchBoxHelper $BcSearchBoxHelper
 */
class BcSearchBoxHelperTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->loadFixtureScenario(PagesScenario::class);
        $BcAdminAppView = new BcAdminAppView($this->getRequest()->withParam('action', 'index'));
        $this->BcSearchBoxHelper = new BcSearchBoxHelper($BcAdminAppView);
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
     * test DispatchShowField
     *
     * @return void
     */
    public function testDispatchShowField()
    {
        $this->entryEventToMock(self::EVENT_LAYER_HELPER, 'BcSearchBox.showField', function (Event $event) {
            $data = $event->getData();
            $this->assertTrue(isset($data['id']));
            $this->assertTrue(isset($data['fields']));
            $inputFields = [
                [
                    'title' => 'first name',
                    'input' => 'text'
                ],
                [
                    'title' => 'last name',
                    'input' => 'text'
                ]
            ];
            $event->setData('fields', $inputFields);
        });
        $expected = '<span class="bca-search__input-item">first name&nbsp;text&nbsp;</span>' . "\n" .
            '<span class="bca-search__input-item">last name&nbsp;text&nbsp;</span>' . "\n";
        $rs = $this->BcSearchBoxHelper->dispatchShowField();
        $this->assertEquals($expected, $rs);
    }
}

