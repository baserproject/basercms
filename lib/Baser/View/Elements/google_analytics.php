<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] Google Analytics トラッキングコード
 * 
 * $this->BcBaser->googleAnalytics() で呼び出す
 */
?>


<?php if (!empty($siteConfig['google_analytics_id'])): ?>
<script>
	<?php if($useUniversalAnalytics): ?>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', '<?php echo $siteConfig['google_analytics_id'] ?>', 'auto');
	ga('send', 'pageview');
	<?php else: ?>
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $siteConfig['google_analytics_id'] ?>']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	<?php endif ?>
</script>
<?php endif; ?>

