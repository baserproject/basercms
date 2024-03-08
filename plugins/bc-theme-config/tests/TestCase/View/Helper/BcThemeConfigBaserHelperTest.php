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

namespace BcThemeConfig\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcThemeConfig\View\Helper\BcThemeConfigBaserHelper;
use Cake\View\View;

/**
 * BcThemeConfigBaserHelper
 * @property BcThemeConfigBaserHelper $BcThemeConfigBaserHelper
 */
class BcThemeConfigBaserHelperTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcThemeConfigBaserHelper = new BcThemeConfigBaserHelper(new View());
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->BcThemeConfigBaserHelper);
        parent::tearDown();
    }

    /**
     * ロゴを出力する
     * @return void
     */
    public function testMethods()
    {
        $rs = $this->BcThemeConfigBaserHelper->methods();
        $this->assertEquals('BcThemeConfig', $rs['logo'][0]);
        $this->assertEquals('mainImage', $rs['mainImage'][1]);
    }
}
