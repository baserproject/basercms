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
 * [ADMIN] ウィジェットエリア一覧　ヘルプ
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcWidgetArea\Model\Entity\WidgetArea $widgetArea
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<p><?php echo __d('baser_core', '一つのウィジェットエリアは、左側の「利用できるウィジェット」からお好きなウィジェットを複数選択して作成する事ができます。') ?></p>
<ul>
  <li><?php echo __d('baser_core', 'まず、わかりやすい「ウィジェットエリア名」を決めて入力します。（例）サイドバー等') ?></li>
  <li><?php echo __d('baser_core', '「エリア名を保存する」ボタンをクリックすると「利用できるウィジェット」と「利用中のウィジェット」の二つの領域が表示されます') ?></li>
  <li><?php echo __d('baser_core', '「利用できるウィジェット」の中から利用したいウィジェットをドラッグして「利用中のウィジェット」の中でドロップします。') ?></li>
  <li><?php echo __d('baser_core', 'ウィジェットの設定欄が開きますので必要に応じて入力し「保存」ボタンをクリックします。') ?></li>
</ul>
<h5><?php echo __d('baser_core', 'ポイント') ?></h5>
<ul>
  <li><?php echo __d('baser_core', '「利用中のウィジェット」はドラッグアンドドロップで並び替える事ができます。') ?></li>
  <li><?php echo __d('baser_core', '一時的に利用しない場合は、削除せずにウィジェット設定の「利用する」チェックを外しておくと同じ設定のまま後で利用する事ができます。') ?></li>
  <?php if ($this->getRequest()->getParam('action') === 'edit'): ?>
    <li>
      <?php echo __d('baser_core', 'システム設定より設定できる標準ウィジェットエリアの他、個別にウィジェットを配置する場合は、テンプレートや、ページ記事中（ソース）に次のコードを貼り付けます。') ?>
      <pre>&lt;?php $this->BcBaser->widgetArea(<?php echo $widgetArea->id ?>) ?&gt;</pre>
    </li>
  <?php endif ?>
</ul>
