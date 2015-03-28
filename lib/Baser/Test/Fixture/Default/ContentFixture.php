<?php
/**
 * ContentFixture
 *
 */
class ContentFixture extends BaserTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'type' => 'ページ',
			'model' => 'Page',
			'model_id' => '2',
			'category' => '',
			'title' => '会社案内',
			'detail' => 'baserCMS inc.の会社案内ページ Company Profile 会社案内会社概要会社名baserCMS inc. [デモ]設立2009年11月所在地福岡県福岡市博多区博多駅前（ダミー）電話番号092-000-55555FAX092-000-55555事業内容インターネットサービス業（ダミー）WEBサイト制作事業（ダミー）WEBシステム開発事業（ダミー）交通アクセスJR○○駅から徒歩6分西鉄バス「○○」停のすぐ目の前※javascriptを有効にしてください。var latlng = new google.maps.LatLng(33.6065756,130.4182970);var options = {zoom: 16,center: latlng,mapTypeId: google.maps.MapTypeId.ROADMAP,navigationControl: true,mapTypeControl: true,scaleControl: true,scrollwheel: false,};var map = new google.maps.Map(document.getElementById("map"), options);var marker = new google.maps.Marker({position: latlng,map: map,title:"baserCMS inc. [デモ]"});var infowindow = new google.maps.InfoWindow({content: \'baserCMS inc. [デモ]福岡県\'});infowindow.open(map,marker);google.maps.event.addListener(marker, \'click\', function() {infowindow.open(map,marker);});',
			'url' => '/company',
			'status' => 1,
			'priority' => '0.5',
			'created' => '2015-01-27 12:56:52',
			'modified' => '2015-01-27 12:58:24'
		),
		array(
			'id' => '2',
			'type' => 'ページ',
			'model' => 'Page',
			'model_id' => '3',
			'category' => '',
			'title' => '事業案内',
			'detail' => 'baserCMS inc.の事業案内ページ。 Service 事業案内サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。',
			'url' => '/service',
			'status' => 1,
			'priority' => '0.5',
			'created' => '2015-01-27 12:56:52',
			'modified' => '2015-01-27 12:58:25'
		),
		array(
			'id' => '3',
			'type' => 'ページ',
			'model' => 'Page',
			'model_id' => '4',
			'category' => '',
			'title' => '採用情報',
			'detail' => 'baserCMS inc.の採用情報ページ Recruit 採用情報baserCMS inc. [デモ]では現在、下記の職種を募集しています。皆様のご応募をお待ちしております。職種営業資格経験者事業内容インターネットサービス業（ダミー）、WEBサイト制作事業（ダミー）、WEBシステム開発事業（ダミー）給与180,000円〜300,000円（※経験等考慮の上、優遇します）待遇昇給年１回・賞与年2回、各種保険完備、交通費支給、退職金、厚生年金制度有り、車通勤可休日日曜日、祝日、月2回土曜日休暇夏季、年末年始、慶弔、有給時間9：00 〜 18：00応募随時受付中。電話連絡後（TEL：092-000-55555　採用担当：山田）、履歴書（写真貼付）をご持参ください',
			'url' => '/recruit',
			'status' => 1,
			'priority' => '0.5',
			'created' => '2015-01-27 12:56:52',
			'modified' => '2015-01-27 12:58:25'
		),
		array(
			'id' => '4',
			'type' => 'ブログ',
			'model' => 'BlogContent',
			'model_id' => '1',
			'category' => '',
			'title' => 'ニュースリリース',
			'detail' => 'Baser CMS inc. [デモ] の最新のニュースリリースをお届けします。',
			'url' => '/news/index',
			'status' => 1,
			'priority' => '0.5',
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '5',
			'type' => 'メール',
			'model' => 'MailContent',
			'model_id' => '1',
			'category' => '',
			'title' => 'お問い合わせ',
			'detail' => '* 印の項目は必須となりますので、必ず入力してください。',
			'url' => '/contact/index',
			'status' => 1,
			'priority' => '0.5',
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '6',
			'type' => 'ページ',
			'model' => 'Page',
			'model_id' => '1',
			'category' => '',
			'title' => 'Index',
			'detail' => 'シングルページデザインで見やすくカッコいいWEBサイトへ！NEWS RELEASE2015.01.27ホームページをオープンしました2015.01.27新商品を販売を開始しました。BaserCMS NEWS',
			'url' => '/index',
			'status' => 1,
			'priority' => '0.5',
			'created' => '2015-01-27 12:56:52',
			'modified' => '2015-01-27 12:58:23'
		),
	);

}
