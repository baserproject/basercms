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
var t={fullUrl:null,mounted:function(){this.fullUrl=$("#AdminCustomEntriesFormScript").attr("data-fullUrl"),this.registerEvents()},registerEvents:function(){$("#BtnPreview").click(this.preview),$("#BtnAddLoop").click(this.addLoop),$(".btn-delete-loop").click(t.deleteLoop)},preview:function(){window.open("","preview");var e=$(this).parents("form"),r=e.attr("action"),i=$.bcUtil.adminBaseUrl+"baser-core/preview/view?url="+t.fullUrl+"&preview=default",a=$.bcUtil.baseUrl+"/baser-core/bc_form/get_token?requestview=false";return e.attr("target","preview").attr("action",i).submit(),e.attr("target","_self").attr("action",r),$.get(a,(function(t){$('input[name="_csrfToken"]').val(t)})),!1},addLoop:function(){var e=$(this).attr("data-src"),r=$(this).attr("data-count"),i=$("#BcCcLoopSrc"+e).clone();i.find("input, select, textarea, hidden").each((function(){$(this).attr("name",$(this).attr("name").replace("__loop-src__",r)),void 0!==$(this).attr("id")&&$(this).attr("id",$(this).attr("id").replace("loop-src",r))})),i.find("label").each((function(){$(this).attr("for",$(this).attr("for").replace("loop-src",r))}));var a="BcCcLoop"+e+"-"+r;i.attr("id",a),i.find(".btn-delete-loop").each((function(){$(this).attr("data-delete-target",a),$(this).click(t.deleteLoop)})),$("#loop-"+e).append(i),i.slideDown(150),$(this).attr("data-count",Number(r)+1),$("#"+a+" .bca-text-counter-value").remove(),$.bcUtil.setUpTextCounter("#"+a+" .bca-text-counter")},deleteLoop:function(){confirm("ループブロックを削除します。本当によろしいですか？")&&$("#"+$(this).attr("data-delete-target")).slideUp(150,(function(){$(this).remove()}))}};t.mounted()})();
//# sourceMappingURL=form.bundle.js.map