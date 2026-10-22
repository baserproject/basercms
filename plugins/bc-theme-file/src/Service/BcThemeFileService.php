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

namespace BcThemeFile\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BcThemeFile\Utility\BcThemeFileUtil;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Utility\Inflector;

/**
 * BcThemeFileService
 *
 */
class BcThemeFileService implements BcThemeFileServiceInterface
{
    /**
     * fullpathを作成
     * @param string $theme
     * @param string $type
     * @param string $path
     * @return string
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFullpath(string $theme, string $plugin, string $type, string $path)
    {
        // パストラバーサル対策(GHSA-2pj4-v76f-wjvx):
        // $type を許可リスト(BcThemeFileUtil::getTemplateTypeName)で検証する。
        // $type が未検証だと $baseDir = $viewPath . $type . DS のベースディレクトリ自体を
        // 攻撃者入力で移動でき（例: $type = '../../../../tmp'）、後続の境界チェックが
        // 「移動後のベース配下」に収まってしまい無効化される。
        if (!BcThemeFileUtil::getTemplateTypeName($type)) {
            throw new BcException(__d('baser_core', 'テンプレートタイプが不正です。'));
        }

        $assets = [
            'css',
            'js',
            'img'
        ];

        if ($plugin) {
            if (in_array($type, $assets)) {
                $viewPath = BcUtil::getExistsWebrootDir($theme, $plugin, '', 'front');
            } else {
                $viewPath = BcUtil::getExistsTemplateDir($theme, $plugin, '', 'front');
            }
            if(!$viewPath) {
                if (in_array($type, $assets)) {
                    $viewPath = Plugin::path($theme) . 'webroot' . DS . Inflector::underscore($plugin) . DS;
                } else {
                    $viewPath = Plugin::templatePath($theme) . 'plugin' . DS . $plugin . DS;
                }
            }
        } else {
            if (in_array($type, $assets)) {
                $viewPath = Plugin::path($theme) . 'webroot' . DS;
            } else {
                $viewPath = Plugin::templatePath($theme);
            }
        }
        $baseDir = $viewPath . $type . DS;
        $fullPath = $baseDir . $path;

        // パストラバーサル対策:
        // ベースディレクトリがディスク上に存在せず realpath() が false を返す場合でも
        // 検証をスキップせず、必ず正規化したパスで境界チェックを行う。
        // $fullPath は作成先の親ディレクトリ（$path が空の場合は baseDir 自身）を指すため、
        // dirname() ではなく $fullPath 自身がテーマディレクトリ内に収まっているかを検証する
        if (!BcThemeFileUtil::isWithinDir($fullPath, $baseDir)) {
            throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
        }

        return $fullPath;
    }

    /**
     * パスを正規化する（../ を解決する）
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
    {
        return BcThemeFileUtil::normalizePath($path);
    }

    /**
     * テーマ／プラグインの当該テンプレートタイプのベースディレクトリを取得する
     *
     * Admin web 経路（ThemeFilesController::parseArgs）が fullpath を組み立てる規則と同じ。
     * type が etc の場合はテンプレートディレクトリ直下（type ディレクトリを挟まない）。
     *
     * @param string $theme テーマ名（コピー元の場合はプラグイン名のこともある）
     * @param string $plugin プラグイン名（空の場合はテーマ自身）
     * @param string $type テンプレートタイプ
     * @return string 末尾に DS を含むベースディレクトリ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getBaseDir(string $theme, string $plugin, string $type): string
    {
        $assets = ['css', 'js', 'img'];
        if ($plugin) {
            if (in_array($type, $assets)) {
                $viewPath = BcUtil::getExistsWebrootDir($theme, $plugin, '', 'front');
            } else {
                $viewPath = BcUtil::getExistsTemplateDir($theme, $plugin, '', 'front');
            }
            if (!$viewPath) {
                if (in_array($type, $assets)) {
                    $viewPath = Plugin::path($theme) . 'webroot' . DS . Inflector::underscore($plugin) . DS;
                } else {
                    $viewPath = Plugin::templatePath($theme) . 'plugin' . DS . $plugin . DS;
                }
            }
        } else {
            if (in_array($type, $assets)) {
                $viewPath = Plugin::path($theme) . 'webroot' . DS;
            } else {
                $viewPath = Plugin::templatePath($theme);
            }
        }
        if ($type !== 'etc') {
            return $viewPath . $type . DS;
        }
        return $viewPath;
    }

    /**
     * コピー元のパスが、コピー元テーマ／プラグインの当該タイプのディレクトリ配下に収まっているか検証する
     *
     * copyToTheme() のコピー元 $params['fullpath'] は Admin web 経路では parseArgs() が
     * URL から `..` を除去せずに組み立てるため、任意のファイル／ディレクトリをカレントテーマの
     * webroot 配下へコピーして公開 URL から取得できてしまう。コピー元も境界を検証する。
     *
     * @param array $params theme / plugin / type / fullpath を含む配列
     * @return void
     * @throws BcException
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function assertWithinSourceDir(array $params): void
    {
        // theme が渡されない呼び出し（テスト等）はコピー元の基準を決められないため検証しない。
        // Admin web は parseArgs()、API は getFullpath() でそれぞれ検証済み。
        if (empty($params['theme']) || empty($params['type'])) return;
        $baseDir = $this->getBaseDir($params['theme'], (string)($params['plugin'] ?? ''), $params['type']);
        if (!BcThemeFileUtil::isWithinDir((string)($params['fullpath'] ?? ''), $baseDir)) {
            throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
        }
    }

    /**
     * 指定したパスがテーマディレクトリ配下に収まっているか検証する
     *
     * パストラバーサル対策(GHSA-2pj4-v76f-wjvx)の最終防衛線。
     * Admin web 経路の create()/update() はリクエストの fullpath を直接書き込み先に使い
     * getFullpath() の境界チェックを通らないため、実際の書き込み sink でも
     * テーマディレクトリ配下であることを必ず検証する。配下でなければ BcException を投げる。
     *
     * 許可する境界は次のとおり。
     * - $theme を指定した場合: そのテーマのディレクトリ配下のみ
     * - 未指定の場合: インストール済みの全テーマ（Theme / AdminTheme）のディレクトリ配下
     *
     * 以前は App::path('plugins') 配下（plugins/ と vendor/baserproject/ 全体）を許可していたため、
     * fullpath に `..` を含めることで編集中のテーマ以外（コアプラグイン等）へ書き込めた。
     * テーマ以外のプラグインディレクトリは書き込み先として許可しない。
     *
     * 書き込み処理（ThemeFileForm::_execute / copy）は rawurldecode() を 1 回かけた値を使うため、
     * 生の値に加えて 1 回デコードした値も検査する（GHSA-q7g3-qwrc-mp89）
     *
     * @param string $path 検証対象のパス（ファイル or ディレクトリ）
     * @param string|null $theme 書き込み先として許可するテーマ名（省略時は全テーマ）
     * @return void
     * @throws BcException
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function assertWithinThemeDir(string $path, ?string $theme = null): void
    {
        // 書き込み処理（ThemeFileForm::_execute / copy）は rawurldecode() を 1 回かけた値を使うため、
        // 生の値に加えて 1 回デコードした値も検査する（GHSA-q7g3-qwrc-mp89）
        $paths = array_unique([$path, rawurldecode($path)]);
        foreach ($paths as $target) {
            if (!$this->isWithinThemeDir($target, $theme)) {
                throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
            }
        }
    }

    /**
     * パスがテーマディレクトリ配下かどうか
     *
     * @param string $path
     * @param string|null $theme 許可するテーマ名（省略時は全テーマ）
     * @return bool
     */
    private function isWithinThemeDir(string $path, ?string $theme = null): bool
    {
        $themes = $theme ? [$theme] : BcUtil::getAllThemeList();
        foreach ($themes as $name) {
            $root = BcUtil::getPluginPath($name);
            if (!$root) continue;
            if (BcThemeFileUtil::isWithinDir($path, $root)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 読み出し対象のパスがプラグイン配置ルート（App::path('plugins')）配下に収まっているか検証する
     *
     * パストラバーサル対策(GHSA-rqr8-gmx5-xvrr):
     * 読み出し処理（ThemeFilesService::get() / ThemeFoldersService::get()）でも境界を検証し、
     * config/ などテーマ・プラグイン外のファイルを読み出せないようにする。
     * 管理画面ではテーマに加えてプラグイン自体のテンプレートも参照するため、
     * 書き込み用の assertWithinThemeDir() より広いプラグイン配置ルートを境界とする。
     * 配下でなければ BcException を投げる。
     *
     * @param string $path 検証対象のパス（ファイル or ディレクトリ）
     * @return void
     * @throws BcException
     * @checked
     * @noTodo
     */
    protected function assertWithinPluginsDir(string $path): void
    {
        $resolved = realpath($path);
        if ($resolved === false) {
            $resolved = $this->normalizePath($path);
        }
        $resolved = rtrim($resolved, DS) . DS;

        foreach (App::path('plugins') as $root) {
            $realRoot = realpath($root);
            if ($realRoot === false) {
                $realRoot = $this->normalizePath($root);
            }
            $realRoot = rtrim($realRoot, DS) . DS;
            if (str_starts_with($resolved, $realRoot)) {
                return;
            }
        }
        throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
    }
}
