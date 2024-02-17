/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var e=$("#alias").val();function i(){var e=$("#id").val(),i=$("#main-site-id").val();void 0===i&&(i=1);var n=$.bcUtil.apiAdminBaseUrl+"baser-core/sites/get_selectable_devices_and_lang/"+i;void 0!==e&&(n+="/"+e),n+=".json",$.bcUtil.ajax(n,(function(e){var i=$("#device"),n=$("#lang"),c=i.val(),o=n.val();i.find("option").remove(),n.find("option").remove(),e=$.parseJSON(e),$.each(e.devices,(function(e,a){i.append($("<option>").val(e).text(a).prop("selected",e===c))})),$.each(e.langs,(function(e,i){n.append($("<option>").val(e).text(i).prop("selected",e===o))})),a()}),{type:"GET",loaderType:"after",loaderSelector:"#main-site-id"})}function a(){var e=$("#auto-redirect"),i=$("#same-main-url"),a=$("#auto-link"),n=$("#SpanSiteAutoRedirect"),c=$("#SpanSiteAutoLink");$("#device").val()||$("#lang").val()?$("#SectionAccessType").show():($("#SectionAccessType").hide(),e.prop("checked",!1),i.prop("checked",!1),a.prop("checked",!1)),i.prop("checked")?(e.prop("checked",!1),n.hide(),a.prop("checked",!1),c.hide()):(n.show(),"mobile"==$("#device").val()||"smartphone"==$("#device").val()?c.show():c.hide())}$("#BtnSave").click((function(){if(e&&e!=$("#alias").val())return $.bcConfirm.show({title:bcI18n.confirmTitle1,message:bcI18n.confirmMessage2,ok:function(){$.bcUtil.showLoader(),$("#BtnSave").parents("form").submit()}}),!1;$.bcUtil.showLoader()})),$("#main-site-id").change(i),$("#device, #lang").change(a),$('input[name="same_main_url"]').click(a),i(),0==$("input[name='use_subdomain']:checked").val()?($("#DomainType").hide(),$("#domain-type-0").prop("checked",!0)):$("#DomainType").show("slow"),$("input[name='use_subdomain']").click("chengeUseDomain")}));
//# sourceMappingURL=form.bundle.js.map