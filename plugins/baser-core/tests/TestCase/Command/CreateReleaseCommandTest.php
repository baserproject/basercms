<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\Command\CreateReleaseCommand;
use BaserCore\TestSuite\BcTestCase;

class CreateReleaseCommandTest extends BcTestCase
{
    private $packagePath;
    private $zipFile;
    public function setUp(): void
    {
        parent::setUp();
        $this->CreateReleaseCommand = new CreateReleaseCommand();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test createZip
     *
     */
    public function test_createZip()
    {
        $this->packagePath = '/var/www/html/plugins/bc-widget-area/';
        $this->zipFile = TMP . 'basercms.zip';

        //create TMP folder if not exist
        if (!is_dir(TMP)) {
            mkdir(TMP, 0777, true);
        }
        $this->CreateReleaseCommand->createZip($this->packagePath);

        $this->assertFileExists($this->zipFile);

        //delete the zip file
        if (file_exists($this->zipFile)) {
            unlink($this->zipFile);
        }
    }
}
