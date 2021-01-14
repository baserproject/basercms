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

/**
 * @var string $baseDir
 */
?>    </div>
</div>
<div id="footer">
	<p>
		<!--PLEASE USE ONE OF THE POWERED BY CAKEPHP LOGO-->
		<a href="http://www.cakephp.org/" target="_blank">
			<img src="<?php echo $baseDir; ?>img/cake.power.gif" alt="CakePHP(tm) :: Rapid Development Framework"/></a>
	</p>
</div>
<?php
App::uses('View', 'View');
$null = null;
$View = new View($null, false);
echo $View->element('sql_dump');
?>
</div>
</body>
</html>
