<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログカレンダーウィジェット設定
 */
$title = __d('baser', 'ブログカレンダー');
$description = __d('baser', 'ブログのカレンダーを表示します。');
?>
<?php echo $this->BcForm->label($key . '.blog_content_id', __d('baser', 'ブログ')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id')]) ?>
<br/>
<small><?php echo __d('baser', 'ブログページを表示している場合は、上記の設定に関係なく、対象ブログのブログカレンダーを表示します。') ?></small>
