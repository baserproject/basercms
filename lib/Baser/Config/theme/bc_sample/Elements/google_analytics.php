<?php
/**
 * Google Analytics トラッキングコード
 * 呼出箇所：全ページ
 *
 * $this->BcBaser->googleAnalytics() で呼び出す
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