<?php
/**
 * [ADMIN] ブログ最近の投稿ウィジェット設定
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
$title = '最近の投稿';
$description = 'ブログの最近の投稿を表示します。';
?>
<?php echo $this->BcForm->label($key . '.count', '表示数') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.count', array('type' => 'text', 'size' => 6, 'default' => 5)) ?>&nbsp;件<br />
<?php echo $this->BcForm->label($key . '.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', array('type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id'))) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、対象ブログの最近の投稿を表示します。</small>