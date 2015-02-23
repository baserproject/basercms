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
?>	</div>
		</div>
 		<div id="footer">
			<p>
 			<!--PLEASE USE ONE OF THE POWERED BY CAKEPHP LOGO-->
 			<a href="http://www.cakephp.org/" target="_blank">
				<img src="<?php echo $baseDir; ?>img/cake.power.gif" alt="CakePHP(tm) :: Rapid Development Framework" /></a>
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