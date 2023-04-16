/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#AlertMessage");var e=$("#AdminUsersLoginScript").attr("data-isEnableLoginCredit");if(e&&$("body").hide(),e){var n=$("body"),i=$("#Logo");n.append($("<div>&nbsp;</div>").attr("id","Credit").show()),$("#HeaderInner").css("height","50px"),i.css("position","absolute"),i.css("z-index","10000"),t(e),n.fadeIn(50)}function t(e){e?$.bcCredit.show():function(e){var n=$("#Credit"),i=$("#Logo");n.length&&($("#HeaderInner").css("height","auto"),i.css("position","relative"),i.css("z-index","0"),$("#Wrap").css("height","280px"),e?(n.length&&n.fadeOut(1e3,e),e()):n.length&&n.fadeOut(1e3))}()}$("#Login").click((function(){t(!1)})),$("#LoginInner").click((function(e){e&&e.stopPropagation?e.stopPropagation():window.event.cancelBubble=!0}))}));
//# sourceMappingURL=login.bundle.js.map