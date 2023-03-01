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

use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Psr\Log\LogLevel;

/**
 * UpdateCommand
 */
class UpdateCommand extends Command
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * buildOptionParser
     *
     * @param \Cake\Console\ConsoleOptionParser $parser
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(\Cake\Console\ConsoleOptionParser $parser): \Cake\Console\ConsoleOptionParser
    {
        $parser->addOption('connection', [
            'help' => __d('baser', 'データベース接続名'),
            'default' => 'default'
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
        $connection = $args->getOption('connection');
        $pluginsService = $this->getService(PluginsServiceInterface::class);
        if($pluginsService->update('BaserCore', $connection)) {
            $io->out(__d('baser', 'Migration と アップデーターによるアップデートが完了しました。'));
        } else {
            $message = __d('baser', 'Migration と アップデーターによるアップデートが失敗しました。');
            $this->log($message, LogLevel::ERROR, 'update');
            $io->out($message);
            exit(1);
        }
    }

}
