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
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Cache\Cache;
use Cake\Core\Configure;
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
        if(!$theme) $theme = Configure::read('BcApp.defaultFrontTheme');
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
     * インストール
     */
    public function apply(): bool
    {
        return true;
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
