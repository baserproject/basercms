<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ページ一覧
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
<script type="text/javascript">
    $(document).ready(function(){
        <?php if($form->value('Page.open')): ?>
            $("#PageFilterBody").show();
        <?php endif ?>
    });
</script>

<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ページ管理では、ブログやメールフォーム以外のWEBページを管理する事ができます。</p>
	<ul>
		<li>新しいページを登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>公開状態を設定する事ができ、一覧各ページ横の「確認」ボタンをクリックすると非公開のページも確認する事ができます。</li>
		<li>各ページは分類分け用の「カテゴリ」に属させる事ができ、階層構造のURLを実現できます。</li>
		<li>管理画面内では、公開状態、カテゴリによりページの検索を行う事ができます。<br />
			検索するには、すぐ下の「検索」をクリックして検索条件を表示させます。</li>
		<li>オーサリングツールでの制作に慣れている方向けに、ファイルをアップロードしてデータベースに一括で読み込む機能を備えています。<br />
			ページを読み込むには、特定のフォルダにページテンプレートをアップロードして、サイドメニューの「ページテンプレート読込」を実行します。<br />
			<a href="http://basercms.net/manuals/designers/10.html" target="_blank">≫ ページテンプレート読込について</a></li>
	</ul>
	<div class="example-box">
		<div class="head">（例）ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[BaserCMS設置URL]/about.html</p>
		<div class="head">（例）カテゴリ「company」に属する、ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[BaserCMS設置URL]/company/about.html</p>
	</div>
</div>


<h3><a href="javascript:void(0);" class="slide-trigger" id="PageFilter">検索</a></h3>
<div class="function-box corner10" id="PageFilterBody" style="display:none">
    <?php echo $formEx->create('Page',array('url'=>array('action'=>'index'))) ?>
    <p>
    <?php $pageCategories = $formEx->getControlSource('Page.page_category_id') ?>
    <?php if($pageCategories): ?>
        <small>カテゴリ</small>
        <?php echo $formEx->select('Page.page_category_id', $pageCategories, null,array('escape'=>false)) ?>　
    <?php endif ?>
    <small>公開状態</small>
        <?php echo $formEx->select('Page.status', $textEx->booleanMarkList()) ?>　
    </p>
        <?php echo $formEx->hidden('Page.open',array('value'=>true)) ?>
    <div class="align-center">
        <?php echo $formEx->submit('検　索',array('div'=>false,'class'=>'btn-orange button')) ?>
    </div>
</div>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TablePages">
<tr>
	<th>操作</th>
    <th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'no'); ?></th>
    <th><?php echo $paginator->sort(array('asc'=>'カテゴリ ▼','desc'=>'カテゴリ ▲'),'page_category_id'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'ページ名 ▼','desc'=>'ページ名 ▲'),'name'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'タイトル ▼','desc'=>'タイトル ▲'),'title'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'公開状態 ▼','desc'=>'公開状態 ▲'),'description'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'登録日 ▼','desc'=>'登録日 ▲'),'created'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
</tr>
<?php if(!empty($dbDatas)): ?>
<?php $count=0; ?>
<?php foreach($dbDatas as $dbData): ?>
	<?php if (!$dbData['Page']['status']): ?>
		<?php $class=' class="disablerow"'; ?>
	<?php elseif ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
            <?php $baser->link('確認',array('action'=>'preview', $dbData['Page']['id']),array('class'=>'btn-green-s button-s','target'=>'_blank'),null,false) ?>
			<?php $baser->link('編集',array('action'=>'edit', $dbData['Page']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $dbData['Page']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $dbData['Page']['name']),false); ?>
		</td>
        <td><?php echo $dbData['Page']['no']; ?></td>
        <td>
            <?php if(!empty($dbData['PageCategory']['title'])): ?>
                <?php echo $dbData['PageCategory']['title']; ?>
            <?php endif; ?>
		</td>
		<td><?php $baser->link($dbData['Page']['name'],array('action'=>'edit', $dbData['Page']['id'])); ?></td>
		<td><?php echo $dbData['Page']['title']; ?></td>
		<td style="text-align:center"><?php echo $textEx->booleanMark($dbData['Page']['status']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$dbData['Page']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$dbData['Page']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<?php $baser->pagination('default',array(),null,false) ?>

<div class="align-center"><?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>