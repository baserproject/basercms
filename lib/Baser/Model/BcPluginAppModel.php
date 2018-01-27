<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * Class BcPluginAppModel
 *
 * @deprecated 5.0.0 since 4.0.0 プラグインは AppModel を直接継承させる
 */
CakeLog::write(LOG_ALERT, __d('baser', 'モデル：BcPluginAppModelの 継承は、バージョン 4.0.0 より非推奨となりました。バージョン 5.0.0 で BcPluginAppModel は削除される予定です。プラグインは AppModel を直接継承してください。'));
class BcPluginAppModel extends AppModel {}