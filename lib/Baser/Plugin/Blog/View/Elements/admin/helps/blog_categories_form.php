<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログカテゴリ一覧　ヘルプ
 */
?>
<p><?php echo __d('baser', 'ブログ記事をグルーピングする為のカテゴリ登録を行います。<br>カテゴリータイトルはTitleタグとして利用されますので、カテゴリを特定するキーワードを登録しましょう。検索エンジン対策として有用です。<br>また、各カテゴリは親カテゴリを指定する事ができ、細かく分類分けが可能です。') ?></p>
<div class="example-box">
	<div class="head"><?php echo __d('baser', '（例）カテゴリーに属した記事のタイトルタグ出力例') ?></div>
	<p><?php echo __d('baser', 'カテゴリー「ニュースリリース」に属する、ブログ記事「新商品を発表しました」のタイトルタグの出力例は次のようになります。') ?></p>
	<p><?php echo __d('baser', '出力結果') ?>：<?php echo __d('baser', '新商品を発表しました') ?>
		｜<?php echo __d('baser', 'ニュースリリース') ?>｜<?php echo __d('baser', 'サイトタイトル') ?></p>
</div>
