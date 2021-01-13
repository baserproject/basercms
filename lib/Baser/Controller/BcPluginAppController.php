<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcPluginAppController
 *
 * @deprecated 5.0.0 since 4.0.0 プラグインは AppController を直接継承させる
 */
CakeLog::write(LOG_ALERT, __d('baser', 'クラス：BcPluginAppController 継承は、バージョン 4.0.0 より非推奨となりました。バージョン 5.0.0 で BcPluginAppController は削除される予定です。プラグインは AppController を直接継承してください。'));

class BcPluginAppController extends AppController
{
}
