<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] Google Analytics トラッキングコード
 *
 * $this->BcBaser->googleAnalytics() で呼び出す
 */
?>


<?php if (!empty($siteConfig['google_analytics_id'])): ?>
	<script async
			src="https://www.googletagmanager.com/gtag/js?id=<?php echo $siteConfig['google_analytics_id'] ?>"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}

		gtag('js', new Date());
		gtag('config', '<?php echo $siteConfig['google_analytics_id'] ?>');
	</script>
<?php endif; ?>

