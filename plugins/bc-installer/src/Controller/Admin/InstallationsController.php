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
        if(!BcUtil::isInstallMode()) $this->notFound();
        // TODO ucmitz 以下、未実装
//        /* インストール状態判別 */
//        if (file_exists(APP . 'Config' . DS . 'database.php')) {
//            ConnectionManager::sourceList();
//            $db = ConnectionManager::$config;
//            if ($db->default['datasource'] != '') {
//                $installed = 'complete';
//            } else {
//                $installed = 'half';
//            }
//        } else {
//            $installed = 'yet';
//        }
//
//        switch($this->request->action) {
//            case 'alert':
//                break;
//            case 'reset':
//                if (Configure::read('debug') != -1) {
//                    $this->notFound();
//                }
//                break;
//            default:
//                if ($installed === 'complete') {
//                    if ($this->request->action !== 'step5') {
//                        $this->notFound();
//                    }
//                } else {
//                    $installationData = Cache::read('Installation', 'default');
//                    if (empty($installationData['lastStep'])) {
//                        if (Configure::read('debug') == 0) {
//                            $this->redirect(['action' => 'alert']);
//                            return;
//                        }
//                    }
//                }
//                break;
//        }

        /*if (strpos($this->request->webroot, 'webroot') === false) {
            $this->request->webroot = DS;
        }*/

//        $this->Security->csrfCheck = false;
//        $this->Security->validatePost = false;
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
     * @return void
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
                        $this->BcMessage->setInfo(__d('baser', 'データベースへの接続に成功しました。'));
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
                        $this->BcMessage->setInfo(__d('baser', 'データベースの構築に成功しました。'));
                        return $this->redirect(['action' => 'step4']);
                    } catch (BcException $e) {
                        $errorMessage = __d('baser', 'データベースの構築中にエラーが発生しました。') . "\n" . $e->getMessage();
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
                } catch (PersistenceFailedException|\Throwable $e) {
                    if($e->getMessage() === 'Could not send email: unknown') {
                        $db->commit();
                        $this->BcMessage->setWarning(__d('baser', 'インストールは完了しましたが、インストール完了メールが送信できませんでした。サーバーのメール設定を見直してください。'));
                        return $this->redirect(['action' => 'step5']);
                    }
                    $db->rollback();
                    $errMsg = implode("\n・", Hash::extract($e->getEntity()->getErrors(), '{s}.{s}'));
                    $this->BcMessage->setError(__d('baser', '管理ユーザーを作成できませんでした。') . "\n\n・" . $errMsg);
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

            BcUtil::clearAllCache();
            if (function_exists('opcache_reset')) opcache_reset();
        }
    }

    /**
     * インストール不能警告メッセージを表示
     *
     * @return void
     */
    public function alert()
    {
        $this->setTitle(__d('baser', 'baserCMSのインストールを開始できません'));
    }

    /**
     * baserCMSを初期化する
     * debug フラグが -1 の場合のみ実行可能
     *
     * @return    void
     * @access    public
     */
    public function reset()
    {
        $this->setTitle(__d('baser', 'baserCMSの初期化'));
        $this->layoutPath = 'admin';
        $this->layout = 'default';
        $this->subDir = 'admin';

        if (empty($this->request->getData('Installation.reset'))) {
            $this->set('complete', !BcUtil::isInstalled()? true : false);
            return;
        }

        $dbConfig = $this->_readDbSetting();
        if (!$dbConfig) {
            $dbConfig = getDbConfig('default');
        }

        if (!$this->BcManager->reset($dbConfig)) {
            $this->BcMessage->setError(
                __d(
                    'baser',
                    'baserCMSを初期化しましたが、正常に処理が行われませんでした。詳細については、エラー・ログを確認してださい。'
                )
            );
        } else {
            $this->BcMessage->setInfo(__d('baser', 'baserCMSを初期化しました。'));
        }
        $this->redirect('reset');
    }

}
