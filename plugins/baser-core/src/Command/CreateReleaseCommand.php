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
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
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
     */
    protected function buildOptionParser(\Cake\Console\ConsoleOptionParser $parser): \Cake\Console\ConsoleOptionParser
    {
        $parser->addArgument('branch', [
            'help' => __d('baser_core', 'クローン対象ブランチ'),
            'default' => 'master',
            'required' => false
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
            $folder = new Folder($packagePath);
            $folder->delete();
        }

        $io->out(__d('baser_core', 'リリースパッケージを作成します。', TMP));
        $io->out();

        $io->out(__d('baser_core', '- {0} にパッケージをクローンします。', TMP));
        $this->clonePackage($packagePath, $args->getArgument('branch'));

        $io->out(__d('baser_core', '- composer.json をセットアップします。'));
        BcComposer::setupComposerForDistribution($packagePath);

        $io->out(__d('baser_core', '- プラグインを初期化します。'));
        $this->deletePlugins($packagePath);

        $io->out(__d('baser_core', '- 不要ファイルを削除します。'));
        $this->deleteExcludeFiles($packagePath);

        $io->out(__d('baser_core', '- Zip ファイルを作成します。'));
        $this->createZip($packagePath);

        $io->out(__d('baser_core', '- クリーニング処理を実行します。'));
        $folder = new Folder($packagePath);
        $folder->delete();

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
        $folder = new Folder($packagePath . 'plugins');
        $files = $folder->read(true, true, true);
        foreach($files[0] as $path) {
            if(in_array(basename($path), $excludes)) continue;
            $folder->delete($path);
        }
        new File($packagePath . 'plugins' . DS . '.gitkeep');
    }

    /**
     * Zip ファイルに固める
     *
     * @param string $packagePath
     * @checked
     * @noTodo
     */
    public function createZip(string $packagePath)
    {
        $zip = new ZipArchiver();
        $zipFile = TMP . 'basercms.zip';
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
     */
    public function deleteExcludeFiles(string $packagePath)
    {
        $excludeFiles = Configure::read('BcApp.excludeReleasePackage');
        foreach($excludeFiles as $file) {
            $file = $packagePath . $file;
            if(is_dir($file)) {
                (new Folder($file))->delete();
            } elseif(file_exists($file)) {
                (new File($file))->delete();
            }
        }
    }

}
