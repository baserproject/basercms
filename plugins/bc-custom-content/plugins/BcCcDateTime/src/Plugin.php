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

namespace BcCcDateTime;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

use BaserCore\BcPlugin;

/**
 * Plugin
 */
class Plugin extends BcPlugin
{

    /**
     * Routes
     * プラグインのルーティンを追加しないように空のメソッドとする
     * @param \Cake\Routing\RouteBuilder $routes
     * @return void
     */
    public function routes(\Cake\Routing\RouteBuilder $routes): void {}

}
