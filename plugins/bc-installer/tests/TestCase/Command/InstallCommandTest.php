<?php

namespace BcInstaller\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use BcInstaller\Command\InstallCommand;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

class InstallCommandTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->InstallCommand = new InstallCommand();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test buildOptionParser
     */
    public function testBuildOptionParser()
    {
        Configure::write('BcApp.defaultFrontTheme', 'BcSample');

        $parser = new ConsoleOptionParser('install_command');
        $result = $this->execPrivateMethod($this->InstallCommand, 'buildOptionParser', [$parser]);

        $this->assertInstanceOf(ConsoleOptionParser::class, $result);

        //addArgument
        $arguments = $parser->arguments();
        $this->assertEquals('siteurl', $arguments[0]->name());
        $this->assertStringContainsString('サイトURL', $arguments[0]->help());
        $this->assertTrue($arguments[0]->isRequired());

        $arguments = $parser->arguments();
        $this->assertEquals('adminemail', $arguments[1]->name());
        $this->assertStringContainsString('管理者メールアドレス', $arguments[1]->help());
        $this->assertTrue($arguments[0]->isRequired());


        $arguments = $parser->arguments();
        $this->assertEquals('adminpassword', $arguments[2]->name());
        $this->assertStringContainsString('管理者パスワード', $arguments[2]->help());
        $this->assertTrue($arguments[0]->isRequired());

        $arguments = $parser->arguments();
        $this->assertEquals('database', $arguments[3]->name());
        $this->assertStringContainsString('データベース名', $arguments[3]->help());
        $this->assertTrue($arguments[0]->isRequired());

        //addOption
        $options = $parser->options();
        $this->assertArrayHasKey('datasource', $options);
        $this->assertStringContainsString('データベースタイプ ( mysql or postgresql or sqlite )', $options['datasource']->help());
        $this->assertEquals('mysql', $options['datasource']->defaultValue());

        $this->assertArrayHasKey('host', $options);
        $this->assertStringContainsString('データベースホスト名', $options['host']->help());
        $this->assertEquals('localhost', $options['host']->defaultValue());

        $this->assertArrayHasKey('username', $options);
        $this->assertStringContainsString('データベースログインユーザー名', $options['username']->help());
        $this->assertEquals('', $options['username']->defaultValue());

        $this->assertArrayHasKey('password', $options);
        $this->assertStringContainsString('データベースログインパスワード', $options['password']->help());
        $this->assertEquals('', $options['password']->defaultValue());

        $this->assertArrayHasKey('prefix', $options);
        $this->assertStringContainsString('データベーステーブルプレフィックス', $options['prefix']->help());
        $this->assertEquals('', $options['prefix']->defaultValue());

        $this->assertArrayHasKey('port', $options);
        $this->assertStringContainsString('データベースポート番号', $options['port']->help());
        $this->assertEquals('', $options['port']->defaultValue());

        $this->assertArrayHasKey('baseurl', $options);
        $this->assertStringContainsString('ベースとなるURL', $options['baseurl']->help());
        $this->assertEquals('/', $options['baseurl']->defaultValue());

        $this->assertArrayHasKey('sitename', $options);
        $this->assertStringContainsString('サイト名', $options['sitename']->help());
        $this->assertEquals('My Site', $options['sitename']->defaultValue());

        $this->assertArrayHasKey('data', $options);
        $this->assertStringContainsString('初期データパターン', $options['data']->help());
        $this->assertEquals('BcSample.default', $options['data']->defaultValue());
    }

    /**
     * test createEnvFile
     */
    public function test_createEnvFile()
    {
        // 本物の config/.env に触れないよう、一時フォルダで確かめる
        $configPath = TMP . 'install_command_env_test' . DS;
        if (!is_dir($configPath)) mkdir($configPath, 0777, true);
        @unlink($configPath . '.env');
        @unlink($configPath . '.env.example');

        // .env.example が無い場合は作成できない
        $this->assertFalse($this->InstallCommand->createEnvFile($configPath));

        // .env が無い場合は .env.example から作成する
        file_put_contents($configPath . '.env.example', 'export INSTALL_MODE="true"');
        $this->assertTrue($this->InstallCommand->createEnvFile($configPath));
        $this->assertEquals('export INSTALL_MODE="true"', file_get_contents($configPath . '.env'));

        // 既に .env がある場合は上書きしない
        file_put_contents($configPath . '.env', 'export INSTALL_MODE="false"');
        $this->assertTrue($this->InstallCommand->createEnvFile($configPath));
        $this->assertEquals('export INSTALL_MODE="false"', file_get_contents($configPath . '.env'));

        unlink($configPath . '.env');
        unlink($configPath . '.env.example');
        rmdir($configPath);
    }

}
