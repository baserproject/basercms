<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcCustomContent\Event;

use BaserCore\Utility\BcUtil;
use BcCustomContent\Model\Table\CustomContentsTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\TableRegistry;

/**
 * BcCustomContentViewEventListener
 */
class BcCustomContentViewEventListener extends \BaserCore\Event\BcViewEventListener
{

    /**
     * Events
     * @var string[]
     */
    public $events = [
        'beforeRender'
    ];

    /**
     * Before render
     * @checked
     * @noTodo
     */
    public function beforeRender(Event $event)
    {
        $view = $event->getSubject();
        if ($view->getName() === 'Error') return;
        if (!BcUtil::isAdminSystem()) return;
        $this->setAdminMenu();
    }

    /**
     * 管理画面メニュー用のデータをセットする
     * @checked
     * @noTodo
     */
    public function setAdminMenu()
    {
        /* @var CustomContentsTable $customContentsTable */
        $customContentsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('BcCustomContent.CustomContents');
        $customContents = $customContentsTable->find()
            ->contain(['Contents', 'CustomTables'])
            ->order(['CustomContents.id'])
            ->all();

        $navi = [];
        foreach($customContents as $customContent) {
            if (empty($customContent->custom_table)) continue;
            $createMenu = function($customContent) {
                $tableId = $customContent->custom_table->id;
                return [
                    'CustomEntries' . $tableId => [
                        'title' => __d('baser_core', 'エントリー'),
                        'url' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomEntries',
                            'action' => 'index',
                            $tableId
                        ],
                        'currentRegex' => '/\/bc-custom-content\/custom_entries\/[^\/]+?\/' . $tableId . '($|\/)/s'
                    ],
                    'CustomContentEdit' . $tableId => [
                        'title' => __d('baser_core', 'コンテンツ設定'),
                        'url' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContents',
                            'action' => 'edit',
                            $customContent->id
                        ]
                    ]
                ];
            };
            $navi['CustomContent' . $customContent->id] = [
                'siteId' => $customContent->content->site_id,
                'title' => $customContent->content->title,
                'type' => 'custom-content',
                'icon' => 'bca-icon--custom',
                'menus' => $createMenu($customContent)
            ];
        }
        Configure::write('BcApp.adminNavigation.Contents', array_merge(
            Configure::read('BcApp.adminNavigation.Contents'),
            $navi
        ));

        $customTablesTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomTables');
        $tables = $customTablesTable->find()
            ->contain(['CustomContents'])
            ->where(['OR' => [
                ['CustomTables.type' => 2],
                ['CustomTables.type' => 1, 'CustomContents.id IS ' => null]
            ]])->all();
        $navi = [];
        if ($tables->count()) {
            $menus = [];
            foreach($tables as $table) {
                $createMenu = function($table) {
                    $tableId = $table->id;
                    return [
                        'CustomEntries' . $tableId => [
                            'title' => $table->title,
                            'url' => [
                                'prefix' => 'Admin',
                                'plugin' => 'BcCustomContent',
                                'controller' => 'CustomEntries',
                                'action' => 'index',
                                $tableId
                            ],
                            'currentRegex' => '/\/custom_entries\/[^\/]+?\/' . $tableId . '($|\/)/s'
                        ]
                    ];
                };
                $menus = array_merge($menus, $createMenu($table));
            }
            $navi['CustomTables'] = [
                'menus' => $menus
            ];
        }
        Configure::write('BcApp.adminNavigation.Systems', array_merge_recursive(
            Configure::read('BcApp.adminNavigation.Systems'),
            $navi
        ));
    }

}
