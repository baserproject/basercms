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
	<h2>PHPUnit is not installed!</h2>
	<p>You must install PHPUnit to use the CakePHP(tm) Test Suite.</p>
	<p>PHPUnit can be installed with pear, using the pear installer.</p>
	<p>To install with the PEAR installer run the following commands:</p>
	<ul>
		<li><code>pear config-set auto_discover 1</code></li>
		<li><code>pear install pear.phpunit.de/PHPUnit</code></li>
	</ul>
	<p>Once PHPUnit is installed make sure its located on PHP's <code>include_path</code> by checking your php.ini</p>
	<p>For full instructions on how to <a href="http://www.phpunit.de/manual/current/en/installation.html" target="_blank">install PHPUnit, see the PHPUnit installation guide</a>.</p>
	<p><a href="http://github.com/sebastianbergmann/phpunit" target="_blank">Download PHPUnit</a></p>
</div>
<?php
include dirname(__FILE__) . DS . 'footer.php';
