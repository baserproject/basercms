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
var t={fullUrl:null,mounted:function(){this.fullUrl=$("#AdminCustomEntriesFormScript").attr("data-fullUrl"),this.registerEvents()},registerEvents:function(){$("#BtnPreview").click(this.preview),$("#BtnAddLoop").click(this.addLoop),$(".btn-delete-loop").click(t.deleteLoop)},preview:function(){window.open("","preview");var e=$(this).parents("form"),r=e.attr("action"),a="&preview=default";"draft"==$("#ContentPreviewMode").val()&&(a="&preview=draft");var i=$.bcUtil.adminBaseUrl+"baser-core/preview/view?url="+t.fullUrl+a,o=$.bcUtil.baseUrl+"/baser-core/bc_form/get_token?requestview=false";return e.attr("target","preview").attr("action",i).submit(),e.attr("target","_self").attr("action",r),$.get(o,function(t){$('input[name="_csrfToken"]').val(t)}),!1},addLoop:function(){var e=$(this).attr("data-src"),r=$(this).attr("data-count"),a=$("#BcCcLoopSrc"+e).clone();a.find("input, select, textarea, hidden").each(function(){$(this).attr("name",$(this).attr("name").replace("__loop-src__",r)),void 0!==$(this).attr("id")&&$(this).attr("id",$(this).attr("id").replace("loop-src",r))}),a.find("label").each(function(){$(this).attr("for",$(this).attr("for").replace("loop-src",r))});var i="BcCcLoop"+e+"-"+r;a.attr("id",i),a.find(".btn-delete-loop").each(function(){$(this).attr("data-delete-target",i),$(this).click(t.deleteLoop)}),$("#loop-"+e).append(a),a.slideDown(150),$(this).attr("data-count",Number(r)+1),$("#"+i+" .bca-text-counter-value").remove(),$.bcUtil.setUpTextCounter("#"+i+" .bca-text-counter")},deleteLoop:function(){confirm("ループブロックを削除します。本当によろしいですか？")&&$("#"+$(this).attr("data-delete-target")).slideUp(150,function(){$(this).remove()})}};t.mounted()})();
//# sourceMappingURL=form.bundle.js.map