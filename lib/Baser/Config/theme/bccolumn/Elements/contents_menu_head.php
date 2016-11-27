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
<?php if (isset($tree)): ?>
    <ul class="dropdown-list">
        <?php if (isset($tree)): ?>
            <?php foreach ($tree as $contentKey => $content): ?>
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
                    ?>
                    <?php
                    /**
                     * カテゴリのindexページは表示しない
                     * ２階層目移行は再帰処理を行わない
                     */
                    ?>
                    <?php if ($content['Content']['name'] !== 'index'): ?>
                        <li><?php $this->BcBaser->link($content['Content']['title'], $content['Content']['url'], $options) ?></li>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach; ?>
        <?php endif ?>
    </ul>
<?php endif ?>
<?php
/**
 * Created by PhpStorm.
 * User: catchup_Abe
 * Date: 2016/11/26
 * Time: 21:41
 */