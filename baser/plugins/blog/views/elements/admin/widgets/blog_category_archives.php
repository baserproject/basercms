<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカテゴリー一覧ウィジェット設定
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$title = 'ブログカテゴリー一覧';
$description = 'ブログのカテゴリー一覧を表示します。';
?>

<script type="text/javascript">
$(function(){
	var key = "<?php echo $key ?>";
	$("#"+key+"ByYear").click(function(){
		if($("#"+key+"ByYear").attr('checked') == 'checked') {
			$("#"+key+"Depth").val(1);
			$("#Span"+key+"Depth").slideUp(200);
		} else {
			$("#Span"+key+"Depth").slideDown(200);
		}
	});
	if($("#"+key+"ByYear").attr('checked') == 'checked') {
		$("#"+key+"Depth").val(1);
		$("#Span"+key+"Depth").hide();
	}
});
</script>
	
<?php echo $bcForm->label($key.'.limit','表示数') ?>&nbsp;
<?php echo $bcForm->text($key.'.limit', array('size' => 6)) ?>&nbsp;件<br />
<?php echo $bcForm->label($key.'.view_count', '記事数表示') ?>&nbsp;
<?php echo $bcForm->radio($key.'.view_count', $bcText->booleanDoList(''), array('legend' => false, 'default' => 0)) ?><br />
<?php echo $bcForm->checkbox($key.'.by_year', array('label' => '年別に表示する')) ?><br />
<p id="Span<?php echo $key ?>Depth"><?php echo $bcForm->label($key.'.depth', '深さ') ?>&nbsp;
<?php echo $bcForm->text($key.'.depth', array('size' => 6, 'default' => 1)) ?>&nbsp;階層</p>
<?php echo $bcForm->label($key.'.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $bcForm->select($key.'.blog_content_id', $bcForm->getControlSource('Blog.BlogContent.id'), null, null, false) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、<br />対象ブログのブログカテゴリー一覧を表示します。</small>