<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\Command\CreateReleaseCommand;
use BaserCore\TestSuite\BcTestCase;
use Cake\Console\ConsoleOptionParser;
use BaserCore\Utility\BcFolder;

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
        $this->zipFile = TMP . 'basercms-5.1.0.zip';

        //create TMP folder if not exist
        if (!is_dir(TMP)) {
            mkdir(TMP, 0777, true);
        }
        $this->CreateReleaseCommand->createZip($this->packagePath, '5.1.0');

        $this->assertFileExists($this->zipFile);

        //delete the zip file
        if (file_exists($this->zipFile)) {
            unlink($this->zipFile);
        }
    }

    /**
     * test buildOptionParser
     * @return void
     */
    public function testBuildOptionParser()
    {
        $parser = new ConsoleOptionParser('create_release');
        $result = $this->execPrivateMethod($this->CreateReleaseCommand, 'buildOptionParser', [$parser]);

        $this->assertInstanceOf(ConsoleOptionParser::class, $result);

        $arguments = $parser->arguments();
        $this->assertEquals('version', $arguments[0]->name());
        $this->assertStringContainsString('リリースバージョン', $arguments[0]->help());
        $this->assertTrue($arguments[0]->isRequired());

        $options = $parser->options();
        $this->assertArrayHasKey('branch', $options);
        $this->assertStringContainsString('クローン対象ブランチ', $options['branch']->help());
        $this->assertEquals('master', $options['branch']->defaultValue());
    }

    /**
     * Test deletePlugins
     *
     */
    public function test_deletePlugins()
    {
        //create TMP folder if not exist
        $pluginsPath = TMP . 'plugins_test' . DS;

        $folder = new BcFolder($pluginsPath . 'plugins');
        $folder->create($pluginsPath . 'plugins' . DS . 'BcThemeSample');
        $folder->create($pluginsPath . 'plugins' . DS . 'BcPluginSample');
        $folder->create($pluginsPath . 'plugins' . DS . 'BcColumn');
        $folder->create($pluginsPath . 'plugins' . DS . 'BcNewPlugin1');
        $folder->create($pluginsPath . 'plugins' . DS . 'BcNewPlugin2');

        //check the directories that has been created
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcThemeSample');
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcPluginSample');
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcColumn');
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcNewPlugin1');
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcNewPlugin2');

        $this->CreateReleaseCommand->deletePlugins($pluginsPath);

        //Plugins be deleted
        $this->assertDirectoryDoesNotExist($pluginsPath . 'plugins' . DS . 'BcNewPlugin1');
        $this->assertDirectoryDoesNotExist($pluginsPath . 'plugins' . DS . 'BcNewPlugin2');

        //Plugins be not deleted
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcThemeSample');
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcPluginSample');
        $this->assertDirectoryExists($pluginsPath . 'plugins' . DS . 'BcColumn');

        //clean up
        $folder->delete($pluginsPath);
    }
}
