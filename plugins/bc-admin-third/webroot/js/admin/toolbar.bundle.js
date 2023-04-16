/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#UserMenu, #ToolMenu").fixedMenu(),$("#UserMenu ul li div ul li").each((function(){$(this).html().replace(/(^\s+)|(\s+$)/g,"")||$(this).remove()})),$("#UserMenu ul li div ul").each((function(){$(this).html().replace(/(^\s+)|(\s+$)/g,"")||($(this).prev().remove(),$(this).remove())})),$("#BtnLogout").click((function(){$.bcJwt.logout()}))}));
//# sourceMappingURL=toolbar.bundle.js.map