<?php

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\Plugin;
use BaserCore\TestSuite\BcTestCase;

class PluginTest extends BcTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->Plugin = new Plugin();
    }

    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    public function testIsPlugin()
    {
        $this->Plugin->type = 'Plugin';
        $this->assertTrue($this->Plugin->isPlugin());

        $this->Plugin->type = 'Theme';
        $this->assertFalse($this->Plugin->isPlugin());
    }

}