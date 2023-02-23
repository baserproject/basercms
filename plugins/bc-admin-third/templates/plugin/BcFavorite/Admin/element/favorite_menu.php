<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\Utility\BcUtil;

/**
 * [ADMIN] よく使う項目
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $currentPageName
 * @var string $currentPageUrl
 */
$this->BcBaser->js(['BcFavorite.admin/favorites/main.bundle', 'BcFavorite.admin/favorite.bundle'], true);
$user = BcUtil::loginUser();
?>


<nav id="FavoriteMenu" class="bca-nav-favorite">
    <favorite-index
        user-id="<?php echo $user->id ?>"
        current-page-name="<?php echo $currentPageName ?>"
        current-page-url="<?php echo $currentPageUrl ?>"
    ></favorite-index>
</nav>
