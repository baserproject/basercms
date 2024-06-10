<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\Helper\BcSmartphoneHelper;
use Cake\View\View;

/**
 * BcSmartphoneHelper Test Case
 *
 * @property BcSmartphoneHelper $BcSmartphone
 */
class BcSmartphoneHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;


    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcSmartphone = new BcSmartphoneHelper(new View());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcSmartphone);

        parent::tearDown();
    }

    /**
     * afterLayout
     *
     * @return void
     */
    public function testAfterLayout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @dataProvider removePrefixDataProvider
     */
    public function test_removePrefix($matches, $expected){
        $result = $this->execPrivateMethod($this->BcSmartphone, '_removePrefix', [$matches]);
        $this->assertEquals($expected, $result);
    }

    public static function removePrefixDataProvider()
    {
        return [
            'with_smartphone_off' => [
                [
                    1 => ' class="link"',
                    2 => 'smartphone=off',
                    3 => 'http://example.com',
                    5 => 'page.html',
                ],
                '<a class="link"href="http://example.comsmartphone=off"'
            ],
            'without_smartphone_off' => [
                [
                    1 => ' class="link"',
                    2 => 'category/news',
                    3 => 'http://example.com',
                    5 => 'page.html',
                ],
                '<a class="link"href="http://example.compage.html"'
            ]
        ];
    }

}
