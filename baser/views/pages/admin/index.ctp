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
$pageCategories = $formEx->getControlSource('Page.page_category_id');
if($pageCategories){
	$pageCategories = array('noncat'=>'カテゴリなし','pconly'=>'PCページのみ')+$pageCategories;
}
?>
<?php $baser->js('sorttable',false) ?>
<script type="text/javascript">
$(function(){
	$("#PageFilterBody").show();
});
</script>
<?php echo $formEx->create('Sort',array('action'=>'update_sort','url'=>am(array('controller'=>'pages'),$this->passedArgs))) ?>
	<?php echo $formEx->hidden('Sort.id') ?>
	<?php echo $formEx->hidden('Sort.offset') ?>
<?php echo $formEx->end() ?>

<div id="pageMessage" class="message" style="display:none"></div>

<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?>
	<?php $baser->img('ajax-loader-s.gif',array('id'=>'ListAjaxLoader')) ?>
</h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ページ管理では、ブログやメールフォーム以外のWEBページを管理する事ができます。</p>
	<ul>
		<li>新しいページを登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>公開状態を設定する事ができ、一覧各ページ横の「確認」ボタンをクリックすると非公開のページも確認する事ができます。</li>
		<li>各ページは分類分け用の「カテゴリ」に属させる事ができ、階層構造のURLを実現できます。</li>
		<li>管理画面内では、公開状態、カテゴリによりページの検索を行う事ができます。<br />
			検索するには、すぐ下の「検索」をクリックして検索条件を表示させます。</li>
		<li>画面一番下の「並び替えモード」をクリックすると、表示される<?php $baser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして行の並び替えができます。<br />
		<small>※ この並び順はローカルナビゲーション等に反映されます。</small></li>
		<li>オーサリングツールでの制作に慣れている方向けに、ファイルをアップロードしてデータベースに一括で読み込む機能を備えています。<br />
			ページを読み込むには、特定のフォルダにページテンプレートをアップロードして、サイドメニューの「ページテンプレート読込」を実行します。<br />
			<a href="http://basercms.net/manuals/etc/5.html" target="_blank">≫ ページテンプレート読込について</a></li>
	</ul>
	<div class="example-box">
		<div class="head">（例）ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[BaserCMS設置URL]/about</p>
		<div class="head">（例）カテゴリ「company」に属する、ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[BaserCMS設置URL]/company/about</p>
	</div>
</div>
<h3><a href="javascript:void(0);" class="slide-trigger" id="PageFilter">検索</a></h3>
<div class="function-box corner10" id="PageFilterBody" style="display:none"> <?php echo $formEx->create('Page',array('url'=>array('action'=>'index'))) ?>
	<p>
		<?php if($pageCategories): ?>
		<small>カテゴリ</small> <?php echo $formEx->select('Page.page_category_id', $pageCategories, null,array('escape'=>false)) ?>　
		<?php endif ?>
		<small>公開状態</small> <?php echo $formEx->select('Page.status', $textEx->booleanMarkList()) ?>　 
	<?php echo $formEx->submit('検　索',array('div'=>false,'class'=>'btn-orange button')) ?> </p>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>
<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="TablePages">
	<tr>
		<th>操作</th>
		<?php if(!$sortmode): ?>
		<th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'id'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'カテゴリ ▼','desc'=>'カテゴリ ▲'),'page_category_id'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'ページ名 ▼','desc'=>'ページ名 ▲'),'name'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'タイトル ▼','desc'=>'タイトル ▲'),'title'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'公開状態 ▼','desc'=>'公開状態 ▲'),'description'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'登録日 ▼','desc'=>'登録日 ▲'),'created'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
		<?php else: ?>
		<th>NO</th>
		<th>カテゴリ</th>
		<th>ページ名</th>
		<th>タイトル</th>
		<th>公開状態</th>
		<th>登録日</th>
		<th>更新日</th>
		<?php endif ?>
	</tr>
	<?php if(!empty($dbDatas)): ?>
		<?php $count=0; ?>
		<?php foreach($dbDatas as $dbData): ?>
			<?php if (!$dbData['Page']['status']): ?>
				<?php $class=' class="disablerow sortable"'; ?>
			<?php elseif ($count%2 === 0): ?>
				<?php $class=' class="altrow sortable"'; ?>
			<?php else: ?>
				<?php $class=' class="sortable"'; ?>
			<?php endif; ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td class="operation-button" style="width:25%">
			<?php if($sortmode): ?>
			<span class="sort-handle"><?php $baser->img('sort.png',array('alt'=>'並び替え')) ?></span>
			<?php echo $formEx->hidden('Sort.id'.$dbData['Page']['id'],array('class'=>'id','value'=>$dbData['Page']['id'])) ?>
			<?php endif ?>
			<?php $url = preg_replace('/index$/', '', $dbData['Page']['url']) ?>
			<?php if(!preg_match('/^\/mobile\//is', $url)): ?>
			<?php $baser->link('確認',$url,array('class'=>'btn-green-s button-s','target'=>'_blank'),null,false) ?>
			<?php else: ?>
			<?php $baser->link('確認',preg_replace('/^\/mobile\//is', '/m/', $url), array('class'=>'btn-green-s button-s','target'=>'_blank'),null,false) ?>
			<?php endif ?>
			<?php $baser->link('編集',array('action'=>'edit', $dbData['Page']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $dbData['Page']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $dbData['Page']['name']),false); ?>
		</td>
		<td class="col-id" style="width:10%"><?php echo $dbData['Page']['id']; ?></td>
		<td style="width:15%"><?php if(!empty($dbData['PageCategory']['title'])): ?>
			<?php echo $dbData['PageCategory']['title']; ?>
			<?php endif; ?></td>
		<td style="width:10%"><?php $baser->link($dbData['Page']['name'],array('action'=>'edit', $dbData['Page']['id'])); ?></td>
		<td style="width:10%"><?php echo $dbData['Page']['title']; ?></td>
		<td style="width:10%;text-align:center"><?php echo $textEx->booleanMark($dbData['Page']['status']); ?></td>
		<td style="width:10%"><?php echo $timeEx->format('y-m-d',$dbData['Page']['created']); ?></td>
		<td style="width:10%"><?php echo $timeEx->format('y-m-d',$dbData['Page']['modified']); ?></td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
<?php $baser->pagination('default',array(),null,false) ?>
<div class="align-center">
	<?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?>
	<?php if(!$sortmode): ?>
	<?php $baser->link('並び替えモード',array('sortmode'=>1),array('class'=>'btn-orange button')) ?>
	<?php else: ?>
	<?php $baser->link('ノーマルモード',array('sortmode'=>0),array('class'=>'btn-orange button')) ?>
	<?php endif ?>
</div>
