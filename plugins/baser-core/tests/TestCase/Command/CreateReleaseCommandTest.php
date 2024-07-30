<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;

class CreateReleaseCommandTest extends BcTestCase
{
    /**
     * Trait
     */
    use ConsoleIntegrationTestTrait;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * test buildOptionParser
     * @return void
     */
    public function testBuildOptionParser()
    {
        $this->exec('create_release --help');
        $this->assertOutputContains('クローン対象ブランチ');
    }

}
