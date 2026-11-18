<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCcBurgerEditor;

use BaserCore\BcPlugin;

/**
 * Plugin
 */
class BcCcBurgerEditorPlugin extends BcPlugin
{

    /**
     * Routes
     * プラグインのルーティングを追加しないように空のメソッドとする
     * @param \Cake\Routing\RouteBuilder $routes
     * @return void
     */
    public function routes(\Cake\Routing\RouteBuilder $routes): void {}

}
