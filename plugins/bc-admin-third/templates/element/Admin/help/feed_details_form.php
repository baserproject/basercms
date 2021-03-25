<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード情報編集　ヘルプ
 */
?>


<p><?php echo __d('baser', 'フィードの登録を行います。キャッシュ時間やカテゴリによる絞り込みの設定が行えます。') ?></p>
<ul>
	<li><?php echo __d('baser', '他サイトのフィードも登録する事ができます。') ?></li>
	<li><?php echo __d('baser', '他サイトのフィードを読み込む場合は、他サイトのサーバー負荷軽減の為、画面下の「詳細設定」をクリックし、キャッシュ時間を多めに設定しておきましょう。（自サイトの場合でも負荷軽減を行う為には同様です）<br><small>※ キャッシュとは、取得したデータを保持し、指定した時間の間、フィードのリクエストを抑制する仕組みです。指定した時間内は、新しい記事に更新されません</small> ') ?></li>
	<li><?php echo __d('baser', 'フィード内の特定のカテゴリの記事のみを取得するには、画面下の「詳細設定」をクリックし、カテゴリフィルターを登録します。') ?></li>
</ul>
