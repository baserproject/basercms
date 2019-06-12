<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] テキストウィジェット設定
 */
$title = __d('baser', 'テキスト');
$description = __d('baser', 'テキストやHTMLの入力ができます。');
echo $this->BcForm->input($key . '.text', ['type' => 'textarea', 'cols' => 38, 'rows' => 14]);
