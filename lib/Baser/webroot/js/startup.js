$(function(){
	$("a[rel='colorbox']").colorbox({transition:"fade"});
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