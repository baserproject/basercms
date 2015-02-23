<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.TestSuite.templates
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
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
