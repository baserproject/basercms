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

namespace BcThemeFile\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Service\BcThemeFileService;

/**
 * BcThemeFileServiceTest
 */
class BcThemeFileServiceTest extends BcTestCase
{

    public $BcThemeFileService = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BcThemeFileService = new BcThemeFileService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getFullpath
     */
    public function test_getFullpath()
    {
        //typeが$assetsではない場合、
        $themePath = $this->BcThemeFileService->getFullpath('BcThemeSample', 'layout', 'default.php');
        $this->assertEquals('/var/www/html/plugins/BcThemeSample/templates/layout/default.php', $themePath);

        //typeがimgの場合、
        $themePath = $this->BcThemeFileService->getFullpath('BcFront', 'img', 'logo.png');
        $this->assertEquals('/var/www/html/plugins/bc-front/webroot/img/logo.png', $themePath);
    }
}
