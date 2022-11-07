<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcContentLink;

use BaserCore\BcPlugin;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcContentLink\ServiceProvider\BcContentLinkServiceProvider;
use Cake\Core\ContainerInterface;

/**
 * plugin for BcPlugin
 */
class Plugin extends BcPlugin
{

    /**
     * services
     *
     * コンテンツリンクプラグインのサービスプロバイダを追加する
     *
     * @param ContainerInterface $container
     * @noTodo
     * @checked
     * @unitTest
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcContentLinkServiceProvider());
    }

}
