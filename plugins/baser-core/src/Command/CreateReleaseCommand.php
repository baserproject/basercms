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
     * execute
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $packagePath = TMP . 'basercms' . DS;
        if(is_dir($packagePath)) {
            $folder = new Folder($packagePath);
            $folder->delete();
        }

        $io->out(__d('baser', 'リリースパッケージを作成します。', TMP));
        $io->out();

        $io->out(__d('baser', '- {0} にパッケージをクローンします。', TMP));
        $this->clonePackage($packagePath);

        $io->out(__d('baser', '- composer.json をセットアップします。'));
        $this->setupComposer($packagePath);

        $io->out(__d('baser', '- プラグインを初期化します。'));
        $this->deletePlugins($packagePath);

        $io->out(__d('baser', '- 不要ファイルを削除します。'));
        $this->deleteExcludeFiles($packagePath);

        $io->out(__d('baser', '- Zip ファイルを作成します。'));
        $this->createZip($packagePath);

        $io->out(__d('baser', '- クリーニング処理を実行します。'));
        $folder = new Folder($packagePath);
        $folder->delete();

        $io->out();
        $io->out(__d('baser', 'リリースパッケージの作成が完了しました。/tmp/basercms.zip を確認してください。'));
    }

    /**
     * composer.json を配布用にセットアップする
     *
     * @param string $packagePath
     */
    public function setupComposer(string $packagePath)
    {
        $composer = $packagePath . 'composer.json';
        $file = new File($composer);
        $data = $file->read();
        $regex = '/^(.+?)    "replace": {.+?},\n(.+?)/s';
        $data = preg_replace($regex, "$1$2", $data);
        $regex = '/^(.+?"cakephp\/cakephp": ".+?",)(.+?)$/s';
        $setupVersion = Configure::read('BcApp.setupVersion');
        $replace = "$1\n        \"baserproject/basercms\": \"{$setupVersion}\",$2";
        $data = preg_replace($regex, $replace, $data);
        $file->write($data);
    }

    /**
     * パッケージを GitHub よりクローンする
     *
     * @param string $packagePath
     */
    public function clonePackage(string $packagePath)
    {
        $tmp = TMP;
        $repository = Configure::read('BcApp.repositoryUrl');
        exec("cd {$tmp}; git clone {$repository} basercms");
        exec("cd {$packagePath}; git checktout master");
    }

    /**
     * プラグインを削除する
     *
     * インストール時、　composer で vendor に配置するため
     * @param string $packagePath
     */
    public function deletePlugins(string $packagePath)
    {
        $folder = new Folder($packagePath . 'plugins');
        $files = $folder->read(true, true, true);
        foreach($files[0] as $path) {
            $folder->delete($path);
        }
        new File($packagePath . 'plugins' . DS . '.gitkeep');
    }

    /**
     * Zip ファイルに固める
     *
     * @param string $packagePath
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
