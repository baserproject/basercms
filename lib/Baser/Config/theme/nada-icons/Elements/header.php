<?php
/**
 * ヘッダー
 */
?>

<div id="Header">
    <div id="Header-page">
        <?php $this->BcBaser->element('search') ?>
        <h1><?php $this->BcBaser->link($this->BcBaser->siteConfig['name'],'/') ?></h1>
    </div>
</div><!--Header-->