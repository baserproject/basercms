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

namespace BaserCore\Test\TestCase\Config;

use BaserCore\TestSuite\BcTestCase;

/**
 * Class PathsTest
 */
class PathsTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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
     * test bootstrap
     *
     * @return void
     */
    public function testDefined(): void
    {
        $this->assertEquals(ROOT. '/plugins/baser-core/', BASER);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Controller/', BASER_CONTROLLERS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Model/', BASER_MODELS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/View/', BASER_VIEWS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Vendor/', BASER_VENDORS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Controller/Component/', BASER_COMPONENTS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/View/Helper/', BASER_HELPERS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Model/Behavior/', BASER_BEHAVIORS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Model/Datasource/', BASER_DATASOURCE);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Model/Datasource/Database/', BASER_DATABASE);
        $this->assertEquals(ROOT. '/plugins/', BASER_PLUGINS);
        $this->assertEquals(ROOT. '/plugins/baser-core/config/', BASER_CONFIGS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Locale/', BASER_LOCALES);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Event/', BASER_EVENTS);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Utility/', BASER_UTILITIES);
        $this->assertEquals(ROOT. '/plugins/baser-core/src/Console/', BASER_CONSOLES);
        $this->assertEquals(ROOT. '/plugins/baser-core/webroot/', BASER_WEBROOT);
        $this->assertEquals(ROOT. '/plugins/', BASER_THEMES);
    }

}
