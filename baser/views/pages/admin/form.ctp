<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ページ フォーム
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
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('Page') ?>
<?php echo $formEx->hidden('Page.id') ?>
<?php echo $formEx->hidden('Page.no') ?>
<?php echo $formEx->hidden('Page.sort') ?>
<?php echo $formEx->hidden('Page.theme') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('Page.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
    <?php $categories = $formEx->getControlSource('page_category_id') ?>
    <?php if($categories): ?>
    <tr>
		<th class="col-head"><?php echo $formEx->label('Page.page_category_id', 'カテゴリ') ?></th>
		<td class="col-input"><?php echo $formEx->select('Page.page_category_id',$categories,null,array('escape'=>false)) ?><?php echo $formEx->error('Page.page_category_id') ?>&nbsp;</td>
	</tr>
    <?php else: ?>
        <?php echo $formEx->hidden('Page.page_category_id') ?>
    <?php endif ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Page.name', 'ページ名') ?></th>
		<td class="col-input">
            <?php echo $formEx->text('Page.name', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('Page.name') ?>
            <?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextName" class="helptext">
                <ul>
                    <li>ページ名はURLに利用します。</li>
                    <li>.htmlなどの拡張子はつけず純粋なページ名を入力します。</li>
                </ul>
            </div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.title', 'タイトル') ?></th>
		<td class="col-input">
            <?php echo $formEx->text('Page.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('Page.title') ?>
            <?php echo $html->image('help.png',array('id'=>'helpTitle','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextTitle" class="helptext">
                <ul>
                    <li>タイトルはTitleタグに利用します。</li>
                    <li>タイトルには、システム設定で設定されたWEBサイト名が自動的に追加されます。</li>
                    <li>タイトルタグの出力するには、レイアウトテンプレートに次のように記述します。<br />
                        &lt;?php $baser->title() ?&gt;</li>
                </ul>
            </div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.description', '説明文') ?></th>
		<td class="col-input">
            <?php echo $formEx->textarea('Page.description', array('cols'=>60,'rows'=>2)) ?><?php echo $formEx->error('Page.description') ?>
            <?php echo $html->image('help.png',array('id'=>'helpDescription','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextDescription" class="helptext">
                <ul>
                    <li>説明文はMetaタグのdescriptionに利用します。</li>
                    <li>他のページと重複しない説明文を推奨します。</li>
                </ul>
            </div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.contents', '本文') ?></th>
        <td class="col-input">
            <?php echo $ckeditor->textarea('Page.contents',array('cols'=>60, 'rows'=>20)) ?>
            <?php echo $formEx->error('Page.contents') ?>&nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.status', '公開状態') ?></th>
		<td class="col-input">
            <?php echo $formEx->radio('Page.status', $textEx->booleanDoList("公開"),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $formEx->error('Page.status') ?>
            &nbsp;
		</td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php $baser->link('確　認',array('action'=>'preview', $formEx->value('Page.id')), array('class'=>'btn-green button','target'=>'_blank')) ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削除する',array('action'=>'delete', $formEx->value('Page.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('Page.name')),false); ?>
<?php endif ?>
</div>
