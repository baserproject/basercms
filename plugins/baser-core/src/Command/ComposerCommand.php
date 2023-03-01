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
     */
    protected function buildOptionParser(\Cake\Console\ConsoleOptionParser $parser): \Cake\Console\ConsoleOptionParser
    {
        $parser->addArgument('version', [
            'help' => __d('baser', 'アップデート対象のバージョン番号'),
            'default' => '',
            'required' => true
        ]);
        $parser->addOption('php', [
            'help' => __d('baser', 'データベース接続名'),
            'default' => 'php'
        ]);
        return $parser;
    }

    /**
     * execute
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        try {
            BcComposer::setup($args->getOption('php'));
        } catch (\Throwable $e) {
            $message = __d('baser', 'Composer によるアップデートが失敗しました。');
            $this->log($message, LogLevel::ERROR, 'update');
            $this->log($e->getMessage(), LogLevel::ERROR, 'update');
            $io->out($message);
            exit(1);
        }
        $result = BcComposer::require('baser-core', $args->getArgument('version'));
        if($result['code'] === 0) {
            $io->out(__d('baser', 'Composer によるアップデートが完了しました。'));
        } else {
            $message = __d('baser', 'Composer によるアップデートが失敗しました。update ログを確認してください。');
            $this->log($message, LogLevel::ERROR, 'update');
            $this->log(implode("\n", $result['out']), LogLevel::ERROR, 'update');
            $io->out($message);
            exit(1);
        }
    }

}
