/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var e=$("#AdminSearchScript"),a=e.attr("data-adminSearchOpened"),c=e.attr("data-adminSearchOpenedSaveUrl");function t(e,a){void 0===a&&(a=300);var t=c;e?($("#Search").slideDown(a),t+="/1"):($("#Search").slideUp(a),t+="/"),$.ajax({type:"GET",url:t,headers:{Authorization:$.bcJwt.accessToken}})}t(a,0),$("#BtnMenuSearch").click((function(){"none"===$("#Search").css("display")?t(!0):t(!1)})),$("#CloseSearch").click((function(){t(!1)})),$("#BtnSearchClear").click((function(){return $('#Search input[type="text"]').val(""),$('#Search input[type="radio"], #Search input[type="checkbox"]').removeAttr("checked"),$("#Search select").val(""),!1}))}));
//# sourceMappingURL=search.bundle.js.map