/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var e=!1,s=$("#BsMenuBtn");s.click((function(){e?(e=!1,s.removeClass("bs-open"),$("#BsMenuContent").removeClass("bs-open")):(e=!0,s.addClass("bs-open"),$("#BsMenuContent").addClass("bs-open"))})),$("#MainImage").show().bxSlider({mode:"fade",auto:!0}),$("a[rel='colorbox']").colorbox({transition:"fade",maxWidth:"80%"}),$(".bs-header__nav.use-mega-menu").accessibleMegaMenu({uuidPrefix:"accessible-megamenu",menuClass:"nav-menu",topNavItemClass:"nav-item",panelClass:"sub-nav",panelGroupClass:"sub-nav-group",hoverClass:"hover",focusClass:"focus",openClass:"open"}),$.bcUtil.init(),$.bcToken.setTokenUrl($.bcUtil.baseUrl+"/baser-core/bc_form/get_token?requestview=false")}));
//# sourceMappingURL=startup.bundle.js.map