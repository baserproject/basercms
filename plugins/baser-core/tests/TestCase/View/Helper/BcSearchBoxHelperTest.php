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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcSearchBoxHelper;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BcSearchBoxHelperTest
 * @property BcSearchBoxHelper $BcSearchBoxHelper
 */
class BcSearchBoxHelperTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
    ];

    /**
     * Trait
     */
    use IntegrationTestTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $BcAdminAppView = new BcAdminAppView();
        $BcAdminAppView->setRequest($this->getRequest()->withParam('action', 'index'));
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

