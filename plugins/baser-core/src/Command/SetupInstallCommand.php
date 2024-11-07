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

use BaserCore\Error\BcException;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFolder;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SetupInstallCommand
 */
class SetupInstallCommand extends Command
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->putEnv('INSTALL_MODE', 'true');
        if(file_exists(ROOT . DS . 'config' . DS . 'install.php')) {
            unlink(ROOT . DS . 'config' . DS . 'install.php');
        }
        if(is_dir(ROOT . DS . 'db' . DS . 'sqlite')) {
            (new BcFolder(ROOT . DS . 'db' . DS . 'sqlite'))->delete();
        }
        $io->out(__d('baser_core', 'インストールの準備ができました。'));
    }

}
