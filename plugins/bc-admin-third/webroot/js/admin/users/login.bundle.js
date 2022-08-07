(()=>{function r(r,n){return function(r){if(Array.isArray(r))return r}
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */(r)||function(r,t){var n=null==r?null:"undefined"!=typeof Symbol&&r[Symbol.iterator]||r["@@iterator"];if(null==n)return;var e,o,a=[],i=!0,l=!1;try{for(n=n.call(r);!(i=(e=n.next()).done)&&(a.push(e.value),!t||a.length!==t);i=!0);}catch(r){l=!0,o=r}finally{try{i||null==n.return||n.return()}finally{if(l)throw o}}return a}(r,n)||function(r,n){if(!r)return;if("string"==typeof r)return t(r,n);var e=Object.prototype.toString.call(r).slice(8,-1);"Object"===e&&r.constructor&&(e=r.constructor.name);if("Map"===e||"Set"===e)return Array.from(r);if("Arguments"===e||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e))return t(r,n)}(r,n)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function t(r,t){(null==t||t>r.length)&&(t=r.length);for(var n=0,e=new Array(t);n<t;n++)e[n]=r[n];return e}$((function(){var t=$("#AlertMessage");$("#BtnLogin").click((function(){return $.bcUtil.showLoader(),t.fadeOut(),$.bcJwt.login($("#email").val(),$("#password").val(),$("#saved").prop("checked"),(function(t){var n;decodeURIComponent(location.search).replace("?","").split("&").forEach((function(t){var e=r(t.split("="),2),o=e[0],a=e[1];"redirect"===o&&(n=a)})),location.href=n||t.redirect}),(function(){t.fadeIn(),$.bcUtil.hideLoader()})),!1}))}))})();
//# sourceMappingURL=login.bundle.js.map