<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.2
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Command;

use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * RestoreDbCommand
 *
 * データメンテナンスの復元をコマンドから実行する
 *
 *   bin/cake bc_utility restore_db <zipファイルパス>
 */
class RestoreDbCommand extends Command
{

    use BcContainerTrait;

    /**
     * オプションパーサー
     *
     * @param ConsoleOptionParser $parser
     * @return ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(__d('baser_core', 'バックアップZIPからデータベースを復元します。'))
            ->addArgument('path', [
                'help' => __d('baser_core', 'バックアップZIPのパス'),
                'required' => true,
            ])
            ->addOption('encoding', [
                'help' => __d('baser_core', 'バックアップの文字コード'),
                'default' => 'UTF-8',
            ]);
    }

    /**
     * execute
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $path = (string)$args->getArgument('path');
        if (!is_file($path)) {
            $io->err(__d('baser_core', 'バックアップファイルが見つかりません。') . ' ' . $path);
            return static::CODE_ERROR;
        }

        /** @var UtilitiesServiceInterface $service */
        $service = $this->getService(UtilitiesServiceInterface::class);
        try {
            $service->restoreDbFromPath($path, (string)$args->getOption('encoding'));
        } catch (\Throwable $e) {
            $io->err($e->getMessage());
            return static::CODE_ERROR;
        }
        $io->success(__d('baser_core', 'データベースの復元が完了しました。'));
        return static::CODE_SUCCESS;
    }

}
