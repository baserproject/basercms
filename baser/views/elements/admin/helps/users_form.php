<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー一覧　ヘルプ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<ul>
	<li>ログイン用のユーザーアカウントを登録する事ができます。</li>
	<?php if($this->action == 'admin_edit'): ?><li>パスワード欄は変更する場合のみ入力します。</li><?php endif ?>
</ul>
