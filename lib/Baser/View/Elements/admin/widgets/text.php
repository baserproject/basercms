<?php

/**
 * [ADMIN] テキストウィジェット設定
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$title = 'テキスト';
$description = 'テキストやHTMLの入力ができます。';
echo $this->BcForm->textarea($key . '.text', array('cols' => 38, 'rows' => 14));
