!function(e){function t(t){for(var r,i,a=t[0],s=t[1],c=t[2],d=0,f=[];d<a.length;d++)i=a[d],Object.prototype.hasOwnProperty.call(o,i)&&o[i]&&f.push(o[i][0]),o[i]=0;for(r in s)Object.prototype.hasOwnProperty.call(s,r)&&(e[r]=s[r]);for(l&&l(t);f.length;)f.shift()();return u.push.apply(u,c||[]),n()}function n(){for(var e,t=0;t<u.length;t++){for(var n=u[t],r=!0,a=1;a<n.length;a++){var s=n[a];0!==o[s]&&(r=!1)}r&&(u.splice(t--,1),e=i(i.s=n[0]))}return e}var r={},o={1:0},u=[];function i(t){if(r[t])return r[t].exports;var n=r[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=e,i.c=r,i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)i.d(n,r,function(t){return e[t]}.bind(null,r));return n},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="";var a=window.webpackJsonp=window.webpackJsonp||[],s=a.push.bind(a);a.push=t,a=a.slice();for(var c=0;c<a.length;c++)t(a[c]);var l=s;u.push([1,0]),n()}({1:function(e,t,n){"use strict";n.r(t);n(2),n(6),n(9),n(10)},10:function(e,t,n){(function(e){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
window.addEventListener("DOMContentLoaded",(function(){var t=document.querySelector('[data-js-tmpl="AdminMenu"]'),n=document.getElementById("AdminMenu"),r=null;try{r=JSON.parse(n?n.textContent:"{}")}catch(e){window.console&&console.warn("管理メニューのデータが破損しています（JSONデータが不正です）")}if(t&&r&&r.menuList&&r.menuList.length){var o=[],u=[];r.menuList.forEach((function(e,t){"system"===e.type?u.push(e):o.push(e)})),t.hidden=!1;var i=u.some((function(e){return e.current||e.expanded})),a=new Vue({el:t,data:{systemExpanded:i,baseURL:e.baseUrl(),currentSiteId:r.currentSiteId,contentList:o,isSystemSettingPage:i,systemList:u},methods:{openSystem:function(){a.systemExpanded=!a.systemExpanded}}})}else window.console&&console.warn("データが空のため、管理メニューは表示されませんでした")}))}).call(this,n(0))},9:function(e,t,n){(function(e){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
var t;(t=e).baseUrl=function(){return t("#AdminScript").attr("data-baseUrl")}}).call(this,n(0))}});
//# sourceMappingURL=common.bundle.js.map