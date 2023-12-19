<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\PageFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * PagesScenario
 *
 */
class PagesScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        PageFactory::make(
            [
                // NOTE: contentFixtureのトップページ
                'id' => 2,
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
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ]
        )->persist();
        PageFactory::make(
            [
                // NOTE: contentFixtureの会社案内
                'id' => 16,
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
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                // NOTE: contentFixtureのサンプル
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
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                // NOTE: contentFixtureのサービス１
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
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                // NOTE: contentFixtureのサービス2
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
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                // NOTE: contentFixtureのサービス3
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
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                'id' => 19,
                'contents' => 'siteId3の固定ページ',
                'draft' => '',
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                'id' => 20,
                'contents' => 'siteId3の固定ページ2',
                'draft' => '',
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();
        PageFactory::make(
            [
                'id' => 21,
                'contents' => 'siteId3の固定ページ3',
                'draft' => '',
                'modified' => null,
                'created' => '2015-01-27 12:56:52'
            ])->persist();

    }

}
