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

use BaserCore\Error\BcException;
use BaserCore\Service\Admin\ThemesAdminServiceInterface;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Vendor\Simplezip;
use Cake\Filesystem\Folder;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ThemesController
 */
class ThemesController extends BcAdminAppController
{

    /**
     * テーマ一覧
     *
     * @param ThemesAdminServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ThemesAdminServiceInterface $service)
    {
        $this->set($service->getViewVarsForIndex($service->getIndex()));
    }

    /**
     * テーマをアップロードして適用する
     * @param ThemesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(ThemesServiceInterface $service)
    {
        if ($this->request->is('post')) {
            try {
                $name = $service->add($this->getRequest()->getUploadedFiles());
                $this->BcMessage->setInfo(__d('baser_core', 'テーマファイル「{0}」を追加しました。', $name));
                $this->redirect(['action' => 'index']);
            } catch (BcException $e) {
                $this->BcMessage->setError(__d('baser_core', 'ファイルのアップロードに失敗しました。') . $e->getMessage());
            }
        }
    }

    /**
     * baserマーケットのテーマデータを取得する
     * @param ThemesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_market_themes(ThemesServiceInterface $service)
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->set('baserThemes', $service->getMarketThemes());
    }

    /**
     * 初期データセットを読み込む
     *
     * @param ThemesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function load_default_data_pattern(ThemesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        if (empty($this->getRequest()->getData('default_data_pattern'))) {
            $this->BcMessage->setError(__d('baser_core', '不正な操作です。'));
            return $this->redirect(['action' => 'index']);
        }
        try {
            $result = $service->loadDefaultDataPattern(
                BcUtil::getRootTheme(),
                $this->getRequest()->getData('default_data_pattern')
            );
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', '初期データの読み込みに失敗しました。' . $e->getMessage()));
            return $this->redirect(['action' => 'index']);
        }
        if (!$result) {
            $this->BcMessage->setError(__d('baser_core', '初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。'));
            return $this->redirect(['action' => 'index']);
        }
        $this->BcMessage->setInfo(__d('baser_core', '初期データの読み込みが完了しました。'));
        return $this->redirect(['action' => 'index']);
    }

    /**
     * テーマをコピーする
     *
     * @param ThemesServiceInterface $service
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(ThemesServiceInterface $service, $theme)
    {
        $this->request->allowMethod(['post']);
        if (!$theme) $this->notFound();
        try {
            $service->copy($theme);
            $this->BcMessage->setInfo(__d('baser_core', 'テーマ「{0}」をコピーしました。', $theme));
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage()));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * テーマを削除する
     *
     * @param ThemesServiceInterface $service
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ThemesServiceInterface $service, $theme)
    {
        $this->request->allowMethod(['post']);
        if (!$theme) $this->notFound();
        try {
            $service->delete($theme);
            $this->BcMessage->setInfo(__d('baser_core', 'テーマ「{0}」を削除しました。', $theme));
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage()));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * テーマを適用する
     *
     * @param ThemesServiceInterface $service
     * @param string $theme
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(ThemesServiceInterface $service, string $theme)
    {
        $this->request->allowMethod(['post']);
        if (!$theme) $this->notFound();
        try {
            $info = $service->apply($this->getRequest()->getAttribute('currentSite'), $theme);
            $message = [__d('baser_core', 'テーマ「{0}」を適用しました。', $theme)];
            if ($info) $message = array_merge($message, [''], $info);
            $this->BcMessage->setInfo(implode("\n", $message));
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'テーマの適用に失敗しました。') . $e->getMessage());
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * 初期データセットをダウンロードする
     *
     * @param ThemesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download_default_data_pattern(ThemesServiceInterface $service)
    {
        $this->autoRender = false;
        $tmpDir = $service->createDownloadDefaultDataPatternToTmp();
        $Simplezip = new Simplezip();
        $Simplezip->addFolder($tmpDir);
        $Simplezip->download('default');
        BcUtil::emptyFolder($tmpDir);
    }

    /**
     * ダウンロード
     *
     * @param ThemesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function download(ThemesServiceInterface $service): void
    {
        $this->autoRender = false;
        $theme = BcUtil::getCurrentTheme();
        $tmpDir = $service->createDownloadToTmp($theme);
        $simplezip = new Simplezip();
        $simplezip->addFolder($tmpDir);
        $simplezip->download($theme);
        $folder = new Folder();
        $folder->delete($tmpDir);
    }

    /**
     * スクリーンショットを表示
     * @param string $theme
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function screenshot($theme)
    {
        $this->autoRender = false;
        $pluginPath = BcUtil::getPluginPath($theme);
        if (!file_exists($pluginPath . 'screenshot.png')) {
            $this->notFound();
        }
        return $this->getResponse()->withFile($pluginPath . 'screenshot.png');
    }

}
