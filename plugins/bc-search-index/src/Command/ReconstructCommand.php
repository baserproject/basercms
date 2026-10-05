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

namespace BcSearchIndex\Command;

use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * ReconstructCommand
 *
 * 検索インデックスを再構築する
 *
 *   bin/cake search_index reconstruct
 */
class ReconstructCommand extends Command
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
            ->setDescription(__d('baser_core', '検索インデックスを再構築します。'))
            ->addOption('parent-content-id', [
                'help' => __d('baser_core', '再構築の対象をコンテンツ配下に限定する場合に指定します。'),
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
        /** @var SearchIndexesServiceInterface $service */
        $service = $this->getService(SearchIndexesServiceInterface::class);
        $parentContentId = $args->getOption('parent-content-id');

        if (!$service->reconstruct($parentContentId? (int)$parentContentId : null)) {
            $io->err(__d('baser_core', '検索インデックスの再構築に失敗しました。'));
            return static::CODE_ERROR;
        }
        $io->success(__d('baser_core', '検索インデックスの再構築が完了しました。'));
        return static::CODE_SUCCESS;
    }

}
