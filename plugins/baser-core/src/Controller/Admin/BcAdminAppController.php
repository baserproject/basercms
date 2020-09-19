<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;
use Cake\Event\EventInterface;
use BaserCore\Controller\AppController;
use Exception;

/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
class BcAdminAppController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        try {
            $this->loadComponent('Authentication.Authentication');
        } catch (Exception $e) {
        }
        // $this->loadComponent('Auth', [
        //     'authenticate' => [
        //         'Form' => [
        //             'fields' => [
        //                 'username' => 'email',
        //                 'password' => 'password'
        //             ]
        //         ]
        //     ],
        //     'loginAction' => [
        //         'prefix' => 'Admin',
        //         'controller' => 'Users',
        //         'action' => 'login'
        //     ],
        //     // コントローラーで isAuthorized を使用します
        //     // 'authorize' => ['Controller'],
        //     'unauthorizedRedirect' => $this->referer()
        // ]);
    }

	public function beforeRender(EventInterface $event)
	{
	    $this->viewBuilder()->setClassName('BaserCore.BcAdminApp');
		$this->viewBuilder()->setTheme('BcAdminThird');
	}
}
