<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $bcBaser->xmlHeader() ?>
<?php $bcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php $bcBaser->charset() ?>
<?php $bcBaser->title() ?>
<?php $bcBaser->metaDescription() ?>
<?php $bcBaser->metaKeywords() ?>
<?php $bcBaser->icon() ?>
<?php $bcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
<?php $bcBaser->css('style') ?>
    
<?php $bcBaser->js(array(
    'jquery-1.7.2.min',
    'functions',
    'startup',
    'jquery.bxSlider.min',
    'jquery.easing.1.3',
    'nada-icons'
)) ?>
<?php $bcBaser->scripts() ?>
<?php $bcBaser->element('google_analytics') ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>">


<?php $bcBaser->header() ?>

<div id="Page">
    <div id="Wrap" class="clearfix">
    

        <?php $bcBaser->element('sidebox') ?>

        <div id="Beta">
            <?php if(!$bcBaser->isTop()): ?>
            <div id="Navigation">
                <?php $bcBaser->element('crumbs'); ?>
            </div>
            <?php endif ?>

            <?php if($bcBaser->isTop()): ?>
            <div id="top-main">
                <?php //$bcBaser->img('./top-main.png'); ?>
                <div id="slider">
                  <div><?php $bcBaser->img('./slider/01.jpg'); ?></div>
                  <div><?php $bcBaser->img('./slider/02.jpg'); ?></div>
                  <div><?php $bcBaser->img('./slider/03.jpg'); ?></div>
                  <div><?php $bcBaser->img('./slider/04.jpg'); ?></div>
                  <div><?php $bcBaser->img('./slider/05.jpg'); ?></div>
                </div>
            </div>
            <?php 
            /*
            *スライダーは色々設定ができるので参考にして下さい  http://zxcvbnmnbvcxz.com/demonstration/bxslide.html 
            *設定ファイルは js/nada-icons です
            */
            ?>
            <?php endif ?>

            <div id="ContentsBody" class="clearfix">
                <?php if($bcBaser->isHome()): ?>
                <?php $bcBaser->element('toppage') ?>
                <?php else: ?>
                <div id="ContentsBody" class="subpage">
                    <?php $bcBaser->flash() ?>
                    <?php $bcBaser->content() ?>
                    <?php $bcBaser->element('contents_navi') ?>
                    <div class="to-top"> <a href="#Page"><?php $bcBaser->img('./icons_up.png'); ?>ページトップへ戻る</a></div>
                </div>
                <?php endif ?>

            <div id="top-contents-main">
                <div id="top-main-telfax-title">お気軽にお問い合わせ下さい</div>
                <div id="top-main-telfax-left">
                    <div id="top-main-telfax-tel"><?php $bcBaser->img('./icons/icons_ico_squ_07.png'); ?><?php $bcBaser->img('./icons_tel.png',array('class' => 'telfax-tel')); ?></div>
                    <div id="top-main-telfax-fax"><?php $bcBaser->img('./icons/icons_ico_squ_08.png'); ?><?php $bcBaser->img('./icons_fax.png',array('class' => 'telfax-fax')); ?></div>
                </div>
                <div id="top-main-telfax-right">
                    <div id="top-main-contact"><?php $bcBaser->img('./icons_contact.png',array('url' => '/contact')); ?></div>
                    <div id="top-main-serch"><?php $bcBaser->element('search') ?></div>
                </div>
            </div>

            </div>
        </div><!--Bata-->

    </div><!--Wrap-->

    
    
</div><!--Page-->
<?php $bcBaser->footer() ?>
<?php $bcBaser->func() ?>
</body>
</html>