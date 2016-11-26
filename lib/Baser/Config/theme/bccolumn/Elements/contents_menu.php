<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] サイトマップ
 *
 * カテゴリの階層構造を表現する為、再帰呼び出しを行う
 * $this->BcBaser->contentsMenu() で呼び出す
 */

if (!isset($level)) {
    $level = 1;
}
if(!isset($currentId)) {
    $currentId = null;
}
?>
<header id="Header">
    <div class="body-wrap">
        <div id="Logo"><?php $this->BcBaser->logo() ?></div>
        <div id="BtnMenu">
            <?php $this->BcBaser->img('sp/btn_menu.png', array('alt'=>'MENU')); ?>
        </div>
        <nav id="GrobalNavi">
            <?php if (isset($tree)): ?>
                <ul>
                    <?php if (isset($tree)): ?>
                        <?php foreach ($tree as $content): ?>
                            <?php if ($content['Content']['title']): ?>
                                <?php
                                if(!empty($content['Content']['exclude_menu'])) {
                                    continue;
                                }
                                $liClass = 'menu-content li-level-' . $level;
                                if($content['Content']['id'] == $currentId) {
                                    $liClass .= ' current';
                                }
                                $options = [];
                                if(!empty($content['Content']['blank_link'])) {
                                    $options = ['target' => '_blank'];
                                }
                                // トップページは HOME に変更
                                if ($content['Content']['title'] == 'トップページ') {
                                    $content['Content']['title'] = 'HOME';
                                }
                                ?>
                                <?php if (!empty($content['children'])): ?>
                                    <li class="dropdown-item">
                                <?php else:?>
                                    <li>
                                <?php endif ?>
                                <?php $this->BcBaser->link($content['Content']['title'], $content['Content']['url'], $options) ?>
                                <?php if (!empty($content['children'])): ?>
                                    <?php $this->BcBaser->element('contents_menu_head', array('tree' => $content['children'], 'level' => $level + 1, 'currentId' => $currentId)) ?>
                                <?php endif ?>
                                </li>
                            <?php endif ?>
                        <?php endforeach; ?>
                    <?php endif ?>
                </ul>
            <?php endif ?>
        </nav>
    </div>
</header>
