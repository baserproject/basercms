<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Controller\Admin;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;
use BaserCore\Controller\AppController;

/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

class BcAdminAppController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'prefix' => 'Admin',
                'controller' => 'Users',
                'action' => 'login'
            ],
            // コントローラーで isAuthorized を使用します
            // 'authorize' => ['Controller'],
            'unauthorizedRedirect' => $this->referer()
        ]);
    }

	public function beforeRender(EventInterface $event)
	{
		$this->viewBuilder()->setTheme('BcAdminThird');
	}
}
