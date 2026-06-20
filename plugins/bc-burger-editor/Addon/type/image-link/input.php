<div class="bge-file-uploader">
	<div class="bge-file-uploader__error" data-bge-class="FileUploadMessenger"></div>
	<input type="file" name="bge-image-file" data-bge-class="ImageUploader" placeholder="ここにファイルをドラッグアンドドロップ">
	<input type="hidden" name="bge-path">
	<input type="hidden" name="bge-empty">
	<input type="hidden" name="bge-imported">
</div>

<div class="bge-input">
	<div class="float-left">
		<p>
			<label>URL: <input type="text" name="bge-link"></label>
			<label><input type="checkbox" name="bge-target" data-bge-value="_blank">新しいウィンドウ(タブ)で開く</label>
		</p>
		<p>
			<input type="text" name="bge-caption" placeholder="キャプション">
			<label><input type="checkbox" name="bge-hr" data-bge-value="1">小さく表示する</label>
		</p>
		<p>
			<input type="text" name="bge-alt" placeholder="画像の説明(alt)">
		</p>
		<p>
			<input type="text" placeholder="絞込検索" data-bge-class="UploadFileSearchForm">
			<input type="button" value="検索" data-bge-class="UploadFileSearchButton">
		</p>
		<p>
			<label><input type="checkbox" name="bge-lazy" data-bge-value="1" checked>遅延読み込みと遅延エンコードを行う</label><br />
			<small>チェックを入れることで表示のパフォーマンスが向上します。</small>
		</p>
	</div>
	<div class="float-right">
		<input type="button" value="選択画像を削除する" data-bge-class="UploadImageDeleter">
	</div>
</div>

<div class="image-select-area" data-bge-class="UploadImageList"></div>
