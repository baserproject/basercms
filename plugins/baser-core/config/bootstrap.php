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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * bootstrap
 *
 * @checked
 */

use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Validation\Validator;

/**
 * DB設定読み込み
 * ユニットテストの際は、test/bootstrap.php にて実行
 */
if(!BcUtil::isTest()) {
    ConnectionManager::drop('default');
    ConnectionManager::drop('test');
    if (file_exists(CONFIG . 'install.php')) {
        Configure::load('install');
        ConnectionManager::setConfig(Configure::consume('Datasources'));
    }
}

/**
 * CLI で別プロセスとして起動するコマンドに、任意の DB 接続を default として使わせる切替。
 * 環境変数 BC_CONNECTION に接続名（例: test）を指定すると、その接続を default にエイリアスする。
 * プラグインのロード（BcUtil::getEnablePlugins）はこの後段で行われるため、ここで切り替えておくと
 * 子プロセスでも親プロセスと同じ接続の plugins テーブルを参照してプラグインを読み込める。
 * 主にユニットテストが起動する常駐プロセス（MCP サーバー等）のための仕組み。
 */
if (BcUtil::isConsole()) {
    $bcConnection = (string)env('BC_CONNECTION', '');
    if ($bcConnection !== '' && $bcConnection !== 'default' && ConnectionManager::getConfig($bcConnection)) {
        ConnectionManager::alias($bcConnection, 'default');
    }
    unset($bcConnection);
}

/**
 * キャッシュ設定
 * ユニットテスト時に重複して設定するとエラーとなるため判定を入れている
 */
if (!Cache::getConfig('_bc_env_')) {
    if (Configure::read('debug')) {
        Configure::write('Cache._bc_env_.duration', '+2 seconds');
    }
    Cache::setConfig(Configure::consume('Cache'));
}

/**
 * デフォルトバリデーションプロバイダー
 */
Validator::addDefaultProvider('bc', 'BaserCore\Model\Validation\BcValidation');

/**
 * パス定義
 */
require __DIR__ . DS . 'paths.php';

// TODO 未確認
// >>>
// require BASER . DS . 'src' . DS . 'basics.php';
// <<<

/**
 * fullBaseUrl
 * コンソールの場合、CakePHP の ShellDispatcher において、
 * http://localhost で設定されるため https に書き換える
 */
if (BcUtil::isConsole()) {
    Configure::write('App.fullBaseUrl', 'https://localhost');
}

/**
 * X-Powered-Byヘッダーを送信を削除
 * 脆弱性につながるためPHPバージョンを隠蔽
 */
if(!BcUtil::isTest()) {
    header_remove('X-Powered-By');
}
