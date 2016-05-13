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
            <ul>
                <li><?php $this->BcBaser->link('HOME', '/') ?></li>
                <li class="dropdown-item"><?php $this->BcBaser->link('このテーマについて', '/concept/index') ?>
                    <ul class="dropdown-list">
                        <li><?php $this->BcBaser->link('左サイド2カラム', '/concept/left') ?></li>
                        <li><?php $this->BcBaser->link('右サイド2カラム', '/concept/right') ?></li>
                        <li><?php $this->BcBaser->link('1カラム(パーツ一覧)', '/concept/parts') ?></li>
                    </ul>
                </li>
                <li><?php $this->BcBaser->link('実績', '/works/index') ?></li>
                <li><?php $this->BcBaser->link('会社案内', '/about') ?></li>
                <li><?php $this->BcBaser->link('お問い合わせ', '/contact') ?></li>
            </ul>
        </nav>
    </div>
</header>
