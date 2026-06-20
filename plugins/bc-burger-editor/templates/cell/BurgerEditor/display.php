<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 変数
 * @var array $addonDir  アドオンのディレクトリを保持しています
 */

// Addonの読み込み
use BcBurgerEditor\View\Helper\BurgerEditorHelper;

$blockList = array();
foreach($addonDir as $path) {
	$blockPath = $path . 'block' . DS;
	if ($dh = opendir($blockPath)) {
		while(($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..' || !is_dir($blockPath . $file)) continue;
			$blockList[] = $blockPath . $file . DS;
		}
		closedir($dh);
	}
}

?>
<!-- BurgerEditor専用領域 -->

<!-- 初期値 -->
<div id="DefaultBlock" hidden><?php $this->BurgerEditor->defaultBlock($blockList); ?></div>
<!-- 入力エリア -->
<div id="InputArea" hidden><?php $this->BurgerEditor->inputArea(); ?></div>
<!-- 初期化処理エリア -->
<div id="InitArea"><?php $this->BurgerEditor->initArea(); ?></div>

<!-- 編集アイコン -->
<button class="edit-inner">編集</button>

<!-- ダイアログ -->
<div class="bge-dialogs" hidden>

	<div id="ContentsEditArea" class="bge-type-editor-dialog" data-title="編集"></div>
	<div id="PanelArea" data-title="要素を追加"><?php $this->BurgerEditor->panelArea(); ?></div>
	<div id="BgBlockConfigArea" data-title="ブロック設定">
		<?php if (isset(BurgerEditorHelper::$bgeConfig['blockClassOption']['free-setting'])): ?>
		設定: <select name="free-setting">
			<option value="">指定なし</option>
			<?php foreach(BurgerEditorHelper::$bgeConfig['blockClassOption']['free-setting'] as $class => $name): ?>
			<option value="<?php echo h($class); ?>"><?php echo h($name); ?></option>
			<?php endforeach; ?>
		</select>
		<br />
		<?php endif; ?>

		<div data-bge-grid-changer>
			<div>
				<label>
					左右の横幅比率:
					<input type="range" value="6" min="1" max="11" name="bge-grid-ratio">
				</label>
			</div>
			<div>
				<label>
					SP版の横幅比率
					<input type="checkbox" name="bge-sp-grid-ratio-enabled">: 有効にする
					<input type="range" value="6" min="1" max="11" name="bge-sp-grid-ratio" disabled>
				</label>
			</div>
		</div>

		<div data-bge-block-option="margin-bottom">
			<label>
				下余白:
				<select data-bge-block-option-select-box></select>
			</label>
		</div>

		<div>
			<label data-bge-block-option="background-color">
				背景色:
				<select data-bge-block-option-select-box></select>
			</label>
		</div>

		<div>
			<fieldset>
				<legend>枠線:</legend>
				<label data-bge-block-option="border-style">
					<select data-bge-block-option-select-box></select>
				</label>
				<label data-bge-block-option="border-type">
					<select data-bge-block-option-select-box></select>
				</label>
			</fieldset>
		</div>

		<div>
			<label data-bge-block-option-custom-class>独自class設定: <input type="text" data-bge-block-option-input /></label>
		</div>

		<div>
			<label data-bge-block-option-id>ID設定: <code>bge-</code><input type="text" data-bge-block-option-input /></label>
			<p><small>アンカーリンク用のID属性を設定します。重複を防ぐために<code>bge-</code>が自動的に頭に付加されます。</small></p>
		</div>

		<hr>

		<div data-bge-block-option-scheduled-publishing-area hidden>
			<fieldset>
				<legend>公開期間設定:</legend>
				<label>
					<span>開始日時</span>
					<input data-bge-block-option-scheduled-publishing="publish-date" size="12" maxlength="10" type="text">
					<input data-bge-block-option-scheduled-publishing="publish-time" size="8" maxlength="8" type="text" class="ui-timepicker-input">
				</label>
				<span>〜</span>
				<label>
					<span>終了日時</span>
					<input data-bge-block-option-scheduled-publishing="unpublish-date" size="12" maxlength="10" type="text">
					<input data-bge-block-option-scheduled-publishing="unpublish-time" size="8" maxlength="8" type="text" class="ui-timepicker-input">
				</label>
				<small>時間は設定しない場合「AM0:00」扱いになります。</small>
			</fieldset>
		</div>

		<div class="bgb-block-covert" style="display: none;">
			<h2>ブロックの変換</h2>
			<ul class="bgb-block-convert-pattern"></ul>
			<script type="text/template">
				<% _.each(patterns, function (pattern) { %>
					<li>
						<label>
							<input type="radio" name="bgb-block-covert-pattern" value="<%= pattern.name %>">
							<span class="bgb-block-covert-from"><img src="/bc_burger_editor/burger_editor/panel/<%= pattern.from %>.png"></span>
							<span class="bgb-block-covert-to"><img src="/bc_burger_editor/burger_editor/panel/<%= pattern.to %>.png"></span>
						</label>
					</li>
				<% }); %>
			</script>
		</div>
	</div>

</div>

