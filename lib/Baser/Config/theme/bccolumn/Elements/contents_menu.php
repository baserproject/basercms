<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
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
    <ul <?php if ($level == 2) {echo 'class = "dropdown-list"';} ?>>
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
					$options = ['escape' => true];
					if(!empty($content['Content']['blank_link'])) {
						$options['target'] = '_blank';
					}
                    // トップページは HOME に変更
                    if ($content['Content']['title'] == __d('baser', 'トップページ')) {
                        $content['Content']['title'] = 'HOME';
                    }
                    ?>
                    <?php // ３階層以降は再帰処理を行わない ?>
                    <?php if ($level <= 2): ?>
                        <?php // ２階層目ではカテゴリのindexページを表示しない ?>
                        <?php if (!($level == 2 && $content['Content']['name'] == 'index')): ?>
                            <?php if (!empty($content['children'])): ?>
                                <li class="dropdown-item">
                            <?php else:?>
                                <li>
                            <?php endif ?>
                            <?php $this->BcBaser->link($content['Content']['title'], $content['Content']['url'], $options) ?>
                            <?php if (!empty($content['children'])): ?>
                                <?php $this->BcBaser->element('contents_menu', array('tree' => $content['children'], 'level' => $level + 1, 'currentId' => $currentId)) ?>
                            <?php endif ?>
                                </li>
                        <?php endif ?>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach; ?>
        <?php endif ?>
    </ul>
<?php endif ?>
