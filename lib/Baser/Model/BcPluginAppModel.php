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
trigger_error(deprecatedMessage('クラス：BcPluginAppModel', '4.0.0', '5.0.0', 'プラグインは AppModel を直接継承させてください。'), E_USER_DEPRECATED);
class BcPluginAppModel extends AppModel {}