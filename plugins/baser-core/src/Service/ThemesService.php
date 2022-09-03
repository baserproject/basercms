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

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Xml;

/**
 * ThemesService
 */
class ThemesService implements ThemesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 単一データ取得
     */
    public function get(): array
    {
        return [];
    }

    /**
     * 一覧データ取得
     * @checked
     * @noTodo
     */
    public function getIndex(): array
    {
        $themeNames = BcUtil::getThemeList();
        $themes = [];
        $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
        foreach($themeNames as $value) {
            $themes[] = $pluginsTable->getPluginConfig($value);
        }
        return $themes;
    }

    /**
     * 初期データのセットを取得する
     *
     * @param string $theme
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDefaultDataPatterns($theme = '', $options = [])
    {
        if (!$theme) $theme = Configure::read('BcApp.defaultFrontTheme');
        $options = array_merge(['useTitle' => true], $options);
        $dataPath = dirname(BcUtil::getDefaultDataPath('BaserCore', $theme));

        if ($theme !== Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-') &&
            $dataPath === dirname(BcUtil::getDefaultDataPath('BaserCore'))) {
            return [];
        }

        $patterns = [];
        $Folder = new Folder($dataPath);
        $files = $Folder->read(true, true);
        if ($files[0]) {
            foreach($files[0] as $pattern) {
                if ($options['useTitle']) {
                    $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
                    $themeRecord = $pluginsTable->getPluginConfig($theme);
                    if ($themeRecord && $themeRecord->title) {
                        $title = $themeRecord->title;
                    } else {
                        $title = $theme;
                    }
                    $patternName = $title . ' ( ' . $pattern . ' )';
                } else {
                    $patternName = $pattern;
                }
                $patterns[$theme . '.' . $pattern] = $patternName;
            }
        }
        return $patterns;
    }

    /**
     * 新しいテーマをアップロードする
     */
    public function add(): bool
    {
        return true;
    }

    /**
     * テーマを適用する
     * @param string $theme
     * @return array 適用完了後に表示するメッセージ
     * @checked
     * @noTodo
     */
    public function apply(string $theme): array
    {
        // テーマ梱包のプラグインを無効化
        $this->detachCurrentThemesPlugins();

        // テーマを適用
        BcUtil::includePluginClass($theme);
        Plugin::getCollection()->get($theme)->applyAsTheme($theme);

        // テーマが梱包するプラグイン情報を取得
        $info = $this->getThemesPluginsInfo($theme);

        // テーマが梱包するプラグインをインストール
        $this->installThemesPlugins($theme);

        // テーマが初期データを保有している場合の情報を取得
        return $this->getThemesDefaultDataInfo($theme, $info);
    }

    /**
     * 現在のテーマのプラグインを無効化する
     * @checked
     */
    private function detachCurrentThemesPlugins()
    {
        // TODO ucmitz 2022/09/03 ryuring
        // テーマプラグインの仕組みを実装してからテストを作成する
        $plugins = BcUtil::getCurrentThemesPlugins();
        foreach($plugins as $plugin) {
            /* @var PluginsService $pluginsService */
            $pluginsService = $this->getService(PluginsServiceInterface::class);
            $pluginsService->detach($plugin);
        }
    }

    /**
     * 指定したテーマが梱包するプラグイン情報を取得
     * @param string $theme
     * @return array|string[]
     * @checked
     * @noTodo
     */
    private function getThemesPluginsInfo(string $theme)
    {
        $info = [];
        $themePath = BcUtil::getPluginPath($theme);
        $Folder = new Folder($themePath . 'Plugin');
        $files = $Folder->read(true, true, false);
        if (!empty($files[0])) {
            $info = array_merge($info, [
                __d('baser', 'このテーマは下記のプラグインを同梱しています。')
            ]);
            foreach($files[0] as $file) {
                $info[] = '	・' . $file;
            }
        }
        return $info;
    }

    /**
     * テーマが初期データを保有している場合の情報を取得
     * @param string $theme
     * @param array $info
     * @return array|mixed|string[]
     */
    private function getThemesDefaultDataInfo(string $theme, array $info = [])
    {
        $path = BcUtil::getDefaultDataPath('BaserCore', $theme);
        if (preg_match('/\/(' . $theme . '|' . Inflector::dasherize($theme) . ')\//', $path)) {
            if ($info) $info = array_merge($info, ['']);
            $info = array_merge($info, [
                __d('baser', 'このテーマは初期データを保有しています。'),
                __d('baser', 'Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。'),
            ]);
        }
        return $info;
    }

    /**
     * テーマが梱包するプラグインをインストールする
     * @param string $theme
     * @throws \Exception
     * @checked
     */
    private function installThemesPlugins(string $theme)
    {
        // TODO ucmitz 2022/09/03 ryuring
        // テーマプラグインの仕組みを実装してからテストを作成する
        /* @var PluginsService $pluginsService */
        $pluginsService = $this->getService(PluginsServiceInterface::class);
        $plugins = BcUtil::getCurrentThemesPlugins();
        // テーマ梱包のプラグインをインストール
        foreach($plugins as $plugin) {
            $pluginsService->install($plugin);
        }
    }

    /**
     * 初期データを読み込む
     */
    public function loadDefaultDataPattern(): bool
    {
        return true;
    }

    /**
     * コピーする
     */
    public function copy(): bool
    {
        return true;
    }

    /**
     * 削除する
     */
    public function delete(): bool
    {
        return true;
    }

    /**
     * 利用中のテーマをダウンロードする
     */
    public function download()
    {
        return true;
    }

    /**
     * 初期データをダウンロードする
     */
    public function downloadDefaultDataPattern()
    {
        return true;
    }

    /**
     * baserマーケットのテーマ一覧を取得する
     */
    public function getMarketThemes(): array
    {
        if (Configure::read('debug')) {
            Cache::delete('baserMarketThemes');
        }
        $baserThemes = Cache::read('baserMarketThemes', '_bc_env_');
        if (!$baserThemes) {
            $Xml = new Xml();
            try {
                $client = new Client([
                    'host' => '',
                    'redirect' => true,
                ]);
                $response = $client->get(Configure::read('BcLinks.marketThemeRss'));
                $baserThemes = $Xml->build($response->getBody()->getContents());
                $baserThemes = $Xml->toArray($baserThemes->channel);
                $baserThemes = $baserThemes['channel']['item'];
            } catch (BcException $e) {
                return [];
            }
            Cache::write('baserMarketThemes', $baserThemes, '_bc_env_');
        }
        if ($baserThemes) {
            return $baserThemes;
        }
        return [];
    }

    /**
     * コアの初期データを読み込む
     */
    public function resetData(): bool
    {
        return true;
    }

}
