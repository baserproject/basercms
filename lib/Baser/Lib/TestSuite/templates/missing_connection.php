<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib.TestSuite.templates
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */
?>
<?php include dirname(__FILE__) . DS . 'header.php'; ?>
	<div id="content">
		<h2>Missing Test Database Connection</h2>
		<h3><?php echo $exception->getMessage(); ?></h3>
		<pre><?php echo $exception->getTraceAsString(); ?></pre>
	</div>
<?php
include dirname(__FILE__) . DS . 'footer.php';
