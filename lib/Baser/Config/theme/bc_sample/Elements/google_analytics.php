<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Google Analytics トラッキングコード
 * 呼出箇所：全ページ
 *
 * $this->BcBaser->googleAnalytics() で呼び出す
 *
 * @var array $siteConfig サイト基本設定データ
 */
?>


<?php if (!empty($siteConfig['google_analytics_id'])): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $siteConfig['google_analytics_id'] ?>"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '<?php echo $siteConfig['google_analytics_id'] ?>');
</script>
<?php endif; ?>
