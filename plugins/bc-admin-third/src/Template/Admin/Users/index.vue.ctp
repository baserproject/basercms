<table cellpadding="0" cellspacing="0" class="list-table bca-table-listup" id="app">
	<thead class="bca-table-listup__thead">
	<tr>
		<th class="bca-table-listup__thead-th">
			<?= __d('baser', 'No')?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?= __d('baser', 'アカウント名') ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?= __d('baser', 'ニックネーム') ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?= __d('baser', 'グループ') ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?= __d('baser', '氏名') ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?= __d('baser', '登録日') ?><br/>
			<?= __d('baser', '更新日') ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
	</tr>
	</thead>
	<tbody>
		<tr v-for="user in results">
			<td class="bca-table-listup__tbody-td">{{ user.id }}</td>
			<td class="bca-table-listup__tbody-td">{{ user.name }}</td>
			<td class="bca-table-listup__tbody-td">{{ user.nickname }}</td>
			<td class="bca-table-listup__tbody-td">{{ user.user_group_id }}</td>
			<td class="bca-table-listup__tbody-td">{{ user.real_name_1 }} {{ user.real_name_2 }}</td>
			<td class="bca-table-listup__tbody-td">{{ user.created }}<br>{{ user.modified }}</td>
			<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
				<a href="" class="bca-btn-icon" data-bca-btn-type="edit" data-bca-btn-size="lg"></a>
				<a href="" class="bca-btn-icon" data-bca-btn-type="delete" data-bca-btn-size="lg"></a>
				<a href="" class="bca-btn-icon" data-bca-btn-type="switch" data-bca-btn-size="lg"></a>
			</td>
		</tr>
	</tbody>
</table>