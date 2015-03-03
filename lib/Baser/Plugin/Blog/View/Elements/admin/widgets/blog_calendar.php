<?php
/**
 * [ADMIN] ブログカレンダーウィジェット設定
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$title = 'ブログカレンダー';
$description = 'ブログのカレンダーを表示します。';
?>
<?php echo $this->BcForm->label($key . '.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', array('type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id'))) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、対象ブログのブログカレンダーを表示します。</small>