<div class="bge-file-uploader">
	<div class="bge-file-uploader__error" data-bge-class="FileUploadMessenger"></div>
	<input type="file" name="bge-image-file" data-bge-class="ImageUploader">
	<input type="hidden" name="bge-path">
	<input type="hidden" name="bge-empty">
</div>

<div class="bge-input">
	<div class="float-left">
		<p>
			<input type="text" name="bge-caption" placeholder="キャプション">
			<label><input type="checkbox" name="bge-popup" data-bge-value="1">ポップアップ表示</label>
		</p>
		<p>
			<input type="text" name="bge-alt" placeholder="画像の説明(alt)">
		</p>
		<p>
			<input type="text" placeholder="絞込検索" data-bge-class="UploadFileSearchForm">
			<input type="button" value="検索" data-bge-class="UploadFileSearchButton">
		</p>
	</div>
	<div class="float-right">
		<input type="button" value="選択画像を削除する" data-bge-class="UploadImageDeleter">
	</div>
</div>

<div class="image-select-area" data-bge-class="UploadImageList"></div>
