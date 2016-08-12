<!-- BaserPageTagBegin -->
<?php $this->BcBaser->setTitle('会社案内') ?>
<?php $this->BcBaser->setDescription('baserCMS inc.の会社案内ページ') ?>
<?php $this->BcBaser->setPageEditLink(2) ?>
<!-- BaserPageTagEnd -->

<h2>会社案内</h2>

<h3>会社データ</h3>

<table>
	<tbody>
		<tr>
			<th>会社名</th>
			<td>コンテストサンプル</td>
		</tr>
		<tr>
			<th>設立</th>
			<td>2015年6月</td>
		</tr>
		<tr>
			<th>所在地</th>
			<td>住所が入ります。住所が入ります。住所が入ります。</td>
		</tr>
		<tr>
			<th>事業内容</th>
			<td>事業内容１が入ります。<br />
			事業内容２が入ります。<br />
			事業内容３が入ります。</td>
		</tr>
	</tbody>
</table>

<h3>アクセスマップ</h3>
<?php $this->BcBaser->googleMaps(array("width" => 585)) ?>