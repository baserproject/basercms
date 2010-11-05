<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー Step5
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
<div>
	<div class="section"> おめでとうございます！BaserCMSのインストールが無事完了しました！ </div>
</div>
<div>
	<h3>次は何をしますか？</h3>
	<div class="section">
		<ul>
			<li><a href="<?php echo str_replace('/index.php','',$this->base.'/') ?>">トップページに移動</a></li>
			<li><a href="<?php echo $this->base.'/' ?>admin/dashboard">管理者ダッシュボードに移動</a></li>
		</ul>
	</div>
</div>