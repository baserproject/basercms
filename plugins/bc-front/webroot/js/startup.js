/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
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

