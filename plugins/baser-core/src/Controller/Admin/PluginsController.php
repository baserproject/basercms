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

namespace BaserCore\Controller\Admin;

use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Service\Admin\PluginsAdminService;
use BaserCore\Service\Admin\PluginsAdminServiceInterface;
use BaserCore\Service\PluginsService;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginsController
 * @property BcMessageComponent $BcMessage
 */
class PluginsController extends BcAdminAppController
{

    /**
     * initialize
     * @throws \Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Before Filter
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return Response|void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function beforeFilter(EventInterface $event)
    {
        $response = parent::beforeFilter($event);
        if($response) return $response;
        $this->Security->setConfig('unlockedActions', ['reset_db', 'update_sort', 'batch']);
    }

    /**
     * プラグインの一覧を表示する
     * @param PluginsServiceInterface $service
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index(PluginsServiceInterface $service)
    {
        $this->set('plugins', $service->getIndex($this->request->getQuery('sortmode') ?? '0'));
    }

    /**
     * インストール
     *
     * @param PluginsAdminServiceInterface $service
     * @param string $name プラグイン名
     * @return Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install(PluginsAdminServiceInterface $service, string $name)
    {
        $this->set($service->getViewVarsForInstall($this->Plugins->getPluginConfig($name)));
        if ($service->getInstallStatusMessage($name) || !$this->request->is(['put', 'post'])) {
            return;
        } else {
            try {
                if ($service->install(
                    $name,
                    $this->request->getData('permission'),
                    $this->request->getData('connection') ?? 'default')
                ) {
                    $this->BcMessage->setSuccess(__d('baser_core', '新規プラグイン「{0}」を {1} に登録しました。', $name, Configure::read('BcApp.title')));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->BcMessage->setError(__d('baser_core', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。'));
                }
            } catch (\Exception $e) {
                $this->BcMessage->setError($e->getMessage());
            }
        }
    }

	/**
	 * プラグインアップデート実行
     * @param PluginsAdminServiceInterface|PluginsAdminService $service
     * @param string $name
     * @return void|Response
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function update(PluginsAdminServiceInterface $service, $name = '')
	{
        $plugin = $this->Plugins->getPluginConfig($name);
        $this->set($service->getViewVarsForUpdate($plugin));

        if($plugin->name === 'BaserCore') {
            $message = [];
            if (!$this->viewBuilder()->getVar('isWritableVendor')) {
                $message[] = __d('baser_core', ROOT . DS . 'vendor に書き込み権限を設定してください。');
            }
            if (!$this->viewBuilder()->getVar('isWritableComposerJson')) {
                $message[] = __d('baser_core', ROOT . DS . 'composer.json に書き込み権限を設定してください。');
            }
            if (!$this->viewBuilder()->getVar('isWritableComposerLock')) {
                $message[] = __d('baser_core', ROOT . DS . 'composer.lock に書き込み権限を設定してください。');
            }
            if($message) {
                $this->BcMessage->setError(implode("\n", $message));
            }
        }

        if (!$this->request->is(['put', 'post'])) return;
        try {
            if($service->update($plugin->name, $this->request->getData('connection') ?? 'default')) {
                $this->BcMessage->setInfo(__d('baser_core', 'アップデート処理が完了しました。画面下部のアップデートログを確認してください。'));
            } else {
                $this->BcMessage->setError(__d('baser_core', 'アップデート処理に失敗しました。画面下部のアップデートログを確認してください。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'アップデート処理に失敗しました。画面下部のアップデートログを確認してください。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'update', $name]);
	}

	/**
	 * コアアップデート実行
     * @param PluginsAdminServiceInterface|PluginsAdminService $service
     * @param string $name
     * @return void|Response
     * @checked
     * @noTodo
	 */
	public function update_core(PluginsAdminServiceInterface $service)
	{
        if (!$this->request->is(['put', 'post'])) return;
        try {
            $request = $this->getRequest();
            $service->updateCore(
                $request->getData('php')?? 'php',
                $request->getData('connection') ?? 'default'
            );
            $this->BcMessage->setInfo(__d('baser_core', 'アップデート処理が完了しました。画面下部のアップデートログを確認してください。'));
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'アップデート処理に失敗しました。画面下部のアップデートログを確認してください。') . $e->getMessage());
            try {
                $service->rollbackCore(
                    $request->getData('currentVersion'),
                    $request->getData('php')
                );
                $this->BcMessage->setError(__d('baser_core', 'コアファイルを元に戻しました。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError($e->getMessage());
            }
        }
        return $this->redirect(['action' => 'update']);
	}

    /**
     * コアアップデートを取得する
     * @param PluginsAdminServiceInterface $service
     * @return Response|null
     * @checked
     * @noTodo
     * @unitTest
     */
	public function get_core_update(PluginsAdminServiceInterface $service)
    {
        if (!$this->request->is(['put', 'post'])) {
            $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
            return $this->redirect(['action' => 'update']);
        }
        $request = $this->getRequest();
        try {
            $service->getCoreUpdate(
                $request->getData('targetVersion')?? '',
                $request->getData('php')?? 'php',
                $request->getData('force'),
            );
        } catch (\Throwable $e) {
            $this->BcMessage->setError($e->getMessage());
        }
        $this->BcMessage->setSuccess(__d('baser_core', '最新版のダウンロードが完了しました。アップデートを実行してください。'));
        return $this->redirect(['action' => 'update']);
    }

    /**
     * 無効化
     *
     * @param PluginsServiceInterface $service
     * @param string $name プラグイン名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(PluginsServiceInterface $service, $name)
    {
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        if ($service->detach(rawurldecode($name))) {
            $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'プラグイン「%s」を無効にしました。'), rawurldecode($name)));
        } else {
            $this->BcMessage->setError(__d('baser_core', 'プラグインの無効化に失敗しました。'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * アンインストール
     *
     * - プラグインのテーブルを削除
     * - プラグインのディレクトリを削除
     *
     * @param PluginsServiceInterface $service
     * @param string $name プラグイン名
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(PluginsServiceInterface $service, $name)
    {
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        try {
            $service->uninstall(rawurldecode($name), $this->request->getData('connection') ?? 'default');
            $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'プラグイン「%s」を削除しました。'), $name));
        } catch (\Exception $e) {
            $this->BcMessage->setError(__d('baser_core', 'プラグインの削除に失敗しました。' . $e->getMessage()));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * プラグインをアップロードする
     *
     * POSTデータで、キー`file` を使って zipファイルを送信する。
     * 送信が完了したら一覧画面にリダイレクトする。
     *
     * @param PluginsAdminServiceInterface|PluginsAdminService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(PluginsAdminServiceInterface $service)
    {
        $this->set($service->getViewVarsForAdd());
        if(!$service->isPluginsDirWritable()) {
            $this->BcMessage->setError(
                __d('baser_core', '{0} に書き込み権限がないためアップロードできません。管理者に問い合わせてください。', BASER_PLUGINS)
            );
            return;
        }
        if ($this->request->is('post')) {
            try {
                /* @var PluginsService $service */
                $name = $service->add($this->getRequest()->getUploadedFiles());
                $this->BcMessage->setInfo(sprintf(__d('baser_core', '新規プラグイン「%s」を追加しました。'), $name));
                $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser_core', 'ファイルのアップロードに失敗しました。') . $e->getMessage());
                $this->redirect(['action' => 'index']);
            }
        }
    }

    /**
     * baserマーケットのプラグインデータを取得する
     * @return void
     * @param PluginsServiceInterface $service
     * @uses get_market_plugins
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_market_plugins(PluginsServiceInterface $service)
    {
        $this->viewBuilder()->disableAutoLayout();
        $baserPlugins = $service->getMarketPlugins();
        if ($baserPlugins) {
            $this->set('baserPlugins', $baserPlugins);
        }
    }

    /**
     * データベースをリセットする
     *
     * @param PluginsServiceInterface $service
     * @param UsersServiceInterface $usersService
     * @return void
     * @uses reset_db
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reset_db(PluginsServiceInterface $service, UsersServiceInterface $usersService)
    {
        if (!$this->request->is('put')) {
            $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
            return;
        }
        $plugin = $service->getByName($this->request->getData('name'));
        try {
            $service->resetDb($this->request->getData('name'), $this->request->getData('connection') ?? 'default');
            $usersService->reLogin($this->request, $this->response);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', '%s プラグインのデータを初期化しました。'), $plugin->title)
            );
        } catch(\Exception $e) {
            $this->BcMessage->setError(__d('baser_core', 'リセット処理中にエラーが発生しました。') . $e->getMessage());
        }
        $this->redirect(['action' => 'install', $plugin->name]);
    }

}
