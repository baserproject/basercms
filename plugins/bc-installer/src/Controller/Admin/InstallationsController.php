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

namespace BcInstaller\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Error\BcException;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcInstaller\Service\Admin\InstallationsAdminService;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Utility\Hash;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class InstallationsController
 *
 * インストーラーコントローラー
 */
class InstallationsController extends BcAdminAppController
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * beforeFilter
     *
     * @return void
     * @checked
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        set_time_limit(300);
    }

    /**
     * Step 1: ウェルカムページ
     *
     * @return void
     * @noTodo
     * @checked
     */
    public function index()
    {
        BcUtil::clearAllCache();
        // クッキーを削除。インストール中のCSRF エラーの発生防止
        $this->setResponse($this->getResponse()->withExpiredCookie(new Cookie('csrfToken')));
    }

    /**
     * Step 2: 必須条件チェック
     *
     * @param InstallationsAdminService $service
     * @return void
     * @noTodo
     * @checked
     */
    public function step2(InstallationsAdminServiceInterface $service)
    {
        if ($this->request->getData('mode') === 'next') {
            return $this->redirect(['action' => 'step3']);
        }
        $this->set($service->getViewVarsForStep2());
    }

    /**
     * Step 3: データベースの接続設定
     *
     * @return void|Response
     * @noTodo
     * @checked
     */
    public function step3(InstallationsAdminServiceInterface $service)
    {
        $blDBSettingsOK = false;
        if (!$this->request->is('post')) {
            BcUtil::clearAllCache();
            $this->setRequest($this->request->withParsedBody($service->getDefaultValuesStep3($this->getRequest())));
        } else {
            $service->writeDbSettingToSession($this->getRequest(), $this->getRequest()->getData());
            switch($this->request->getData('mode')) {
                case 'back':
                    return $this->redirect(['action' => 'step2']);
                case 'checkDb':
                    try {
                        $service->testConnectDb($service->readDbSetting($this->getRequest()));
                        $this->BcMessage->setInfo(__d('baser_core', 'データベースへの接続に成功しました。'));
                        $blDBSettingsOK = true;
                    } catch (\PDOException $e) {
                        $this->BcMessage->setError($e->getMessage());
                    } catch (BcException $e) {
                        $this->BcMessage->setError($e->getMessage());
                    }
                    break;
                case 'createDb':
                    ini_set("max_execution_time", 180);
                    try {
                        $service->deleteAllTables($this->getRequest());
                        $service->constructionDb(
                            $service->readDbSetting($this->getRequest()),
                            $this->request->getData('dbDataPattern'),
                            Configure::read('BcApp.defaultAdminTheme')
                        );
                        $this->BcMessage->setInfo(__d('baser_core', 'データベースの構築に成功しました。'));
                        return $this->redirect(['action' => 'step4']);
                    } catch (\Throwable $e) {
                        $errorMessage = __d('baser_core', 'データベースの構築中にエラーが発生しました。') . "\n" . $e->getMessage();
                        $this->BcMessage->setError($errorMessage);
                    }
                    break;
            }
        }
        $this->set($service->getViewVarsForStep3($blDBSettingsOK));
    }

    /**
     * Step 4: データベース生成／管理者ユーザー作成
     *
     * @param InstallationsAdminService $service
     * @return void
     * @checked
     */
    public function step4(InstallationsAdminServiceInterface $service)
    {
        if (!$this->request->is('post')) {
            $this->setRequest($this->request->withParsedBody($service->getDefaultValuesStep4($this->getRequest())));
        } else {
            // ユーザー情報をセッションに保存
            $this->getRequest()->getSession()->write('Installation', array_merge(
                $this->getRequest()->getSession()->read('Installation'),
                $this->getRequest()->getData()
            ));

            if ($this->request->getData('mode') === 'back') {
                return $this->redirect(['action' => 'step3']);
            } elseif ($this->request->getData('mode') === 'finish') {
                try {
                    $db = $service->connectDb($this->getRequest());
                    $db->begin();
                    $service->initAdmin($this->getRequest());
                    $service->sendCompleteMail($this->getRequest()->getData());
                    $db->commit();
                    $this->redirect(['action' => 'step5']);
                } catch (PersistenceFailedException $e) {
                    $db->rollback();
                    $errMsg = implode("\n・", Hash::extract($e->getEntity()->getErrors(), '{s}.{s}'));
                    $this->BcMessage->setError(__d('baser_core', '管理ユーザーを作成できませんでした。') . "\n\n・" . $errMsg);
                } catch (\Throwable $e) {
                    if(strpos($e->getMessage(), 'Could not send email:') !== false) {
                        $db->commit();
                        $this->BcMessage->setWarning(__d('baser_core', 'インストールは完了しましたが、インストール完了メールが送信できませんでした。サーバーのメール設定を見直してください。'));
                        return $this->redirect(['action' => 'step5']);
                    }
                    $db->rollback();
                    $this->BcMessage->setError(__d('baser_core', 'インストール中にエラーが発生しました。') . $e->getMessage());
                }
            }
        }
    }

    /**
     * Step 5: 設定ファイルの生成
     * データベース設定ファイル[database.php]
     * インストールファイル[install.php]
     *
     * @param InstallationsAdminService $service
     * @return void
     * @noTodo
     * @checked
     */
    public function step5(InstallationsAdminServiceInterface $service)
    {
        if (!BcUtil::isInstalled()) {
            $service->connectDb($this->getRequest());
            $service->initFiles($this->getRequest());
            $service->initDb($this->getRequest());
            $service->login($this->getRequest(), $this->getResponse());

            // コントローラーの引数から注入した場合、DB接続でエラーとなるためここで初期化
            /** @var SiteConfigsServiceInterface $siteConfigsService */
            $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
            $siteConfigsService->putEnv('INSTALL_MODE', 'false');
            if(!$this->getRequest()->is('ssl')) {
                $siteConfigsService->putEnv('ADMIN_SSL', 'false');
            }

            BcUtil::clearAllCache();
            if (function_exists('opcache_reset')) opcache_reset();
        }
    }

}
