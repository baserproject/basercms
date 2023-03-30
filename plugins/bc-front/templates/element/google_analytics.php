<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Google Analytics トラッキングコード
 * 呼出箇所：全ページ
 *
 * $this->BcBaser->googleAnalytics() で呼び出す
 *
 * @var string $googleAnalyticsId
 */
if(!$googleAnalyticsId) return;
?>


<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo h($googleAnalyticsId) ?>"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '<?php echo h($googleAnalyticsId) ?>');
</script>
