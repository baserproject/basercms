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

namespace BaserCore\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcComposer;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
 * ComposerCommandTest
 */
class ComposerCommandTest extends BcTestCase
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
        $this->useCommandRunner();
    }

    /**
     * test buildOptionParser
     * @return void
     */
	public function testBuildOptionParser()
	{
        $this->exec('composer --help');
        $this->assertOutputContains('データベース接続名');
        $this->assertOutputContains('実行対象ディレクトリ');
        $this->assertOutputContains('アップデート対象のバージョン番号');
	}

    /**
     * test execute
     * @return void
     */
	public function testExecute()
    {
        // バージョン指定なし
        $this->exec('composer');
        $this->assertErrorContains('Missing required argument. The `version` argument is required');
        $this->assertExitError();

        // composer.json / composer.lock をバックアップ
        copy(ROOT . DS . 'composer.json', ROOT . DS . 'composer.json.bak');
        copy(ROOT . DS . 'composer.lock', ROOT . DS . 'composer.lock.bak');

        // composer実行（composer.json を配布用にセットアップなし）
        $this->exec('composer 5.0.15');
        $this->assertExitError();
        $this->assertErrorContains('Composer によるアップデートが失敗しました。update ログを確認してください。');

        // composer実行（composer.json を配布用にセットアップ）
        BcComposer::setupComposerForDistribution(ROOT . DS);
        $this->exec('composer 5.0.15');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Composer によるアップデートが完了しました。');

        // バージョンを確認
        $file = new File(ROOT . DS . 'vendor' . DS . 'baserproject' . DS . 'baser-core' . DS . 'VERSION.txt');
        $versionData = $file->read();
        $aryVersionData = explode("\n", $versionData);
        $this->assertEquals('5.0.15', $aryVersionData[0]);

        // バックアップをリストア
        rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
        // vendor/baserproject を削除
        (new Folder())->delete(ROOT . DS . 'vendor' . DS . 'baserproject');
    }

    /**
     * test execute on update tmp
     * @return void
     */
	public function testExecuteOnUpdateTmp()
    {
        // 一時ファイル作成
        (new Folder())->create(TMP . 'update');
        (new Folder(ROOT . DS . 'vendor'))->copy(TMP . 'update' . DS . 'vendor');
        copy(ROOT . DS . 'composer.json', TMP . 'update' . DS . 'composer.json');
        copy(ROOT . DS . 'composer.lock', TMP . 'update' . DS . 'composer.lock');
        // composer.json を配布用にセットアップ
        BcComposer::setupComposerForDistribution(TMP . 'update' . DS);
        // composer 実行
        $this->exec('composer 5.0.15 --dir ' . TMP . 'update');
        // バージョンを確認
        $file = new File(TMP . 'update' . DS . 'vendor' . DS . 'baserproject' . DS . 'baser-core' . DS . 'VERSION.txt');
        $versionData = $file->read();
        $aryVersionData = explode("\n", $versionData);
        $this->assertEquals('5.0.15', $aryVersionData[0]);
        // 一時ファイル削除
        (new Folder())->delete(TMP . 'update');
    }

}
