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
 * [ADMIN] テーマ一覧
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser', '
    <p><strong>初期データを読み込みます。よろしいですか？</strong></p><br>
    <p>※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。<br>
    ※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。</p>
  '),
  'confirmTitle1' => __d('baser', '初期データ読込'),
], ['escape' => false]);
$this->BcBaser->js([
  'admin/themes/index.bundle'
]);
$this->BcAdmin->setTitle(__d('baser', 'テーマ一覧'));
$this->BcAdmin->setHelp('themes_index');
?>


<div id="tabs">
  <ul>
    <li><a href="#DataList"><?php echo __d('baser', '所有テーマ') ?></a></li>
    <li><a href="#BaserMarket"><?php echo __d('baser', 'baserマーケット') ?></a></li>
  </ul>
  <div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('Themes/index_list') ?></div>
  <div id="BaserMarket">
    <div
      style="padding:20px;text-align:center;"><?php $this->BcBaser->img('admin/ajax-loader.gif', ['alt' => 'Loading...']) ?></div>
  </div>
</div>
