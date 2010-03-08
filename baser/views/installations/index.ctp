<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー初期ページ
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<p>WEBサイト基本制作支援プロジェクト「BaserCMS」のインストールを開始します。<br />よろしければ「次のステップ」ボタンをクリックして下さい。</p>
<p>SQLite3（PHP5のみ）やBaserCMS標準のCSVデータベースを利用すれば、<br />インストールにデータベースサーバーは必要ありません。</p>
<p<small>※ 膨大なデータの操作、データベースによる複雑な処理が必要な場合は、MySQLなどのデータベースサーバーの利用を推奨します。</small></p>

<div>
    <form action="<?php echo $this->base ?>/installations/step2" method="post">
          <button class='btn-red button' id='startbtn' type='submit' ><span>次のステップ</span></button>
	</form>
</div>