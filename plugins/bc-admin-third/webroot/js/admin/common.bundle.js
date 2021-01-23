!function(e){function t(t){for(var n,s,i=t[0],l=t[1],d=t[2],u=0,f=[];u<i.length;u++)s=i[u],Object.prototype.hasOwnProperty.call(a,s)&&a[s]&&f.push(a[s][0]),a[s]=0;for(n in l)Object.prototype.hasOwnProperty.call(l,n)&&(e[n]=l[n]);for(c&&c(t);f.length;)f.shift()();return o.push.apply(o,d||[]),r()}function r(){for(var e,t=0;t<o.length;t++){for(var r=o[t],n=!0,i=1;i<r.length;i++){var l=r[i];0!==a[l]&&(n=!1)}n&&(o.splice(t--,1),e=s(s.s=r[0]))}return e}var n={},a={0:0},o=[];function s(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,s),r.l=!0,r.exports}s.m=e,s.c=n,s.d=function(e,t,r){s.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},s.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},s.t=function(e,t){if(1&t&&(e=s(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(s.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)s.d(r,n,function(t){return e[t]}.bind(null,n));return r},s.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return s.d(t,"a",t),t},s.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},s.p="";var i=window.webpackJsonp=window.webpackJsonp||[],l=i.push.bind(i);i.push=t,i=i.slice();for(var d=0;d<i.length;d++)t(i[d]);var c=l;o.push([0,6]),r()}([function(e,t,r){"use strict";r.r(t);r(1),r(5),r(6),r(7)},,,,,function(e,t){
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
!function(e){e.bcUtil={disabledHideMessage:!1,baseUrl:null,adminPrefix:null,init:function(t){var r=e("#AdminScript");e.bcUtil.baseUrl=r.attr("data-baseUrl"),e.bcUtil.adminPrefix=r.attr("data-adminPrefix"),void 0!==t.baseUrl&&(e.bcUtil.baseUrl=t.baseUrl),void 0!==t.adminPrefix&&(e.bcUtil.adminPrefix=t.adminPrefix)},showAlertMessage:function(t){e.bcUtil.hideMessage(),e("#BcSystemMessage").removeClass("notice-messge alert-message").addClass("alert-message").html(t),e("#BcMessageBox").fadeIn(500)},showNoticeMessage:function(t){e.bcUtil.hideMessage(),e("#BcSystemMessage").removeClass("notice-messge alert-message").addClass("notice-message").html(t),e("#BcMessageBox").fadeIn(500)},hideMessage:function(){e.bcUtil.disabledHideMessage||(e("#BcMessageBox").fadeOut(200),e("#AlertMessage").fadeOut(200),e("#MessageBox").fadeOut(200))},showLoader:function(t,r,n){switch((null==t||"none"!=t&&null==r)&&(t="over"),t){case"over":e("#Waiting").show();break;case"inner":var a=e("<div>").css({"text-align":"center"}).attr("id",n),o=e("<img>").attr("src",e.baseUrl+"/img/admin/ajax-loader.gif");a.html(o),e(r).html(a);break;case"after":o=e("<img>").attr("src",e.baseUrl+"/img/admin/ajax-loader-s.gif").attr("id",n);e(r).after(o);break;case"target":e(r).show()}},hideLoader:function(t,r,n){switch((null==t||"none"!=t&&null==r)&&(t="over"),t){case"over":e("#Waiting").hide();break;case"inner":case"after":e("#"+n).remove();break;case"target":e(r).show()}},ajax:function(t,r,n){var a,o,s;n||(n={});var i=!0;void 0!==n.loaderType&&(a=n.loaderType,delete n.loaderType),void 0!==n.loaderSelector&&(o=n.loaderSelector,delete n.loaderSelector,s=o.replace(/\./g,"").replace(/#/g,"").replace(/\s/g,"")+"loaderkey"),void 0!==n.hideLoader&&(i=n.hideLoader,delete n.loaderType);var l={url:t,type:"POST",dataType:"html",beforeSend:function(){e.bcUtil.showLoader(a,o,s)},complete:function(){i&&e.bcUtil.hideLoader(a,o,s)},error:function(t,r,n){e.bcUtil.showAjaxError("処理に失敗しました。",t,n)},success:r};return n&&e.extend(l,n),e.ajax(l)},showAjaxError:function(t,r,n){var a="";void 0!==r&&r.status&&(a="<br />("+r.status+") "),void 0!==r&&r.responseText?a+=r.responseText:void 0!==n&&(a+=n),e.bcUtil.showAlertMessage(t+a)}}}(jQuery)},function(e,t){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
window.addEventListener("DOMContentLoaded",(function(){var e=document.querySelector('[data-js-tmpl="AdminMenu"]'),t=document.getElementById("AdminMenu"),r=null;try{r=JSON.parse(t?t.textContent:"{}")}catch(e){window.console&&console.warn("管理メニューのデータが破損しています（JSONデータが不正です）")}if(e&&r&&r.menuList&&r.menuList.length){var n=[],a=[];r.menuList.forEach((function(e,t){"system"===e.type?a.push(e):n.push(e)})),e.hidden=!1;var o=a.some((function(e){return e.current||e.expanded})),s=new Vue({el:e,data:{systemExpanded:o,baseURL:$.baseUrl(),currentSiteId:r.currentSiteId,contentList:n,isSystemSettingPage:o,systemList:a},methods:{openSystem:function(){s.systemExpanded=!s.systemExpanded}}})}else window.console&&console.warn("データが空のため、管理メニューは表示されませんでした")}))}]);
//# sourceMappingURL=common.bundle.js.map