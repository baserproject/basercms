<div class="bge-split-container">
	<div class="bge-multi-field-selection">
		<table data-bge-class="MultiFieldSelection">
			<caption>スライドショーに使用する画像</caption>
			<tbody data-bge-list>
				<tr>
					<th class="bge-multi-field-selection__thumb">
						<div class="bge-multi-field-selection__thumb-clip"><img alt=" " data-bge="path:src"></div>
						<input type="hidden" name="bge-path">
					</th>
					<td class="bge-multi-field-selection__caption">
						<input type="text" name="bge-caption" placeholder="キャプション">
					</td>
					<td class="bge-multi-field-selection__ctrl">
						<button data-bge-event="click:removeRow">削除</button>
						<button data-bge-event="click:replaceRow">下と入替</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="bge-multi-field-selector">
		<button data-bge-class="MultiFieldSelector">選択した画像を使用する</button>
	</div>

	<div class="bge-image">
		<div class="bge-file-uploader">
			<div class="bge-file-uploader__error" data-bge-class="FileUploadMessenger"></div>
			<input type="file" name="bge-image-file" data-bge-class="ImageUploader" placeholder="ここにファイルをドラッグアンドドロップ">
			<input type="hidden" name="bge-empty">
		</div>

		<div class="bge-input">
			<div>
				<fieldset>
					<legend>エフェクト</legend>
					<label><input type="radio" name="bge-effect" value="fade">フェードイン・アウト</label>
					<label><input type="radio" name="bge-effect" value="slide">スライド</label>
				</fieldset>
				<label>静止時間: <input type="text" name="bge-delay">ミリ秒</label><br>
				<label>切り替わり時間: <input type="text" name="bge-duration">ミリ秒</label><br>
				<label><input type="checkbox" name="bge-autoplay">自動再生</label><br>
				<label><input type="checkbox" name="bge-ctrl">コントローラー</label><br>
				<fieldset>
					<legend>マーカー</legend>
					<label><input type="radio" name="bge-marker" value="dot">ドット</label>
					<label><input type="radio" name="bge-marker" value="thumbs">サムネイル</label>
					<label><input type="radio" name="bge-marker" value="none">なし</label>
				</fieldset>
			</div>
			<hr>
			<div class="float-left">
				<p>
					<input type="text" placeholder="絞込検索" data-bge-class="UploadFileSearchForm">
					<input type="button" value="検索" data-bge-class="UploadFileSearchButton">
				</p>
			</div>
			<div class="float-right">
				<input type="button" value="選択画像を削除する" data-bge-class="UploadImageDeleter">
			</div>
		</div>

		<div class="image-select-area" data-bge-class="UploadImageListMultiSelect"></div>
	</div>
</div>
