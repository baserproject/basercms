<?php

/**
 * PageFixture
 */
class PageFixture extends BaserTestFixture
{

	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'Page';

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => 1,
			'contents' => '<section class="mainHeadline">
<h2>シングルページデザインで<br />
<span class="fcGreen">見やすくカッコいい</span>Webサイトへ！</h2>
</section>
<!-- /mainHeadline -->

<div class="mainWidth" id="information">
<section class="news1">
<h2>NEWS RELEASE</h2>
<?php $this->BcBaser->blogPosts(\'news\', 5) ?>
</section>

<section class="news2">
<h2>BaserCMS NEWS</h2>
<?php echo $this->BcBaser->js(\'/feed/ajax/1\'); ?>
</section>
</div><!-- /information -->',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 2,
			'contents' => '<div class="articleArea" id="company">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down">Company Profile <span>会社案内</span></h2>

<section class="companyArea">
<h3><span>会社概要</span></h3>

<div class="profileArea">
<dl>
	<dt>会社名</dt>
	<dd>baserCMS inc. [デモ]</dd>
	<dt>設立</dt>
	<dd>2009年11月</dd>
	<dt>所在地</dt>
	<dd>福岡県福岡市博多区博多駅前（ダミー）</dd>
	<dt>電話番号</dt>
	<dd>092-000-55555</dd>
	<dt>FAX</dt>
	<dd>092-000-55555</dd>
	<dt>事業内容</dt>
	<dd>インターネットサービス業（ダミー）<br />
	Webサイト制作事業（ダミー）<br />
	WEBシステム開発事業（ダミー）</dd>
</dl>
</div>
</section>

<section class="companyArea access">
<h3><span>交通<br />
アクセス</span></h3>

<div class="profileArea">
<p>JR○○駅から徒歩6分<br />
西鉄バス「○○」停のすぐ目の前</p>
</div>
</section>
</article>
<?php $this->BcBaser->googleMaps(array("width" => "100%","height" => 500)) ?></div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 3,
			'contents' => '<div class="articleArea bgGray" id="service">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down">Service <span>事業案内</span></h2>

<div class="commentArea">
<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>

<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>
</div>

<figure class="imgArea"><?php $this->BcBaser->img(\'img_service.jpg\',array(\'alt\'=>\'事業内容の写真\')) ?></figure>
</article>
</div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 4,
			'contents' => '<div class="articleArea" id="recruit">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down">Recruit <span>採用情報</span></h2>
<p>baserCMS inc. [デモ]では現在、下記の職種を募集しています。皆様のご応募をお待ちしております。</p>
<table class="defaultTable">
<tbody>
<tr>
<th>職種</th>
<td>営業</td>
</tr>
<tr>
<th>資格</th>
<td>経験者</td>
</tr>
<tr>
<th>事業内容</th>
<td>インターネットサービス業（ダミー）、Webサイト制作事業（ダミー）、WEBシステム開発事業（ダミー）</td>
</tr>
<tr>
<th>給与</th>
<td>180,000円〜300,000円（※経験等考慮の上、優遇します）</td>
</tr>
<tr>
<th>待遇</th>
<td>昇給年１回・賞与年2回、各種保険完備、交通費支給、退職金、厚生年金制度有り、車通勤可</td>
</tr>
<tr>
<th>休日</th>
<td>日曜日、祝日、月2回土曜日</td>
</tr>
<tr>
<th>休暇</th>
<td>夏季、年末年始、慶弔、有給</td>
</tr>
<tr>
<th>時間</th>
<td>9：00 〜 18：00</td>
</tr>
<tr>
<th>応募</th>
<td>随時受付中。電話連絡後（TEL：092-000-55555　採用担当：山田）、履歴書（写真貼付）をご持参ください</td>
</tr>
</tbody>
</table>
</article>
</div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 5,
			'contents' => '<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">メインメニュー</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<span style="color:#8ABE08">■</span>
<?php $this->BcBaser->link("ニュースリリース",array("controller"=>"news","action"=>"index")) ?>
<br />
<span style="color:#8ABE08">■</span>
<?php $this->BcBaser->link("お問い合わせ",array("controller"=>"contact","action"=>"index")) ?>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">NEWS RELEASE</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<?php $this->BcBaser->blogPosts(\'news\', 5) ?> <div>&nbsp;</div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">baserCMS NEWS</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<?php $this->BcBaser->feed(1) ?>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 6,
			'contents' => '<div id="news" class="clearfix">
<div class="news" style="margin-right:28px;">
<h2 id="newsHead01">NEWS RELEASE</h2>
<div class="body">
<?php $this->BcBaser->blogPosts(\'news\', 5) ?>
</div>
</div>
<div class="news">
<h2 id="newsHead02">baserCMS NEWS</h2>
<div class="body">
<?php $this->BcBaser->js(\'/s/feed/ajax/1\') ?>
</div>
</div>
</div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 7,
			'contents' => '<h2 class="contents-head">会社案内</h2>
<h3 class="contents-head">会社データ</h3>
<div class="section">
<table class="row-table-01" cellspacing="0" cellpadding="0">
<tr><th width="150">会社名</th><td>baserCMS inc.  [デモ]</td></tr>
<tr><th>設立</th><td>2009年11月</td></tr>
<tr><th>所在地</th><td>福岡県福岡市博多区博多駅前（ダミー）</td></tr>
<tr><th>事業内容</th><td>インターネットサービス業（ダミー）<br />
Webサイト制作事業（ダミー）<br />
WEBシステム開発事業（ダミー）</td></tr>
</table>
</div>
<h3 class="contents-head">アクセスマップ</h3>
<div class="section">
<?php $this->BcBaser->googleMaps(array("width" => 585)) ?>
</div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 8,
			'contents' => '<h2 class="contents-head">サービス</h2>
<div class="section">
<p>
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
</p>
</div>
<div class="section">
<p>
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
</p>
</div>
<div class="section">
<p>
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。
</p>
</div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 9,
			'contents' => '<h2 class="contents-head">サイトマップ</h2>
<?php $this->BcBaser->sitemap() ?>
<ul class="section sitemap">
	<li><?php $this->BcBaser->link("新着情報","/s/news/index") ?></li>
	<li><?php $this->BcBaser->link("お問い合わせ","/s/contact/index") ?>	</li>
</ul>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 10,
			'contents' => '<h2>
	アイコンの使い方</h2>
<h3>
	50種類のアイコンを自由にカスタマイズしよう。</h3>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	まずは、<a href="https://basercms.net/files/extra/nada-works-png.zip">nada-works-png.zip</a> をダウンロードして解凍します。</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	icons_ico_.pngをFireworksで開くと下記の50種類のアイコンがレイヤー分けされています。</p>
<p>
	&nbsp;</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_001.png\', array(\'style\' => \'width: 656px; height: 250px;\')) ?></p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	カスタマイズ1：ベースの形を変える。</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_002.png\', array(\'style\' => \'width: 656px; height: 93px;\')) ?></p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	カスタマイズ2：色を変える。</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_003.png\', array(\'style\' => \'width: 656px; height: 93px;\')) ?></p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	カスタマイズ3：パスを使って変形させる。（上級者向け）<br />
	パスで作成しています。自由に変形させることが可能です。</p>
<p>
	&nbsp;</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_004.png\', array(\'style\' => \'width: 193px; height: 186px;\')) ?></p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	パターン例：各コンテンツで色を変える。同じアイコンを使用する、など</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_005.png\', array(\'style\' => \'width: 656px; height: 215px;\')) ?></p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<h3>
	文字と写真を変えるだけで完成！かんたんバナーを作ろう。</h3>
<p>
	&nbsp;</p>
<p>
	&nbsp;</p>
<p>
	icons_banner_00.png、icons_banner_l_00.pngをFireworksで開くと各要素をレイヤー分けされています。<br />
	言葉、フォント、色、画像を変更してオリジナルのバナーを作成することができます。<br />
	画像は「シンボル」にて配置しています。差し替えたい画像をシンボル化し、「シンボルを入れ替え」にて差し替えてご使用ください。</p>
<p>
	&nbsp;</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_006.png\', array(\'style\' => \'width: 656px; height: 302px;\')) ?></p>
<p>
	&nbsp;</p>
<p>
	例：言葉、フォントの変更、画像差し替え</p>
<p>
	&nbsp;</p>
<p style="text-align: center;">
	<?php $this->BcBaser->img(\'icons/about_007.png\', array(\'style\' => \'width: 656px; height: 278px;\')) ?></p>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => 11,
			'contents' => '<h2 class="contents-head">会社案内</h2>
<h3 class="contents-head">会社データ</h3>
<div class="section">
<table class="row-table-01" cellspacing="0" cellpadding="0">
<tr><th width="150">会社名</th><td>baserCMS inc.  [デモ]</td></tr>
<tr><th>設立</th><td>2009年11月</td></tr>
<tr><th>所在地</th><td>福岡県福岡市博多区博多駅前（ダミー）</td></tr>
<tr><th>事業内容</th><td>インターネットサービス業（ダミー）<br />
Webサイト制作事業（ダミー）<br />
WEBシステム開発事業（ダミー）</td></tr>
</table>
</div>
<h3 class="contents-head">アクセスマップ</h3>
<div class="section">
<?php $this->BcBaser->googleMaps(array("width" => 585)) ?>
</div>',
			'draft' => '',
			'code' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
	];
}
