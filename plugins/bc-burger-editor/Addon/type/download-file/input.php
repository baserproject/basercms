<div class="bge-file-uploader">
	<div class="bge-file-uploader__error" data-bge-class="FileUploadMessenger"></div>
	<input type="file" name="bge-image-file" data-bge-class="FileUploader">
	<input type="hidden" name="bge-path">
	<input type="hidden" name="bge-formated-size" value="0">
	<input type="hidden" name="bge-size" value="0">
	<input type="hidden" name="bge-imported">
</div>

<div class="bge-input">
	<div class="float-left">
		<p>
			<label>表示ファイル名<br><input type="text" name="bge-name" placeholder="サンプルダウンロードファイル"></label><br>
			<label><input type="checkbox" name="bge-download" value="bge:checked" checked>ブラウザで開かずに直接ダウンロードさせる</label>
		</p>
		<p>
			<input type="text" placeholder="絞込検索" data-bge-class="UploadFileSearchForm">
			<input type="button" value="検索" data-bge-class="UploadFileSearchButton">
		</p>
	</div>
	<div class="float-right">
		<input type="button" value="選択ファイルを削除する" data-bge-class="UploadFileDeleter">
	</div>
</div>

<div class="file-select-area" data-bge-class="UploadFileList"></div>

