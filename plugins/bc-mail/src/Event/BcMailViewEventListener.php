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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setAdminMenu()
    {

        // TODO ucmitz 未実装
//        /* @var MailContent $MailContent */
//        $MailContent = ClassRegistry::init('BcMail.MailContent');
//        $mailContents = $MailContent->find('all', [
//            'conditions' => [
//                $MailContent->Content->getConditionAllowPublish()
//            ],
//            'recursive' => 0,
//            'order' => $MailContent->id,
//            'cache' => false,
//        ]);
//        foreach($mailContents as $mailContent) {
//            $mail = $mailContent['MailContent'];
//            $content = $mailContent['Content'];
//            $config['BcApp.adminNavigation.Contents.' . 'MailContent' . $mail['id']] = [
//                'siteId' => $content['site_id'],
//                'title' => $content['title'],
//                'type' => 'mail-content',
//                'icon' => 'bca-icon--mail',
//                'menus' => [
//                    'MailMessages' . $mail['id'] => ['title' => '受信メール', 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_messages', 'action' => 'index', $mail['id']]],
//                    'MailFields' . $mail['id'] => [
//                        'title' => 'フィールド',
//                        'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mail['id']],
//                        'currentRegex' => '/\/mail\/mail_fields\/[^\/]+?\/' . $mail['id'] . '($|\/)/s'
//                    ],
//                    'MailContents' . $mail['id'] => ['title' => '設定', 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $mail['id']]]
//                ]
//            ];
//        }

        // 以下参考コード
//        /* @var \BcBlog\Model\Table\BlogContentsTable $BlogContent */
//        $blogContentTable = \Cake\ORM\TableRegistry::getTableLocator()->get('BcBlog.BlogContents');
//        $blogContents = $blogContentTable->find()
//            ->contain('Contents')
//            ->where($blogContentTable->Contents->getConditionAllowPublish())
//            ->order(['BlogContents.id'])
//            ->all();
//        $blogContentMenus = [];
//        foreach($blogContents as $blogContent) {
//            $menus = function($blogContent) {
//                $route = ['Admin' => true, 'plugin' => 'BcBlog', 'action' => 'index', $blogContent->id];
//                $menus = [
//                    'BlogPosts' . $blogContent->id => [
//                        'title' => '記事',
//                        'url' => array_merge($route, ['controller' => 'blog_posts']),
//                        'currentRegex' => '/\/bc-blog\/blog_posts\/[^\/]+?\/' . $blogContent->id . '($|\/)/s'
//                    ],
//                    'BlogCategories' . $blogContent->id => [
//                        'title' => 'カテゴリ',
//                        'url' => array_merge($route, ['controller' => 'blog_categories']),
//                        'currentRegex' => '/\/bc-blog\/blog_categories\/[^\/]+?\/' . $blogContent->id . '($|\/)/s'
//                    ]
//                ];
//                if ($blogContent->tag_use) {
//                    $menus = array_merge($menus, [
//                        'BlogTags' . $blogContent->id => [
//                            'title' => 'タグ',
//                            'url' => array_merge($route, ['controller' => 'blog_tags']),
//                            'currentRegex' => '/\/bc-blog\/blog_tags\/[^\/]+?/s'
//                        ]
//                    ]);
//                }
//                if ($blogContent->comment_use) {
//                    $menus = array_merge($menus, [
//                        'BlogComments' . $blogContent->id => [
//                            'title' => 'コメント',
//                            'url' => array_merge($route, ['controller' => 'blog_comments'])
//                        ]
//                    ]);
//                }
//                $menus = array_merge($menus, [
//                    'BlogContentsEdit' . $blogContent->id => [
//                        'title' => '設定',
//                        'url' => array_merge($route, ['controller' => 'blog_contents', 'action' => 'edit'])
//                    ]
//                ]);
//                return $menus;
//            };
//            $blogContentMenus['BlogContent' . $blogContent->id] = [
//                'siteId' => $blogContent->content->site_id,
//                'title' => $blogContent->content->title,
//                'type' => 'blog-content',
//                'icon' => 'bca-icon--blog',
//                'menus' => $menus($blogContent)
//            ];
//        }
//        Configure::write('BcApp.adminNavigation.Contents', array_merge(
//            Configure::read('BcApp.adminNavigation.Contents'),
//            $blogContentMenus
//        ));
    }

}
