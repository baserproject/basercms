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

use BaserCore\Event\BcControllerEventDispatcher;
use BaserCore\Event\BcModelEventDispatcher;
use BaserCore\Event\BcViewEventDispatcher;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Event\EventManager;
use Cake\Validation\Validator;

Configure::config('baser', new PhpConfig());
Configure::load('BaserCore.setting', 'baser');

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
