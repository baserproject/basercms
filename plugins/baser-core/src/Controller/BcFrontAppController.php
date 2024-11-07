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
namespace BaserCore\Controller;

use BaserCore\Event\BcShortCodeEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\I18n;
use Cake\Routing\Router;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * BcFrontAppController
 */
class BcFrontAppController extends AppController
{

    /**
     * Initialize
     * @checked
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        // フロント認証が有効、かつ、permissionType が 2（ブラックリスト）の場合以外に認証を設定
        if(!Configure::read('BcPrefixAuth.Front.disabled') && (int) Configure::read('BcPrefixAuth.Front.permissionType') !== 2) {
            $this->loadComponent('Authentication.Authentication', [
                'logoutRedirect' => Router::url(Configure::read("BcPrefixAuth.Front.loginAction"), true),
            ]);
        }
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return Response|void
     * @checked
     * @noTodo
     */
    public function beforeFilter(EventInterface $event)
    {
        /**
         * フロントページ用言語設定
         */
        $currentSite = $this->getRequest()->getAttribute('currentSite');
        if ($currentSite && $currentSite->lang) {
            $lang = Configure::read('BcLang.' . $currentSite->lang);
        }
        if (Configure::read('BcApp.systemMessageLangFromSiteSetting') && isset($lang['langs'][0])) {
            I18n::setLocale($lang['langs'][0]);
        }

        $response = parent::beforeFilter($event);
        if($response) return $response;
    }

    /**
     * Before Render
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        if (BcUtil::isInstalled()) {
            // ショートコード
            $this->getEventManager()->on(new BcShortCodeEventListener());
        }
        $this->setupFrontView();
    }

}
