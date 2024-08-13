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

/**
 * CreateJwtCommand
 *
 * bin/cake create jwt
 */
class CreateJwtCommand extends Command
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
     * @return null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $service = $this->getService(InstallationsServiceInterface::class);
        $service->createJwt();
    }

}
