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
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;

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
    }

    /**
     * test buildOptionParser
     * @return void
     */
	public function testBuildOptionParser()
	{
        $this->exec('composer --help');
        $this->assertOutputContains('PHPのパス');
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
        $this->exec('composer 9999.9999.9999');
        $this->assertExitError();
        $this->assertErrorContains('Composer によるアップデートが失敗しました。update ログを確認してください。');

        // composer実行（composer.json を配布用にセットアップ）
        // TODO 一旦コメントアウト、永続的にテスト可能な内容に調整する
//        BcComposer::setup('', ROOT . DS);
//        $this->exec('composer 5.1.4.0');
//        $this->assertExitCode(Command::CODE_SUCCESS);
//        $this->assertOutputContains('Composer によるアップデートが完了しました。');

        // バックアップをリストア
        rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
        // vendor/baserproject を削除
        (new BcFolder(ROOT . DS . 'vendor' . DS . 'baserproject'))->delete();
    }

    /**
     * test execute の脆弱パッケージブロック解除
     *
     * 現行バージョンが5.2系以下の場合のみ、依存する CakePHP 5.0系がセキュリティアドバイザリの
     * 対象になっている影響で発生する composer のダウンロードブロックを解除することを確認する
     * @return void
     */
    public function test_executeDisablesBlockInsecureForOldVersion()
    {
        // composer.json / composer.lock / VERSION.txt をバックアップ
        copy(ROOT . DS . 'composer.json', ROOT . DS . 'composer.json.bak');
        copy(ROOT . DS . 'composer.lock', ROOT . DS . 'composer.lock.bak');
        copy(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');

        // 現行バージョンが5.2系以下の場合、disableBlockInsecure() が呼ばれるべき
        (new BcFile(BASER . 'VERSION.txt'))->write('5.2.8');
        $this->exec('composer 9999.9999.9999');
        $data = json_decode((new BcFile(ROOT . DS . 'composer.json'))->read(), true);
        $this->assertFalse($data['config']['audit']['block-insecure'] ?? true, '5.2系以下では disableBlockInsecure() が呼ばれるべき');

        // composer.json を一旦バックアップから戻して次のケースへ
        copy(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');

        // 現行バージョンが5.3系以上の場合、disableBlockInsecure() は呼ばれないべき
        (new BcFile(BASER . 'VERSION.txt'))->write('5.3.0');
        $this->exec('composer 9999.9999.9999');
        $data = json_decode((new BcFile(ROOT . DS . 'composer.json'))->read(), true);
        $this->assertArrayNotHasKey('block-insecure', $data['config']['audit'] ?? [], '5.3系以上では disableBlockInsecure() は呼ばれないべき');

        // バックアップをリストア
        rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
        (new BcFolder(ROOT . DS . 'vendor' . DS . 'baserproject'))->delete();
    }

    /**
     * test execute on update tmp
     * @return void
     */
	public function testExecuteOnUpdateTmp()
    {
        // このテストは、monorepo自体(replaceでbaserproject/*を自己解決しており、
        // vendor/plugins配下のいずれにもbaser-core自身のcomposer.jsonが存在しない)を
        // ソースにして「cakephp 5.2系pin → cakephp 4.4系を要求する旧baser-core 5.0.15への
        // ダウングレード」を試みるため、relaxFrameworkConstraints() が対象を検出できず、
        // 別の要因で失敗する。実際の配布サイト(replaceを使わずbaser-coreが実インストール
        // される)を模した形に調整してから再実装する
        $this->markTestIncomplete('monorepo特有のreplace構成により、ダウングレード方向のシナリオ再現には別途テスト環境の調整が必要');
        // 一時ファイル作成
        (new BcFolder(TMP . 'update'))->create();
        (new BcFolder(ROOT . DS . 'vendor'))->copy(TMP . 'update' . DS . 'vendor');
        copy(ROOT . DS . 'composer.json', TMP . 'update' . DS . 'composer.json');
        copy(ROOT . DS . 'composer.lock', TMP . 'update' . DS . 'composer.lock');
        // composer.json を配布用にセットアップ
        BcComposer::setup('', TMP . 'update' . DS);
        BcComposer::setupComposerForDistribution('5.0.15');
        // composer 実行
        $this->exec('composer 5.0.15 --dir ' . TMP . 'update');
        // バージョンを確認
        $file = new BcFile(TMP . 'update' . DS . 'vendor' . DS . 'baserproject' . DS . 'baser-core' . DS . 'VERSION.txt');
        $versionData = $file->read();
        $aryVersionData = explode("\n", $versionData);
        $this->assertEquals('5.0.15', $aryVersionData[0]);
        // 一時ファイル削除
        (new BcFolder(TMP . 'update'))->delete();
    }

    /**
     * test execute の脆弱性回避
     * @return void
     */
    public function test_execute_vulnerability()
    {
        $rceFile = TMP . 'rce_test_command';
        if (file_exists($rceFile)) unlink($rceFile);

        // --dir 未指定のため ROOT の composer.json が対象になり、
        // relaxFrameworkConstraints() が実行される。require 失敗時も元の状態に戻すためバックアップする
        copy(ROOT . DS . 'composer.json', ROOT . DS . 'composer.json.bak');
        copy(ROOT . DS . 'composer.lock', ROOT . DS . 'composer.lock.bak');

        $maliciousVersion = '1.0.0; touch ' . $rceFile . ';';
        // ConsoleIntegrationTestTrait の exec は引数をパースして Command に渡すため、
        // ここで渡す引数はエスケープされている必要がある（実際のシェル実行をシミュレート）
        ob_start();
        $this->exec('composer ' . escapeshellarg($maliciousVersion));
        ob_get_clean();

        $this->assertFalse(file_exists($rceFile), 'ComposerCommand::execute でOSコマンドインジェクションが発生しました');

        // バックアップをリストア
        rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
    }

}
