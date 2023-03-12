<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\Utility\BcUtil;

/**
 * [ADMIN] よく使う項目
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $currentPageName
 * @var string $currentPageUrl
 */
echo $this->BcBaser->i18nScript([
  'labelTitle' => __d('baser_core', 'お気に入り名'),
  'labelUrl' => __d('baser_core', 'URL'),
  'addTitle' => __d('baser_core', 'お気に入り登録'),
  'editTitle' => __d('baser_core', 'お気に入り編集'),
  'buttonSubmit' => __d('baser_core', '確定'),
  'buttonCancel' => __d('baser_core', 'キャンセル'),
  'i18nFavorite' => __d('baser_core', 'お気に入り'),
  'i18nNoData' => __d('baser_core', '登録がありません'),
  'i18nEdit' => __d('baser_core', '編集'),
  'i18nDelete' => __d('baser_core', '削除'),
  'alertServerError' => __d('baser_core', 'サーバーの処理に失敗しました。'),
  'alertRequire' => __d('baser_core', '必須項目です')
], ['block' => false]);
$this->BcBaser->js('BcFavorite.admin/favorites/main.bundle', true, ['defer' => true]);
$user = BcUtil::loginUser();
?>


<nav id="FavoriteMenu" class="bca-nav-favorite">
    <favorite-index
        user-id="<?php echo $user->id ?>"
        current-page-name="<?php echo $currentPageName ?>"
        current-page-url="<?php echo $currentPageUrl ?>"
    ></favorite-index>
</nav>
