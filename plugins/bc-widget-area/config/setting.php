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

return[
    'BcApp' => [
        /**
         * 管理画面メニュー
         */
        'adminNavigation' => [
            'Systems' => [
                'Utilities' => [
                    'menus' => [
                        'WidgetAreas' => [
                            'title' => __d('baser', 'ウィジェットエリア'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BcWidgetArea', 'controller' => 'WidgetAreas', 'action' => 'index'],
                            'currentRegex' => '/\/widget_areas\/[^\/]+?\/[0-9]+/s'
    ]]]]]],
    /**
     * ウィジェット
     */
    'BcWidget' => [
        // フロントにウィジェットエリアの編集リンクを表示するかどうか
        'editLinkAtFront' => false
    ]
];
