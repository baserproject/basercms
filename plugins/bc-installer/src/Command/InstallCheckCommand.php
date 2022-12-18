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

namespace BcInstaller\Command;

use BaserCore\Utility\BcContainerTrait;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;

/**
 * InstallCheckCommand
 *
 * bin/cake install check
 */
class InstallCheckCommand extends Command
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * execute
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->check($io);
    }

    /**
     * 環境チェック
     *
     * @param ConsoleIo $io
     */
    public function check(ConsoleIo $io)
    {
        $checkResult = $this->getService(InstallationsServiceInterface::class)->checkEnv();

        $io->hr();
        $io->out(__d('baser', '基本必須条件'));
        $io->hr();
        $io->out('* PHP mbstring (' . $checkResult['encoding'] . ')：' . (($checkResult['encodingOk'])? 'OK' : 'NG'));
        if (!$checkResult['encodingOk']) {
            $io->out('　' . __d('baser', 'mbstring.internal_encoding を UTF-8 に設定してください。'));
        }
        $io->out('* PHP Version (' . $checkResult['phpVersion'] . ')：' . (($checkResult['phpVersionOk'])? 'OK' : 'NG'));
        if (!$checkResult['phpVersionOk']) {
            $io->out('　' . __d('baser', '古いバージョンのPHPです。動作保証はありません。'));
        }
        $io->out('* PHP Memory Limit (' . $checkResult['phpMemory'] . 'MB)：' . (($checkResult['phpMemoryOk'])? 'OK' : 'NG'));
        if (!$checkResult['phpMemoryOk']) {
            $io->out('　' . sprintf(__d('baser', 'memoty_limit の設定値を %s MB 以上に変更してください。'), Configure::read('BcRequire.phpMemory')));
        }
        $io->out('* Writable /config/ (' . (($checkResult['configDirWritable'])? 'True' : 'False') . ')：' . (($checkResult['configDirWritable'])? 'OK' : 'NG'));
        if (!$checkResult['configDirWritable']) {
            $io->out('　' . __d('baser', '/config/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。'));
        }
        $io->out('* Writable /tmp/ (' . (($checkResult['tmpDirWritable'])? 'True' : 'False') . ')：' . (($checkResult['tmpDirWritable'])? 'OK' : 'NG'));
        if (!$checkResult['tmpDirWritable']) {
            $io->out('　' . __d('baser', '/tmp/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。'));
        }
        $io->out('* Writable /plugins/ (' . (($checkResult['pluginDirWritable'])? 'True' : 'False') . ')：' . (($checkResult['pluginDirWritable'])? 'OK' : 'NG'));
        if (!$checkResult['pluginDirWritable']) {
            $io->out('　' . __d('baser', '/plugins/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。'));
        }
        $io->out('* Writable /webroot/files/ (' . (($checkResult['filesDirWritable'])? 'True' : 'False') . ')：' . (($checkResult['filesDirWritable'])? 'OK' : 'NG'));
        if (!$checkResult['filesDirWritable']) {
            $io->out('　' . __d('baser', '/webroot/files/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。'));
        }
        $io->out('* PHP PDO (' . (($checkResult['phpPdo'])? 'True' : 'False') . ')');
        if (!$checkResult['phpPdo']) {
            $io->out('　' . __d('baser', 'PHP の PDO は必須モジュールです。'));
        }
        $io->out('* PHP GD (' . (($checkResult['phpGd'])? 'True' : 'False') . ')');
        if (!$checkResult['phpGd']) {
            $io->out('　' . __d('baser', 'PHP の GD は、必須モジュールです。GDが利用可能な状態にしてください。'));
        }
        $io->out('* PHP XML (' . (($checkResult['phpXml'])? 'True' : 'False') . ')');
        if (!$checkResult['phpXml']) {
            $io->out('　' . __d('baser', 'PHP の XML は、必須モジュールです。XMLが利用可能な状態にしてください。'));
        }

        $io->out();
        $io->hr();
        $io->out(__d('baser', 'オプション'));
        $io->hr();

        $io->out('* PHP Safe Mode (' . (!($checkResult['safeModeOff'])? 'On' : 'Off') . ')');
        if (!$checkResult['safeModeOff']) {
            $io->out('　' . __d('baser', 'Safe Mode が On の場合、動作保証はありません。'));
        }
        $io->out('* Writable /db/ (' . (($checkResult['dbDirWritable'])? 'True' : 'False') . ')');
        if (!$checkResult['dbDirWritable']) {
            $io->out('　' . __d('baser', '/db/ に書き込み権限を与える事ができませんでした。'));
            $io->out('　' . __d('baser', 'SQLite など、ファイルベースのデータベースを利用するには、'));
            $io->out('　' . __d('baser', '手動で書き込み権限を与えてください。'));
        }
        if ($checkResult['apacheRewrite']) {
            $apacheRewrite = 'True';
        } elseif ($checkResult['apacheRewrite'] === -1) {
            $apacheRewrite = __d('baser', '不明');
        } else {
            $apacheRewrite = 'False';
        }
        $io->out('* Apache Rewrite (' . $apacheRewrite . ')');
        if ($checkResult['apacheRewrite'] > 0) {
            $io->out('　' . __d('baser', 'Apache の Rewrite モジュール がインストールされていない場合、スマートURLは利用できません。'));
        }
        $io->out();
        if($checkResult['blRequirementsMet']) {
            $io->out(__d('baser', 'baserCMSのインストールが可能な状態です。 cake install を実行しましょう。'));
        } else {
            $io->out(__d('baser', 'baserCMSのインストールができません。環境を見直しましょう。'));
        }
        $io->out();
    }
}
