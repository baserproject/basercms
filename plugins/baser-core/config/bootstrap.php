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

