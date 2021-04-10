!function(e){function t(t){for(var r,i,s=t[0],c=t[1],l=t[2],d=0,f=[];d<s.length;d++)i=s[d],Object.prototype.hasOwnProperty.call(a,i)&&a[i]&&f.push(a[i][0]),a[i]=0;for(r in c)Object.prototype.hasOwnProperty.call(c,r)&&(e[r]=c[r]);for(u&&u(t);f.length;)f.shift()();return o.push.apply(o,l||[]),n()}function n(){for(var e,t=0;t<o.length;t++){for(var n=o[t],r=!0,s=1;s<n.length;s++){var c=n[s];0!==a[c]&&(r=!1)}r&&(o.splice(t--,1),e=i(i.s=n[0]))}return e}var r={},a={0:0},o=[];function i(t){if(r[t])return r[t].exports;var n=r[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=e,i.c=r,i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)i.d(n,r,function(t){return e[t]}.bind(null,r));return n},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="";var s=window.webpackJsonp=window.webpackJsonp||[],c=s.push.bind(s);s.push=t,s=s.slice();for(var l=0;l<s.length;l++)t(s[l]);var u=c;o.push([0,7]),n()}([function(e,t,n){"use strict";n.r(t);n(1),n(5),n(6),n(7),n(8)},,,,,function(e,t){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
!function(e){e.baseUrl=function(){return e("#AdminScript").attr("data-baseUrl")}}(jQuery)},function(e,t){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
!function(e){e.bcUtil={disabledHideMessage:!1,baseUrl:null,adminPrefix:null,init:function(t){var n=e("#AdminScript");e.bcUtil.baseUrl=n.attr("data-baseUrl"),e.bcUtil.adminPrefix=n.attr("data-adminPrefix"),void 0!==t.baseUrl&&(e.bcUtil.baseUrl=t.baseUrl),void 0!==t.adminPrefix&&(e.bcUtil.adminPrefix=t.adminPrefix)},showAlertMessage:function(t){e.bcUtil.hideMessage(),e("#BcSystemMessage").removeClass("notice-messge alert-message").addClass("alert-message").html(t),e("#BcMessageBox").fadeIn(500)},showNoticeMessage:function(t){e.bcUtil.hideMessage(),e("#BcSystemMessage").removeClass("notice-messge alert-message").addClass("notice-message").html(t),e("#BcMessageBox").fadeIn(500)},hideMessage:function(){e.bcUtil.disabledHideMessage||(e("#BcMessageBox").fadeOut(200),e("#AlertMessage").fadeOut(200),e("#MessageBox").fadeOut(200))},showLoader:function(t,n,r){switch((null==t||"none"!=t&&null==n)&&(t="over"),t){case"over":e("#Waiting").show();break;case"inner":var a=e("<div>").css({"text-align":"center"}).attr("id",r),o=e("<img>").attr("src",e.baseUrl+"/img/admin/ajax-loader.gif");a.html(o),e(n).html(a);break;case"after":o=e("<img>").attr("src",e.baseUrl+"/img/admin/ajax-loader-s.gif").attr("id",r);e(n).after(o);break;case"target":e(n).show()}},hideLoader:function(t,n,r){switch((null==t||"none"!=t&&null==n)&&(t="over"),t){case"over":e("#Waiting").hide();break;case"inner":case"after":e("#"+r).remove();break;case"target":e(n).show()}},ajax:function(t,n,r){var a,o,i;r||(r={});var s=!0;void 0!==r.loaderType&&(a=r.loaderType,delete r.loaderType),void 0!==r.loaderSelector&&(o=r.loaderSelector,delete r.loaderSelector,i=o.replace(/\./g,"").replace(/#/g,"").replace(/\s/g,"")+"loaderkey"),void 0!==r.hideLoader&&(s=r.hideLoader,delete r.loaderType);var c={url:t,type:"POST",dataType:"html",beforeSend:function(){e.bcUtil.showLoader(a,o,i)},complete:function(){s&&e.bcUtil.hideLoader(a,o,i)},error:function(t,n,r){e.bcUtil.showAjaxError("処理に失敗しました。",t,r)},success:n};return r&&e.extend(c,r),e.ajax(c)},showAjaxError:function(t,n,r){var a="";void 0!==n&&n.status&&(a="<br />("+n.status+") "),void 0!==n&&n.responseText?a+=n.responseText:void 0!==r&&(a+=r),e.bcUtil.showAlertMessage(t+a)}}}(jQuery)},function(e,t){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
!function(e){e((function(){e.bcToken={key:null,requested:!1,requesting:!1,url:null,defaultUrl:"/baser/bc_form/get_token?requestview=false",check:function(t,n){if(e.bcToken.requesting)var r=setInterval((function(){e.bcToken.requesting||(clearInterval(r),t&&e.bcToken.execCallback(t,n))}),100);else e.bcToken.key?t&&e.bcToken.execCallback(t,n):e.bcToken.update(n).done((function(){t&&e.bcToken.execCallback(t,n)}))},execCallback:function(t,n){var r={useUpdate:!0};n=void 0!==n?e.extend(r,n):r;var a=t();n.useUpdate&&(n.hideLoader=!0,n.loaderType="none",a?a.always((function(){e.bcToken.update(n)})):e.bcToken.update(n))},update:function(t){var n={type:"GET"};return t=void 0!==t?e.extend(n,t):n,e.bcToken.requesting=!0,e.bcUtil.ajax(e.baseUrl()+this.url,(function(t){e.bcToken.key=t,e.bcToken.requesting=!1,e('input[name="data[_Token][key]"]').val(e.bcToken.key)}),e.extend(!0,{},t))},getForm:function(t,n,r){var a=e("<form/>");a.attr("action",t).attr("method","post"),e.bcToken.check((function(){a.append(e.bcToken.getHiddenToken()),n(a)}),r)},getHiddenToken:function(){return e('<input name="_Token[key]" type="hidden">').val(e.bcToken.key)},submitToken:function(t){e.bcToken.getForm(t,(function(t){e("body").append(t),t.submit()}),{useUpdate:!1,hideLoader:!1})},replaceLinkToSubmitToken:function(t){e(t).each((function(){if(e(this).attr("onclick")){var t=e(this).attr("onclick").match(/if \(confirm\("(.+?)"\)/);t&&(e(this).attr("data-confirm-message",t[1]),e(this).get(0).onclick="",e(this).removeAttr("onclick"))}})),e(t).click((function(){if(e(this).attr("data-confirm-message")){var t=e(this).attr("data-confirm-message");if(t=JSON.parse('"'+t+'"').replace(/\\n/g,"\n"),!confirm(t))return!1}return e.bcToken.submitToken(e(this).attr("href")),!1}))},setTokenUrl:function(e){return this.url=null!=e?e:this.defaultUrl,this}},e.bcToken.setTokenUrl()}))}(jQuery)},function(e,t){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
window.addEventListener("DOMContentLoaded",(function(){var e=document.querySelector('[data-js-tmpl="AdminMenu"]'),t=document.getElementById("AdminMenu"),n=null;try{n=JSON.parse(t?t.textContent:"{}")}catch(e){window.console&&console.warn("管理メニューのデータが破損しています（JSONデータが不正です）")}if(e&&n&&n.menuList&&n.menuList.length){var r=[],a=[];n.menuList.forEach((function(e,t){"system"===e.type?a.push(e):r.push(e)})),e.hidden=!1;var o=a.some((function(e){return e.current||e.expanded})),i=new Vue({el:e,data:{systemExpanded:o,baseURL:$.baseUrl(),currentSiteId:n.currentSiteId,contentList:r,isSystemSettingPage:o,systemList:a},methods:{openSystem:function(){i.systemExpanded=!i.systemExpanded}}})}else window.console&&console.warn("データが空のため、管理メニューは表示されませんでした")}))}]);
//# sourceMappingURL=common.bundle.js.map