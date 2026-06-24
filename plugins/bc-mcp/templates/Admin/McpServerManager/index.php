<?php
/**
 * MCPサーバー管理画面
 */
?>


<!-- サーバー状態表示 -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">MCPサーバー状態</div>
  <div class="bca-panel-box__body">

    <div class="bca-data-list">
      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">状態</div>
        <div class="bca-data-list__item-value">
          <?php if ($status['running']): ?>
            <span class="bca-label bca-label--success">稼働中</span>
            <?php if ($status['pid']): ?>
              <small>(PID: <?= h($status['pid']) ?>)</small>
            <?php endif; ?>
          <?php else: ?>
            <span class="bca-label bca-label--danger">停止中</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">AIエージェント設定用URL</div>
        <div class="bca-data-list__item-value">
          <code><?= h($status['proxy_url']) ?></code>
          <button type="button" class="bca-btn bca-btn--sm" onclick="copyToClipboard('<?= h($status['proxy_url']) ?>')">
            コピー
          </button>
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">内部URL</div>
        <div class="bca-data-list__item-value">
          <code><?= h($status['internal_url']) ?></code>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- コントロールパネル -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">サーバー操作</div>
  <div class="bca-panel-box__body">

    <div class="bca-btn-group">
      <?php if ($status['running']): ?>
        <?= $this->BcAdminForm->postLink(
          '停止',
          ['action' => 'stop'],
          [
            'class' => 'bca-btn bca-btn--danger',
            'confirm' => 'MCPサーバーを停止しますか？'
          ]
        ) ?>

        <?= $this->BcAdminForm->postLink(
          '再起動',
          ['action' => 'restart'],
          [
            'class' => 'bca-btn bca-btn--warning',
            'confirm' => 'MCPサーバーを再起動しますか？'
          ]
        ) ?>
      <?php else: ?>
        <?= $this->BcAdminForm->postLink(
          '起動',
          ['action' => 'start'],
          [
            'class' => 'bca-btn bca-btn--success'
          ]
        ) ?>
      <?php endif; ?>

<!-- 設定はちゃんと動作確認していないためコメントアウト --><?php //= $this->Html->link(
//        '設定',
//        ['action' => 'configure'],
//        ['class' => 'bca-btn bca-btn--default']
//      ) ?>
    </div>

  </div>
</div>

<!-- 使用方法 -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">AIエージェントでの設定方法</div>
  <div class="bca-panel-box__body">

    <div class="bca-data-list">
      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">手順1</div>
        <div class="bca-data-list__item-value">
          上記の「起動」ボタンでMCPサーバーを起動してください
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">手順2</div>
        <div class="bca-data-list__item-value">
          AIエージェントの設定ファイルで上記AIエージェント設定用URLを設定してください
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">手順3</div>
        <div class="bca-data-list__item-value">
          AIエージェントから「ブログ記事を追加して」などの指示でbaserCMSを操作できます
        </div>
      </div>
    </div>

    <div class="bca-section">
      <h3>利用可能な機能</h3>
      <ul>
        <li>ブログ記事の、単一取得・一覧取得・追加・編集・削除</li>
        <li>カスタムエントリーの、単一取得・一覧取得・追加・編集・削除</li>
        <li>サーバー情報の取得</li>
      </ul>
    </div>

  </div>
</div>


<script>
  function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
      alert('URLをクリップボードにコピーしました');
    }, function (err) {
      console.error('コピーに失敗しました: ', err);
      // フォールバック
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
      alert('URLをクリップボードにコピーしました');
    });
  }

  // 自動リロード（ステータス確認用）
  setInterval(function () {
    if (document.visibilityState === 'visible') {
      location.reload();
    }
  }, 30000); // 30秒ごと
</script>

<style>
  .bca-data-list__item {
    display: flex;
  }

  .bca-data-list__item-label,
  .bca-data-list__item-value {
    padding: 0.2rem 0.5rem;
  }
</style>
