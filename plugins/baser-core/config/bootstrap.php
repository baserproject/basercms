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
use Cake\Validation\Validator;

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

// TODO ucmitz dockerに依存しているため、インストーラー実装後に見直し
// ユニットテストの setUp() で状態を書き換える事ができるようにするため、config/setting.php にはキーは記述しないようにする。
if(is_null(Configure::read('BcRequest.isInstalled'))) {
    Configure::write('BcRequest.isInstalled', file_exists(ROOT . DS . 'docker_inited'));
}

/**
 * 文字コードの検出順を指定
 */
if (function_exists('mb_detect_order')) {
    mb_detect_order(Configure::read('BcEncode.detectOrder'));
}

/**
 * コンソール判定
 * BcUtil::isConsole で利用
 */
$_ENV['IS_CONSOLE'] = (substr(php_sapi_name(), 0, 3) === 'cli');

/**
 * fullBaseUrl
 * コンソールの場合、CakePHP の ShellDispatcher において、
 * http://localhost で設定されるため https に書き換える
 */
if(BcUtil::isConsole()) {
    Configure::write('App.fullBaseUrl', 'https://localhost');
}

