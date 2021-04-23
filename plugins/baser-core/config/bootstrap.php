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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Validation\Validator;

Configure::config('baser', new PhpConfig());
Configure::load('BaserCore.setting', 'baser');

/**
 * デフォルトバリデーションプロバイダー
 */
Validator::addDefaultProvider('bc', 'BaserCore\Model\Validation\BcValidation');

/**
 * パス定義
 */
require __DIR__ . DS . 'paths.php';
// require BASER . DS . 'src' . DS . 'basics.php';
