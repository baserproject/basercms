<?php
/**
 * MCPサーバー設定画面
 */
$this->BcAdmin->setTitle('MCPサーバー設定');
?>


<?= $this->BcAdminForm->create(null, ['novalidate' => true]) ?>

<div class="bca-form-table">
  <div class="bca-form-table__row">
    <div class="bca-form-table__head">
      ホスト
    </div>
    <div class="bca-form-table__col">
      <?= $this->BcAdminForm->control('host', [
        'type' => 'text',
        'value' => $config['host'],
        'help' => '通常は変更不要です（127.0.0.1）'
      ]) ?>
    </div>
  </div>

  <div class="bca-form-table__row">
    <div class="bca-form-table__head">
      ポート
    </div>
    <div class="bca-form-table__col">
      <?= $this->BcAdminForm->control('port', [
        'type' => 'number',
        'value' => $config['port'],
        'min' => 1024,
        'max' => 65535,
        'help' => '内部通信用ポート（デフォルト: 3000）'
      ]) ?>
    </div>
  </div>

</div>
</div>

<div class="bca-actions">
  <div class="bca-actions__main">
    <?= $this->BcAdminForm->submit('保存', [
      'div' => false,
      'class' => 'btn-red bca-btn bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?= $this->Html->link('キャンセル', ['action' => 'index'], [
      'class' => 'bca-btn',
      'data-bca-btn-type' => 'cancel',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>


  <?= $this->BcAdminForm->end() ?>


  <script>
    // フォーム送信前の確認
    document.getElementById('BtnSave').addEventListener('click', function (e) {
      const port = document.querySelector('input[name="port"]').value;
      if (port < 1024 || port > 65535) {
        e.preventDefault();
        alert('ポート番号は1024〜65535の範囲で指定してください');
        return false;
      }
    });
  </script>
