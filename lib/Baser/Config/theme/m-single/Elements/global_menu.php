<?php
/**
 * [ADMIN] グロバールメニュー
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
$prefix = '';
if (Configure::read('BcRequest.agent')) {
	$prefix = '/' . Configure::read('BcRequest.agentAlias');
}
?>


<?php if ($this->BcBaser->isHome()): ?>
<ul>
<li><a href="#top">ホーム</a></li>
<li><a href="<?php $this->BcBaser->root() ?>news/">新着情報</a></li>
<li><a href="#service">事業案内</a></li>
<li><a href="#company">会社案内</a></li>
<li><a href="#recruit">採用情報</a></li>
<li><a href="#contact">お問い合わせ</a></li>
</ul>
<?php else: ?>
<ul>
<li><a href="<?php $this->BcBaser->root() ?>">ホーム</a></li>
<li><a href="<?php $this->BcBaser->root() ?>news/">新着情報</a></li>
<li><a href="<?php $this->BcBaser->root() ?>#service">事業案内</a></li>
<li><a href="<?php $this->BcBaser->root() ?>#company">会社案内</a></li>
<li><a href="<?php $this->BcBaser->root() ?>#recruit">採用情報</a></li>
<li><a href="<?php $this->BcBaser->root() ?>#contact">お問い合わせ</a></li>
</ul>
<?php endif ?>

