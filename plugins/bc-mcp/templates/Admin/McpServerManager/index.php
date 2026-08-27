<?php
/**
 * MCPサーバー管理画面
 *
 * 常駐プロセスを持たないため、死活監視や起動・停止ではなく、接続情報・
 * 提供しているツール・直近の接続状況を表示する。
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $endpointUrl
 * @var string $authorizationServerMetadataUrl
 * @var string $protectedResourceMetadataUrl
 * @var array $protocolVersions
 * @var array $tools
 * @var array $negotiations
 * @var bool $encryptionKeyMissing
 */
?>


<?php if ($encryptionKeyMissing): ?>
  <div class="bca-panel-box">
    <div class="bca-panel-box__body">
      <p class="bca-alert bca-alert--error">
        暗号化キー（OAUTH2_ENC_KEY）が設定されていないため、MCP サーバーは停止しています。
        <code>config/.env</code> に <code>OAUTH2_ENC_KEY</code> を設定してください。
      </p>
    </div>
  </div>
<?php endif; ?>

<!-- 接続情報 -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">接続情報</div>
  <div class="bca-panel-box__body">

    <div class="bca-data-list">
      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">AIエージェント設定用URL</div>
        <div class="bca-data-list__item-value">
          <code><?= h($endpointUrl) ?></code>
          <button type="button" class="bca-btn bca-btn--sm" onclick="copyToClipboard('<?= h($endpointUrl) ?>')">
            コピー
          </button>
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">認可サーバーのメタデータ</div>
        <div class="bca-data-list__item-value">
          <code><?= h($authorizationServerMetadataUrl) ?></code>
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">保護リソースのメタデータ</div>
        <div class="bca-data-list__item-value">
          <code><?= h($protectedResourceMetadataUrl) ?></code>
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">対応プロトコルバージョン</div>
        <div class="bca-data-list__item-value">
          <?= h(implode(' / ', $protocolVersions)) ?>
          <p><small>最新の <?= h($protocolVersions[0]) ?> と、それ以前の世代（initialize 方式）の両方に対応しています。</small></p>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- AIエージェントでの設定方法 -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">AIエージェントでの設定方法</div>
  <div class="bca-panel-box__body">

    <div class="bca-data-list">
      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">手順1</div>
        <div class="bca-data-list__item-value">
          AIエージェントの設定で、上記の「AIエージェント設定用URL」をMCPサーバーとして登録してください
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">手順2</div>
        <div class="bca-data-list__item-value">
          AIエージェントから「ブログ記事を追加して」などの指示でbaserCMSを操作できます
        </div>
      </div>

      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">接続の要件</div>
        <div class="bca-data-list__item-value">
          <ul>
            <li>認可方式は Authorization Code + PKCE（S256）のみに対応しています</li>
            <li>クライアントはパブリッククライアント（<code>token_endpoint_auth_method: none</code>）として登録されます</li>
            <li>リダイレクト先は https、または <code>127.0.0.1</code> / <code>[::1]</code> / <code>localhost</code> の http のみ登録できます</li>
            <li>クライアント登録は同一のIPアドレスから1時間に10件までです</li>
          </ul>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- 直近の接続状況 -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">直近の接続状況</div>
  <div class="bca-panel-box__body">

    <?php if ($negotiations): ?>
      <table class="bca-table-listup">
        <thead>
        <tr>
          <th class="bca-table-listup__thead-th">日時</th>
          <th class="bca-table-listup__thead-th">世代</th>
          <th class="bca-table-listup__thead-th">プロトコルバージョン</th>
          <th class="bca-table-listup__thead-th">クライアント</th>
          <th class="bca-table-listup__thead-th">メソッド</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($negotiations as $negotiation): ?>
          <tr>
            <td class="bca-table-listup__tbody-td"><?= h($negotiation['loggedAt']) ?></td>
            <td class="bca-table-listup__tbody-td"><?= h($negotiation['era']) ?></td>
            <td class="bca-table-listup__tbody-td"><?= h($negotiation['protocolVersion']) ?></td>
            <td class="bca-table-listup__tbody-td">
              <?= h($negotiation['clientName']) ?>
              <?php if ($negotiation['clientVersion']): ?>
                <small>(<?= h($negotiation['clientVersion']) ?>)</small>
              <?php endif; ?>
            </td>
            <td class="bca-table-listup__tbody-td"><?= h($negotiation['method']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <p><small>クライアントが新しい世代（<?= h($protocolVersions[0]) ?>）へ切り替わったかどうかを、ここで確認できます。</small></p>
    <?php else: ?>
      <p>まだ接続がありません。</p>
    <?php endif; ?>

  </div>
</div>

<!-- 利用可能なツール -->
<div class="bca-panel-box">
  <div class="bca-panel-box__title">利用可能なツール（<?= count($tools) ?>件）</div>
  <div class="bca-panel-box__body">

    <?php if ($tools): ?>
      <table class="bca-table-listup">
        <thead>
        <tr>
          <th class="bca-table-listup__thead-th">ツール名</th>
          <th class="bca-table-listup__thead-th">説明</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($tools as $tool): ?>
          <tr>
            <td class="bca-table-listup__tbody-td"><code><?= h($tool['name']) ?></code></td>
            <td class="bca-table-listup__tbody-td"><?= h($tool['description'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>利用可能なツールがありません。</p>
    <?php endif; ?>

  </div>
</div>


<script>
  function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
      alert('URLをクリップボードにコピーしました');
    }, function (err) {
      console.error('コピーに失敗しました: ', err);
    });
  }
</script>
