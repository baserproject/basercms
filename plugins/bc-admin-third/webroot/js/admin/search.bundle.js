/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var e=$("#AdminSearchScript"),a=e.attr("data-adminSearchOpened"),c=e.attr("data-adminSearchOpenedTarget");function t(e,a,c){void 0===c&&(c=300);var t=$.bcUtil.apiBaseUrl+"baser-core/utilities/save_search_opened/"+e;a?($("#Search").slideDown(c),t+="/1.json"):($("#Search").slideUp(c),t+=".json"),$.ajax({type:"POST",url:t,headers:{Authorization:$.bcJwt.accessToken}})}t(c,a,0),$("#BtnMenuSearch").click((function(){"none"===$("#Search").css("display")?t(c,!0):t(c,!1)})),$("#CloseSearch").click((function(){t(c,!1)})),$("#BtnSearchClear").click((function(){return $('#Search input[type="text"]').val(""),$('#Search input[type="radio"], #Search input[type="checkbox"]').removeAttr("checked"),$("#Search select").val(""),!1}))}));
//# sourceMappingURL=search.bundle.js.map