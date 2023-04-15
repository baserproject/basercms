<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * インストーラー Step2
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $configDir
 * @var string $pluginDir
 * @var string $filesDir
 * @var string $tmpDir
 * @var string $dbDir
 * @var string $requirePhpVersion
 * @var string $requirePhpMemory
 * @var string $encoding
 * @var string $phpMemory
 * @var string $phpActualVersion
 * @var bool $configDirWritable
 * @var bool $pluginDirWritable
 * @var bool $tmpDirWritable
 * @var bool $filesDirWritable
 * @var bool $dbDirWritable
 * @var bool $encodingOk
 * @var bool $phpMemoryOk
 * @var bool $phpVersionOk
 * @var bool $gdOk
 * @var bool $xmlOk
 * @var bool $zipOk
 * @var bool $blRequirementsMet
 */
$this->BcBaser->js('BcInstaller.admin/installations/step2.bundle', false);
$this->BcAdmin->setTitle(__d('baser_core', 'baserCMSのインストール｜ステップ２'));
?>


<div class="step2">

  <div class="em-box bca-em-box">
    <?php echo __d('baser_core', 'インストール環境の条件をチェックしました。<br />次に進む為には、「基本必須条件」の赤い項目を全て解決する必要があります。') ?>
  </div>

  <div class="section bca-section">

    <!-- basic -->
    <h2 class="bca-main__heading"><?php echo __d('baser_core', '基本必須条件') ?></h2>
    <div class="panel-box bca-panel-box corner10">
      <ul class="section">
        <li class='<?php if ($phpVersionOk) echo 'check'; else echo 'failed'; ?>'>
          <?php echo __d('baser_core', 'PHPのバージョン') ?> >= <?php echo $requirePhpVersion; ?>
          <div class="check-result"><?php echo __d('baser_core', '現在のPHPバージョン') ?>
            ： <?php echo $phpActualVersion; ?>
            <?php if (!$phpVersionOk): ?>
              <br>
              <small><?php echo __d('baser_core', 'ご利用のサーバーでは残念ながらbaserCMSを動作させる事はできません') ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($configDirWritable) echo 'check'; else echo 'failed'; ?>'>
          <?php echo __d('baser_core', '{0} フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）', $configDir) ?>
          <div class="check-result">
            <?php if ($configDirWritable): ?>
              <?php echo __d('baser_core', '書き込み可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '書き込み不可') ?><br>
              <small><?php echo __d('baser_core', '{0} フォルダに書き込み権限が必要です', $configDir) ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($pluginDirWritable) echo 'check'; else echo 'failed'; ?>'>
          <?php echo __d('baser_core', '{0} フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）', $pluginDir) ?>
          <div class="check-result">
            <?php if ($pluginDirWritable): ?>
              <?php echo __d('baser_core', '書き込み可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '書き込み不可') ?><br>
              <small><?php echo __d('baser_core', '{0} フォルダに書き込み権限が必要です', $pluginDir) ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($tmpDirWritable) echo 'check'; else echo 'failed'; ?>'>
          <?php echo __d('baser_core', '{0} フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）', $tmpDir) ?>
          <div class="check-result">
            <?php if ($tmpDirWritable): ?>
              <?php echo __d('baser_core', '書き込み可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '書き込み不可') ?><br>
              <small><?php echo __d('baser_core', '{0} フォルダに書き込み権限が必要です。', $tmpDir) ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($filesDirWritable) echo 'check'; else echo 'failed'; ?>'>
          <?php echo __d('baser_core', '{0} フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）', $filesDir) ?>
          <div class="check-result">
            <?php if ($filesDirWritable): ?>
              <?php echo __d('baser_core', '書き込み可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '書き込み不可') ?><br>
              <small><?php echo __d('baser_core', '{0} フォルダに書き込み権限が必要です', $filesDir) ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($encodingOk) echo 'check';
        else echo 'failed'; ?>'>
          <?php echo __d('baser_core', '文字エンコーディングの設定 = UTF-8') ?>
          <div class="check-result">
            <?php echo $encoding ?><br>
            <?php if (!$encodingOk): ?>
              <small><?php echo __d('baser_core', 'phpの内部文字エンコーディングがUTF-8である必要があります') ?></small>
              <br>
              <small><?php echo __d('baser_core', 'php.iniで「mbstring.internal_encoding」をUTF-8に設定してください') ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($gdOk) echo 'check';
        else echo 'failed'; ?>'>
          <?php echo __d('baser_core', 'GDの利用') ?>
          <div class="check-result">
            <?php if ($gdOk): ?>
              <?php echo __d('baser_core', '利用可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '利用不可') ?><br>
              <small><?php echo __d('baser_core', 'phpのGDモジュールでPNGが使える必要があります') ?></small>
              <br>
              <small><?php echo __d('baser_core', 'GDモジュールをインストールするか有効にしてください') ?></small>
            <?php endif ?>
          </div>
        </li>
        <li class='<?php if ($xmlOk) echo 'check';
        else echo 'failed'; ?>'>
          <?php echo __d('baser_core', 'DOMDocumentの利用') ?>
          <div class="check-result">
            <?php if ($xmlOk): ?>
              <?php echo __d('baser_core', '利用可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '利用不可') ?><br>
              <small><?php echo __d('baser_core', 'phpのxmlモジュールでDOMDocumentが使える必要があります') ?></small>
              <br>
              <small><?php echo __d('baser_core', 'xmlモジュールをインストールするか有効にしてください') ?></small>
            <?php endif ?>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <div class="section bca-section">
    <!-- option -->
    <h2 class="bca-main__heading"><?php echo __d('baser_core', 'オプション') ?></h2>

    <div class="panel-box bca-panel-box corner10">
      <h3 class="bca-panel-box__title"><?php echo __d('baser_core', 'ファイルデータベース') ?></h3>
      <div class="section"><p
          class="bca-main__text"><?php echo __d('baser_core', 'データベースサーバーが利用できない場合には、ファイルベースデータベースの SQLite を利用できます。<br>有効にするには、下記のフォルダへの書き込み権限が必要です ') ?></p>
      </div>
      <ul class="section">
        <li class='<?php if ($dbDirWritable) echo 'check';
        else echo 'failed'; ?>'>
          <?php echo __d('baser_core', '{0} の書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）', $dbDir) ?><br>
          <div class="check-result">
            <?php if ($dbDirWritable): ?>
              <?php echo __d('baser_core', '書き込み可') ?>
            <?php else: ?>
              <?php echo __d('baser_core', '書き込み不可') ?><br>
              <small><?php echo __d('baser_core', 'SQLite を利用するには、{0} フォルダに書き込み権限が必要です', $dbDir) ?></small>
            <?php endif ?>
          </div>
        </li>
      </ul>
    </div>

    <div class="panel-box bca-panel-box corner10">
      <h3 class="bca-panel-box__title"><?php echo __d('baser_core', 'PHPのメモリ') ?></h3>
      <div class="section"><p
          class="bca-main__text"><?php echo sprintf(__d('baser_core', 'PHPのメモリが %s より低い場合、baserCMSの全ての機能が正常に動作しない可能性があります。'), $requirePhpMemory . " MB") ?>
          <br>
          <small><?php echo __d('baser_core', 'サーバー環境によってはPHPのメモリ上限が取得できず「0MB」となっている場合もあります。その場合、サーバー業者等へサーバースペックを直接確認してください。') ?></small>
        </p></div>
      <ul class="section">
        <li class='<?php if ($phpMemoryOk) echo 'check';
        else echo 'failed'; ?>'>
          <?php echo sprintf(__d('baser_core', 'PHPのメモリ上限 >= %s'), $requirePhpMemory . " MB") ?>
          <div
            class="check-result"><?php echo sprintf(__d('baser_core', '現在のPHPのメモリ上限： %s'), '&nbsp;' . $phpMemory . " MB") ?>
            <?php if (!$phpMemoryOk): ?>
              <br>
              <small><?php echo sprintf(__d('baser_core', 'php.iniの設定変更が可能であれば、memory_limit の値を%s以上に設定してください'), $requirePhpMemory . " MB") ?></small>
            <?php endif ?>
          </div>
        </li>
      </ul>
    </div>

		<div class="panel-box bca-panel-box corner10">
			<h3 class="bca-panel-box__title"><?php echo __d('baser', '拡張モジュール') ?></h3>
			<ul class="section">
				<li class='<?php echo $zipOk ? 'check' : 'failed'; ?>'>
					<?php echo __d('baser', 'Zip') ?><br/>
					<div class="check-result">
						<?php if ($zipOk): ?>
							<?php echo __d('baser', '利用可') ?>
						<?php else: ?>
							<?php echo __d('baser', '利用不可') ?><br/>
							<small><?php echo __d('baser', 'テーマなどのzipダウンロードが制限されます。') ?></small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>

  </div>

  <?php echo $this->BcAdminForm->create(null, ['url' => ['action' => 'step2'], 'type' => 'post', 'id' => 'CheckEnvForm']) ?>
  <?php echo $this->BcAdminForm->hidden('mode', ['id' => 'mode']) ?>
  <?php $this->BcAdminForm->unlockField('mode') ?>

  <div class="submit bca-actions">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '再チェック'), ['class' => 'bca-btn bca-actions__item', 'id' => 'BtnCheckAgain']) ?>
    <?php if (!$blRequirementsMet): ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '次のステップへ'), [
        'class' => 'bca-btn bca-actions__item',
        'id' => 'BtnNext',
        'disabled' => 'disabled'
      ]) ?>
    <?php else: ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '次のステップへ'), [
        'class' => 'bca-btn bca-actions__item',
        'id' => 'BtnNext',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save'
      ]) ?>
    <?php endif ?>
  </div>
  <?php echo $this->BcAdminForm->end() ?>

</div>


