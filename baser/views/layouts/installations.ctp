<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー用レイアウト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views.layout
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php echo @$declareXml ?><?php echo $html->docType('xhtml-trans') ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php echo $html->charset() ?>
<title>
<?php echo $title_for_layout ?>　コーポレートサイトにちょうどいいCMS - BaserCMS -
</title>
<?php echo $html->meta('description','BaserCMSのインストーラー') ?>
<?php echo $html->meta('icon') ?>
<?php echo $html->css('font_small','stylesheet',array('title'=>'small')) ?>
<?php echo $html->css('font_medium','stylesheet',array('title'=>'medium')) ?>
<?php echo $html->css('font_large','stylesheet',array('title'=>'large')) ?>
<?php echo $html->css('import') ?>
<?php $baser->css(array('jquery-ui-1.7.2/ui.all','colorbox/colorbox')) ?>
<!--[if IE]><?php $baser->js(array('excanvas')) ?><![endif]-->
<?php $baser->js(array('jquery-1.3.2.min',
                            'jquery-ui-1.7.2.custom.min',
                            'i18n/ui.datepicker-ja',
                            'jquery.bt.min',
                            'jquery.colorbox-min',
                            'jquery.corner',
                            'functions',
                            'styleswitcher',
                            'startup')) ?>
<?php $baser->scripts() ?>
</head>
<body id="<?php $baser->contentsName() ?>">


<!-- begin page -->
<div id="page">


    <!-- begin gradationShadow -->
    <div id="gradationShadow">


        <!-- begin header -->
        <?php $baser->element('installations_header') ?>
        <!-- end header -->


        <!-- begin contents -->
        <div id="contents">


            <!-- navigation -->
            <!--<div id="navigation">
            <?php $baser->element('navi',array('title_for_element'=>$title_for_layout)); ?>
            </div>-->


            <!-- begin alfa -->
            <div id="alfa" >


                <!-- begin contentsBody -->
                <div id="contentsBody">

                    <?php if($this->name != 'CakeError'): ?>
                        <?php if ($title_for_layout): ?><h2><?php echo $title_for_layout; ?></h2><?php endif ?>
                    <?php endif; ?>

                    <?php if ($session->check('Message.flash')): ?><?php $session->flash() ?><?php endif ?>

                    <?php echo $content_for_layout; ?>

                    <?php $resets = array('step3','step4') ?>
                    <?php if(in_array($this->action,$resets)): ?>
                        <p><small>
                            <?php $baser->link('≫ インストールを完全に最初からやり直す場合はコチラをクリックしてください','/installations/reset') ?>
                        </small></p>
                    <?php endif ?>

                </div>
                <!-- end contentsBody -->


            </div>
            <!-- end alfa -->


            <!-- begin beta -->
            <?php $baser->element('sidebar') ?>
            <!-- end beta -->


            <div class="to-top">
            <a href="#page">このページの先頭へ戻る</a>
            </div>


        </div>
        <!-- end contents -->


        <!-- begin footer -->
        <?php $baser->element('footer') ?>
        <!-- end footer -->


    </div>
    <!-- end gradationShadow -->


</div>
<!-- end page -->


<?php echo $cakeDebug; ?>
</body>
</html>