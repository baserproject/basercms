<?php
/**
 * OAuth2 認可画面テンプレート
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcBaser->setTitle('BcMcp アプリケーション認可');
?>


<div class="oauth2-authorize">
  <div class="card">
    <div class="card-header">
      <h3>BcMcp アプリケーション認可</h3>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <strong><?= h($client->getName()) ?></strong> が、<?php echo h(env('SITE_URL')) ?> に対して、以下の権限を要求しています。
      </div>

      <div class="permissions mb-3">
        <h4>要求されている権限</h4>
        <ul>
          <?php if (empty($scope)): ?>
            <li>基本的なアクセス権限</li>
          <?php else: ?>
            <?php foreach(explode(' ', $scope) as $scopeItem): ?>
              <li><?= h($this->OAuth2->getScopeDescription($scopeItem)) ?></li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>

      <div class="user-info mb-3">
        <small class="text-muted">
          ログインユーザー: <?= h($user->email) ?>
        </small>
      </div>

      <?= $this->BcAdminForm->create(null, ['type' => 'post']) ?>
      <?= $this->BcAdminForm->hidden('client_id', ['value' => $clientId]) ?>
      <?= $this->BcAdminForm->hidden('redirect_uri', ['value' => $redirectUri]) ?>
      <?= $this->BcAdminForm->hidden('scope', ['value' => $scope]) ?>
      <?= $this->BcAdminForm->hidden('state', ['value' => $state]) ?>

      <div class="submit section bca-actions">
        <div class="bca-actions__main">
          <?= $this->BcAdminForm->button('拒否', [
            'block' => true,
            'class' => 'bca-btn bca-actions__item',
            'data-bca-btn-type' => 'delete',
            'data-bca-btn-size' => 'lg',
            'data-bca-btn-color' => "danger",
            'type' => 'submit',
            'name' => 'action',
            'value' => 'deny'
          ]) ?>
          <?= $this->BcAdminForm->button('許可', [
            'div' => false,
            'class' => 'button bca-btn bca-actions__item',
            'data-bca-btn-type' => 'save',
            'data-bca-btn-size' => 'lg',
            'data-bca-btn-width' => 'lg',
            'type' => 'submit',
            'name' => 'action',
            'value' => 'approve'
          ]) ?>
        </div>
      </div>

      <?= $this->BcAdminForm->end() ?>
    </div>
  </div>
</div>

<style>
  #cboxOverlay {
    display: block !important;
    background: rgba(10, 10, 10, .9);
  }

  .bca-nav {
    display: none !important;
  }

  .oauth2-authorize {
    background-color: #f8f9fa;
    z-index: 10000;
    position: relative;
    width: 400px;
    margin-right: auto;
    margin-left: auto;
    display: block;
    border-radius: 5px;
    font-family: Arial, Geneva, sans-serif, "メイリオ", Verdana, "Hiragino Kaku Gothic Pro", "ヒラギノ角ゴ Pro W3", "ＭＳ Ｐゴシック";
    color: #424f44;
  }

  .oauth2-authorize .card {
    border: none;
  }

  .oauth2-authorize strong {
    font-weight: bold;
  }

  .oauth2-authorize .card-header {
    background-color: #59a73b;
    color: white;
    text-align: center;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
  }

  .oauth2-authorize .card-header h3 {
    font-size: 1.17em;
    font-weight: normal;
    padding-top: 15px;
    padding-bottom: 15px;
    margin-bottom: 0;
    background: none;
    line-height: 1.4;
  }

  .oauth2-authorize .card-body h4 {
    margin-bottom: 0;
    font-size: 1.4rem;
    margin-top: 20px;
    border: none;
    padding-bottom: 0;
  }

  .oauth2-authorize .card-body {
    padding: 2rem 2rem 0.5rem 2rem;
  }

  .oauth2-authorize .permissions {
    margin-bottom: 40px;
  }

  .oauth2-authorize .permissions ul {
    list-style-type: none;
    padding-left: 0;
    margin-top: 10px;
  }

  .oauth2-authorize .permissions li {
    padding: 0.25rem 0;
    margin: 0;
  }

  .oauth2-authorize .permissions li:before {
    content: "✓ ";
    color: #59a73b;
    font-weight: bold;
    background: none;
    position: relative;
    display: inline;
    top: 0;
    left: 0;
  }

  .oauth2-authorize .user-info {
    margin-bottom: 5px;
  }
</style>
