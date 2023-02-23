(()=>{
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
var e={searchOpenedTarget:null,mounted:function(){var e=$("#AdminSearchScript"),a=e.attr("data-adminSearchOpened");this.searchOpenedTarget=e.attr("data-adminSearchOpenedTarget"),this.changeSearchBox(a,0),this.registerEvents()},registerEvents:function(){$("#BtnMenuSearch").click((function(){"none"===$("#Search").css("display")?e.changeSearchBox(!0):e.changeSearchBox(!1)})),$("#BtnSearchClear").click((function(){return $('#Search input[type="text"], #Search input[type="date"], #Search input[type="tel"], #Search input[type="email"]').val(""),$('#Search input[type="radio"], #Search input[type="checkbox"]').removeAttr("checked"),$("#Search select").val(""),!1}))},changeSearchBox:function(e,a){void 0===a&&(a=300);var t=$.bcUtil.apiBaseUrl+"baser-core/utilities/save_search_opened/"+this.searchOpenedTarget;e?($("#Search").slideDown(a),t+="/1.json"):($("#Search").slideUp(a),t+=".json"),$.ajax({type:"POST",url:t})}};e.mounted()})();
//# sourceMappingURL=search.bundle.js.map