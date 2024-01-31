<?php
/**
 * ヘッダー
 */
?>


<header id="Header">
    <div class="body-wrap">
        <div id="Logo"><?php $this->BcBaser->logo() ?></div>
        <div id="BtnMenu">
            <?php $this->BcBaser->img('sp/btn_menu.png', array('alt'=>'MENU')); ?>
        </div>
        <nav id="GrobalNavi">
            <?php $this->BcBaser->contentsMenu(); ?>
        </nav>
    </div>
</header>