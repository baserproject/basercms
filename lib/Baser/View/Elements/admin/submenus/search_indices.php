<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] 検索インデックスメニュー
 */
?>


<script>
	$(function(){
		$("#BtnReconstruct").click(function(){
			$.bcConfirm.show({
				title: '確認',
				message: '現在の検索インデックスを消去して、再構築します。本当にいいですか？',
				ok: function(){
					$.bcUtil.showLoader();
					location.href = $("#BtnReconstruct").attr('href');
				}
			});
			return false;
		});
	});
</script>
<tr>
	<th>検索インデックスメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', '検索インデックス再構築'), ['controller' => 'search_indices', 'action' => 'reconstruct'], ['id' => 'BtnReconstruct']) ?></li>
		</ul>
	</td>
</tr>
