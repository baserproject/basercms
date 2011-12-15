<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー Step5
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
$adminPrefix = Configure::read('Routing.admin');
?>
<div>
	<div class="section"> おめでとうございます！baserCMSのインストールが無事完了しました！ </div>
</div>
<div>
	<h3>次は何をしますか？</h3>
	<div class="section">
		<ul>
			<li><a href="<?php echo str_replace('/index.php','',$this->base.'/') ?>">トップページに移動</a></li>
			<li><a href="<?php echo $this->base.'/'.$adminPrefix ?>/dashboard">管理者ダッシュボードに移動</a></li>
		</ul>
	</div>
</div>