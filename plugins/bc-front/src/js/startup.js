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
 * Startup
 */
$(function(){
    let isOpenedMenu = false;
    var menuButton = $("#BsMenuBtn");
    menuButton.click(clickMenuBtn);
	$("#MainImage").show().bxSlider({mode:"fade", auto:true});
	$("a[rel='colorbox']").colorbox({transition:"fade", maxWidth:"80%"});
	$(".bs-header__nav.use-mega-menu").accessibleMegaMenu({
		uuidPrefix: "accessible-megamenu",
		menuClass: "nav-menu",
		topNavItemClass: "nav-item",
		panelClass: "sub-nav",
		panelGroupClass: "sub-nav-group",
		hoverClass: "hover",
		focusClass: "focus",
		openClass: "open"
	});

    $.bcUtil.init();
	$.bcToken.setTokenUrl($.bcUtil.baseUrl + '/baser-core/bc_form/get_token?requestview=false');

    function clickMenuBtn() {
        if(isOpenedMenu) {
            isOpenedMenu = false;
            menuButton.removeClass("bs-open")
            $("#BsMenuContent").removeClass("bs-open")
        } else {
            isOpenedMenu = true;
            menuButton.addClass("bs-open")
            $("#BsMenuContent").addClass("bs-open")
        }
    }
});
