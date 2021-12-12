<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * bootstrap
 *
 * @checked
 * @note(value="固定ページを実装するまではTODO消化できない")
 */

use BaserCore\Event\BcContainerEventListener;
use BaserCore\Event\BcControllerEventDispatcher;
use BaserCore\Event\BcModelEventDispatcher;
use BaserCore\Event\BcViewEventDispatcher;
use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\EventManager;
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
 * グローバルイベント登録
 */
$event = EventManager::instance();
$event->on(new BcControllerEventDispatcher());
$event->on(new BcModelEventDispatcher());
$event->on(new BcViewEventDispatcher());
$event->on(new BcContainerEventListener());

// TODO 未実装
// >>>
//$event->on(new PagesControllerEventListener());
//$event->on(new ContentFoldersControllerEventListener());
// <<<

/**
 * パス定義
 */
require __DIR__ . DS . 'paths.php';

// TODO 未確認
// >>>
// require BASER . DS . 'src' . DS . 'basics.php';
// <<<

if(!defined('BC_INSTALLED')) {
    define('BC_INSTALLED', BcUtil::isInstalled());
}

