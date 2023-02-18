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

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

/**
 * SetupTestCommand
 */
class SetupTestCommand extends Command
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
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->putEnv('DEBUG', 'true');
        $siteConfigsService->putEnv('USE_CORE_API', 'true');
        $io->out(__d('baser', 'ユニットテストの準備ができました。'));
    }

}
