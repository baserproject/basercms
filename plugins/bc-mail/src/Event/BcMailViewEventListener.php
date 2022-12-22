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

namespace BcMail\Event;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcMailViewEventListener
 */
class BcMailViewEventListener extends \BaserCore\Event\BcViewEventListener
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
     * @unitTest
     */
    public function beforeRender()
    {
        if (!BcUtil::isAdminSystem()) return;
        $this->setAdminMenu();
    }

    /**
     * 管理画面メニュー用のデータをセットする
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setAdminMenu()
    {
        /* @var \BcMail\Model\Table\MailContentsTable $table */
        $table = \Cake\ORM\TableRegistry::getTableLocator()->get('BcMail.MailContents');
        $entities = $table->find()
            ->contain('Contents')
            ->where($table->Contents->getConditionAllowPublish())
            ->order(['MailContents.id'])
            ->all();
        $contentMenus = [];
        foreach($entities as $entity) {
            $menus = function($entity) {
                $route = ['Admin' => true, 'plugin' => 'BcMail', 'action' => 'index', $entity->id];
                return [
                    'MailMessages' . $entity->id => [
                        'title' => '受信メール',
                        'url' => array_merge($route, ['controller' => 'MailMessages']),
                        'currentRegex' => '/\/bc-mail\/mail_messages\/[^\/]+?\/' . $entity->id . '($|\/)/s'
                    ],
                    'MailFields' . $entity->id => [
                        'title' => 'フィールド',
                        'url' => array_merge($route, ['controller' => 'MailFields']),
                        'currentRegex' => '/\/bc-mail\/mail_fields\/[^\/]+?\/' . $entity->id . '($|\/)/s'
                    ],
                    'MailContentsEdit' . $entity->id => [
                        'title' => '設定',
                        'url' => array_merge($route, ['controller' => 'MailContents', 'action' => 'edit'])
                    ]
                ];
            };
            $contentMenus['MailContent' . $entity->id] = [
                'siteId' => $entity->content->site_id,
                'title' => $entity->content->title,
                'type' => 'blog-content',
                'icon' => 'bca-icon--blog',
                'menus' => $menus($entity)
            ];
        }
        Configure::write('BcApp.adminNavigation.Contents', array_merge(
            Configure::read('BcApp.adminNavigation.Contents'),
            $contentMenus
        ));
    }

}
