'use strict';
BgE.registerTypeModule('Table', {
	beforeOpen: (editorDialog, _, data) => {
		if (!data) {
			return;
		}
		const $base = editorDialog.$el.find('[data-bge-table-base]');
		const $baseRow = $base.find('tr');
		const $input = editorDialog.$el.find('[data-bge-table-input]');
		const $inputBody = $input.find('tbody');
		let i = 0;
		for (const key of Object.keys(data)) {
			// "td-"で開始するkeyだけ回す
			if (key.indexOf('td-') === 0) {
				const thKey = `th-${i}`;
				const tdKey = `td-${i}`;
				const thValue = BgE.Util.br2nl(`${data[thKey]}`);
				const tdValue = BgE.Util.br2nl(`${data[tdKey]}`);
				data[thKey] = thValue;
				data[tdKey] = tdValue;
				const $cloneRow = $baseRow.clone();
				$cloneRow.find('[name="th"]').attr('name', `bge-th-${i}`);
				$cloneRow.find('[name="td"]').attr('name', `bge-td-${i}`);
				$cloneRow.appendTo($inputBody);
				i += 1;
			}
		}
	},
	// 完了前イベント
	beforeChange: (data, type) => {
		// 書き込み先を準備
		let rowNum = 0;
		for (const key of Object.keys(data)) {
			// "td-"で開始するkeyだけ回す
			if (key.indexOf('td-') === 0) {
				rowNum += 1;
			}
		}
		const $targetTbl = $(type.el).find('tbody');
		let rows = '';
		for (let i = 0; i < rowNum; i++) {
			rows += `
				<tr>
					<th class="bge-type-table__heading" data-bge="th-${i}"></th>
					<td class="bge-type-table__text" data-bge="td-${i}"></td>
				</tr>
			`;
		}
		$targetTbl.html(rows);
	},
	// 入力・選択終了時イベント
	change: (_, type) => {
		$(type.el)
			.find('[data-bge]')
			.each((_, el) => {
				$(el).html((_, html) => {
					return BgE.Util.nl2br(html);
				});
			});
	},
	migrateElement(values, type) {
		let _a, _b;
		const $el = $(type.el);
		const $tbody = $el.find('tbody');
		const $rowOrigin = $tbody.find('tr').clone();
		const keys = Object.keys(values);
		$tbody.empty();
		for (const key of keys) {
			if (!/^th-\d+$/.test(key)) {
				continue;
			}
			const n = Number.parseInt(
				(_b = (_a = key.match(/\d+$/)) === null || _a === void 0 ? void 0 : _a[0]) !==
					null && _b !== void 0
					? _b
					: '',
				10,
			);
			if (Number.isNaN(n)) {
				continue;
			}
			const thVal = values[`th-${n}`];
			if (thVal == null || Array.isArray(thVal) || $.isFunction(thVal)) {
				continue;
			}
			const tdVal = values[`td-${n}`];
			if (tdVal == null || Array.isArray(tdVal) || $.isFunction(tdVal)) {
				continue;
			}
			values[`th-${n}`] = BgE.Util.nl2br(`${thVal}`);
			values[`td-${n}`] = BgE.Util.nl2br(`${tdVal}`);
			const $row = $rowOrigin.clone();
			$row
				.find('[data-bge^="th-"]')
				.attr('data-bge', `th-${n}`)
				.html(values[`th-${n}`] + '');
			$row
				.find('[data-bge^="td-"]')
				.attr('data-bge', `td-${n}`)
				.html(values[`td-${n}`] + '');
			$tbody.append($row);
		}
	},
	customFunctions: {
		// 追加ボタンクリック時イベント
		addRow: (e, editorDialog, type, module) => {
			const $this = $(e.target);
			const $baseTbl = editorDialog.$el.find('[data-bge-table-base]');
			const nextRowNum = editorDialog.$el.find('[data-bge-table-input] tr').length;
			$baseTbl.find('th [data-bge-title]').attr('name', 'bge-th-' + nextRowNum);
			$baseTbl.find('td [data-bge-text]').attr('name', 'bge-td-' + nextRowNum);
			$this.parents('tr').after($baseTbl.find('tbody').html());
			// bind対象外の名称にする
			$baseTbl.find('th [data-bge-title]').attr('name', 'th');
			$baseTbl.find('td [data-bge-text]').attr('name', 'td');
			module.fire('refreshRow', editorDialog, type, module);
			return false;
		},
		// 削除ボタンクリック時イベント
		removeRow: (e, editorDialog, type, module) => {
			if (confirm('削除します。よろしいですか？')) {
				if (editorDialog.$el.find('[data-bge-table-input] tr').length === 1) {
					alert('全て削除する場合はブロック要素を削除してください');
					return false;
				}
				$(e.target)
					.parents('tr')
					.fadeOut(200, function () {
						$(this).remove();
						module.fire('refreshRow', editorDialog, type, module);
					});
			}
			return false;
		},
		// 移動ボタンクリック時イベント
		replaceRow: (e) => {
			let _a;
			const $thisTr = $(e.target).parents('tr');
			const $nextTr = $thisTr.next();
			if (
				$nextTr.length > 0 &&
				((_a = $thisTr.next().get(0)) === null || _a === void 0
					? void 0
					: _a.nodeName.toLowerCase()) === 'tr'
			) {
				const thVal = $thisTr.find('th [data-bge-title]').val();
				const tdVal = $thisTr.find('td [data-bge-text]').val();
				$thisTr
					.find('th [data-bge-title]')
					.val($nextTr.find('th [data-bge-title]').val());
				$thisTr.find('td [data-bge-text]').val($nextTr.find('td [data-bge-text]').val());
				$nextTr.find('th [data-bge-title]').val(thVal);
				$nextTr.find('td [data-bge-text]').val(tdVal);
			} else {
				alert('対象が見つかりません');
			}
			return false;
		},
		// 入力エリアのnameを再構築する
		refreshRow: (_, editorDialog) => {
			const $targetTable = editorDialog.$el.find('[data-bge-table-input]');
			$targetTable.find('tr').each((i, el) => {
				$(el).find('th [data-bge-title]').attr('name', `bge-th-${i}`);
				$(el).find('td [data-bge-text]').attr('name', `bge-td-${i}`);
			});
		},
	},
});
