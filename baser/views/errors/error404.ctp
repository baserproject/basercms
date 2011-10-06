<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] 404エラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(strpos($message,'.html') !== false) $message = str_replace('pages/','',$message);
?>

<div id="errorPage">
	<h2>404 NOT FOUND</h2>
	<p class="error"> <strong>
		<?php __('Error'); ?>
		: </strong> <?php echo sprintf(__("The requested address %s was not found on this server.", true), "<strong>'{$message}'</strong>")?> </p>
</div>
