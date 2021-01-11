/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

$(function () {
    $("a[rel='colorbox']").colorbox({transition: "fade"});
    $("nav:first").accessibleMegaMenu({
        uuidPrefix: "accessible-megamenu",
        menuClass: "nav-menu",
        topNavItemClass: "nav-item",
        panelClass: "sub-nav",
        panelGroupClass: "sub-nav-group",
        hoverClass: "hover",
        focusClass: "focus",
        openClass: "open"
    });
});
