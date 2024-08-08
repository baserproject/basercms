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
use Psr\Log\LogLevel;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * ComposerCommand
 */
class ComposerCommand extends Command
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
        $parser->addArgument('version', [
            'help' => __d('baser_core', 'アップデート対象のバージョン番号'),
            'default' => '',
            'required' => true
        ]);
        $parser->addOption('php', [
            'help' => __d('baser_core', 'PHPのパス'),
            'default' => 'php'
        ]);
        $parser->addOption('dir', [
            'help' => __d('baser_core', '実行対象ディレクトリ'),
            'default' => ''
        ]);
        $parser->addOption('force', [
            'help' => __d('baser_core', '指定したバージョンを設定せず composer.json の内容で update する'),
            'default' => ''
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
        try {
            BcComposer::setup($args->getOption('php'), $args->getOption('dir'));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'Composer によるアップデートが失敗しました。');
            $this->log($message, LogLevel::ERROR, 'update');
            $this->log($e->getMessage(), LogLevel::ERROR, 'update');
            $io->error($message);
            $this->abort();
        }

        BcComposer::clearCache();

        if($args->getOption('force')) {
            $result = BcComposer::update();
        } else {
            $result = BcComposer::require('baser-core', $args->getArgument('version'));
        }

        if($result['code'] === 0) {
            $io->out(__d('baser_core', 'Composer によるアップデートが完了しました。'));
        } else {
            $message = __d('baser_core', 'Composer によるアップデートが失敗しました。update ログを確認してください。');
            $this->log($message, LogLevel::ERROR, 'update');
            $this->log(implode("\n", $result['out']), LogLevel::ERROR, 'update');
            $io->error($message);
            $this->abort();
        }
    }

}
