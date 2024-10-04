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

namespace BaserCore\Command;

use BaserCore\Utility\BcComposer;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Composer\Package\Archiver\ZipArchiver;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CreateReleaseCommand
 */
class CreateReleaseCommand extends Command
{

    /**
     * buildOptionParser
     *
     * @param \Cake\Console\ConsoleOptionParser $parser
     * @return \Cake\Console\ConsoleOptionParser
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function buildOptionParser(\Cake\Console\ConsoleOptionParser $parser): \Cake\Console\ConsoleOptionParser
    {
        $parser->addArgument('version', [
            'help' => __d('baser_core', 'リリースバージョン'),
            'required' => true
        ]);
        $parser->addOption('branch', [
            'help' => __d('baser_core', 'クローン対象ブランチ'),
            'default' => 'master'
        ]);
        return $parser;
    }

    /**
     * execute
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int|void|null
     * @checked
     * @noTodo
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $packagePath = TMP . 'basercms' . DS;
        if(is_dir($packagePath)) {
            (new BcFolder($packagePath))->delete();
        }
        $version = $args->getArgument('version');

        $io->out(__d('baser_core', 'リリースパッケージを作成します。', TMP));
        $io->out();

        $io->out(__d('baser_core', '- {0} にパッケージをクローンします。', TMP));
        $this->clonePackage($packagePath, $args->getOption('branch'));

        $io->out(__d('baser_core', '- composer.json / composer.lock をセットアップします。'));
        BcComposer::setup('', $packagePath);
        $result = BcComposer::setupComposerForDistribution($version);
        if($result['code'] === 0) {
            $io->out(__d('baser_core', 'Composer による lock ファイルの更新に失敗アップデートが完了しました。'));
        } else {
            $message = __d('baser_core', 'Composer による lock ファイルの更新に失敗しました。ログを確認してください。');
            $this->log($message);
            $this->log(implode("\n", $result['out']));
            $io->error($message);
            $this->abort();
        }

        $io->out(__d('baser_core', '- プラグインを初期化します。'));
        $this->deletePlugins($packagePath);

        $io->out(__d('baser_core', '- 不要ファイルを削除します。'));
        $this->deleteExcludeFiles($packagePath);

        $io->out(__d('baser_core', '- Zip ファイルを作成します。'));
        $this->createZip($packagePath, $version);

        $io->out(__d('baser_core', '- クリーニング処理を実行します。'));
        (new BcFolder($packagePath))->delete();

        $io->out();
        $io->out(__d('baser_core', 'リリースパッケージの作成が完了しました。/tmp/basercms.zip を確認してください。'));
    }

    /**
     * パッケージを GitHub よりクローンする
     *
     * @param string $packagePath
     * @checked
     * @noTodo
     */
    public function clonePackage(string $packagePath, string $branch)
    {
        $tmp = TMP;
        $repository = Configure::read('BcApp.repositoryUrl');
        exec("cd {$tmp}; git clone {$repository} basercms");
        exec("cd {$packagePath}; git checkout {$branch}");
    }

    /**
     * プラグインを削除する
     *
     * インストール時、　composer で vendor に配置するため
     * @param string $packagePath
     * @checked
     * @noTodo
     */
    public function deletePlugins(string $packagePath)
    {
        $excludes = ['BcThemeSample', 'BcPluginSample', 'BcColumn'];
        $folder = new BcFolder($packagePath . 'plugins');
        $files = $folder->getFolders(['full'=>true]);
        foreach($files as $path) {
            if(in_array(basename($path), $excludes)) continue;
            (new BcFolder($path))->delete();
        }
    }

    /**
     * Zip ファイルに固める
     *
     * @param string $packagePath
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createZip(string $packagePath, string $version)
    {
        $zip = new ZipArchiver();
        $zipFile = TMP . 'basercms-' . $version . '.zip';
        if(file_exists($zipFile)) {
            unlink($zipFile);
        }
        $zip->archive($packagePath, $zipFile, true);
    }

    /**
     * 配布用に不要なファイルを削除する
     *
     * @param string $packagePath
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteExcludeFiles(string $packagePath)
    {
        $excludeFiles = Configure::read('BcApp.excludeReleasePackage');
        foreach($excludeFiles as $file) {
            $file = $packagePath . $file;
            if(is_dir($file)) {
                (new BcFolder($file))->delete();
            } elseif(file_exists($file)) {
                (new BcFile($file))->delete();
            }
        }
    }

}
