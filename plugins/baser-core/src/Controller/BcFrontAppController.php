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
     * @unitTest
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
        $response = $this->redirectIfIsNotSameSite();
        if ($response) return $response;
    }

    /**
     * Before Render
     * @param EventInterface $event
     * @checked
     * @note(value="マイルストーン２が終わってから確認する")
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        // TODO ucmitz サイト名でサブフォルダを設定し、サブフォルダが存在しない場合は、通常のフォルダを参照できるようにする
        // この部分もテストも実装要
//        $subDir = $this->getRequest()->getAttribute('currentSite')->name;
//        if ($subDir) {
//            $this->viewBuilder()->setLayoutPath($subDir);
//            $this->viewBuilder()->setTemplatePath($this->getName() . DS . $subDir);
//        }
        if (BcUtil::isInstalled()) {
            // ショートコード
            $this->getEventManager()->on(new BcShortCodeEventListener());
        }
        if (!isset($this->RequestHandler) || !$this->RequestHandler->prefers('json')) {
            $this->setupFrontView();
        }
    }

    /**
     * siteUrlや、sslUrlと現在のURLが違う場合には、そちらのURLにリダイレクトを行う
     * setting.php にて、cmsUrlとして、cmsUrlを定義した場合にはそちらを優先する
     * @return Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function redirectIfIsNotSameSite()
    {
        if (Configure::read('BcEnv.cmsUrl')) {
            $siteUrl = Configure::read('BcEnv.cmsUrl');
        } elseif ($this->getRequest()->is('ssl')) {
            $siteUrl = Configure::read('BcEnv.sslUrl');
        } else {
            $siteUrl = Configure::read('BcEnv.siteUrl');
        }
        if (!$siteUrl) {
            return;
        }
        if (BcUtil::siteUrl() !== $siteUrl) {
            $params = $this->getRequest()->getAttributes()['params'];
            unset($params['Content']);
            unset($params['Site']);
            $url = Router::reverse($params, false);
            $webroot = $this->request->getAttributes()['webroot'];
            if($webroot) {
                $webrootReg = '/^\/' . preg_quote($webroot, '/') . '/';
                $url = preg_replace($webrootReg, '', $url);
            }
            return $this->redirect(preg_replace('/\/$/', '', $siteUrl) . $url);
        }
    }
}
