<?php
/* SVN FILE: $Id$ */
/**
 * リプレースプレフィックスコンポーネント
 *
 * 既に用意のあるプレフィックスアクションがある場合、
 * 違うプレフィックスでのアクセスを既にあるアクション、ビューに置き換える
 *
 * 【例】
 * /admin/users/login・・・admin_login が呼び出される
 * /mypage/users/login・・・admin_login が呼び出される
 *
 * リクエストしたプレフィックスに適応したアクションがある場合はそちらが優先される
 * リクエストしたプレフィックスに適応したビューが存在する場合はそちらが優先される
 *
 * 【注意事項】
 * ・baserCMS用のビューパスのサブディレクトリ化に依存している。
 * ・リクエストしたプレフィックスに適応したアクションが存在する場合は、ビューの置き換えは行われない。
 * ・Authと併用する場合は、コンポーネントの宣言で、Authより前に宣言しないと認証処理が動作しない。
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers.components
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 * @deprecated		BcReplacePrefixComponent に移行
 */
trigger_error('ReplacePrefixComponent は非推奨です。BcReplacePrefixComponent を利用してください。', E_USER_WARNING);
