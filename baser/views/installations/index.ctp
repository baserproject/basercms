<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー初期ページ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<p>コーポレートサイトにちょうどいいCMS「baserCMS」のインストールを開始します。<br />
	よろしければ「次のステップ」ボタンをクリックしてください。</p>

<p>SQLite3（PHP5のみ）やbaserCMS標準のCSVデータベースを利用すれば、<br />
	インストールにデータベースサーバーは必要ありません。</p>

<p<small>※ 膨大なデータの操作、データベースによる複雑な処理が必要な場合は、MySQLなどのデータベースサーバーの利用を推奨します。</small></p>

<div>
	<form action="<?php echo $this->base ?>/installations/step2" method="post">
		<button class='btn-red button' id='startbtn' type='submit' ><span>次のステップ</span></button>
	</form>
</div>
