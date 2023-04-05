<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * Google Analytics トラッキングコード
 *
 * 呼出箇所：全ページ
 * $this->BcBaser->googleAnalytics() で呼び出す
 *
 * @var \BaserCore\View\BcFrontAppView $this
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
