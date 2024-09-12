<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcSiteConfig;

class BcSiteConfigTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGet()
    {
        SiteConfigFactory::make(['name' => 'version', 'value' => '2.0.0'])->persist();
        $this->assertEquals('2.0.0', BcSiteConfig::get('version'));

        //field not exist
        $this->assertEquals(null, BcSiteConfig::get('not_exist'));
    }
}
