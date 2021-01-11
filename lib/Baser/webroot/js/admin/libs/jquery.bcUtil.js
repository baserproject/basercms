/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

(function ($) {
    $.bcUtil = {
        /**
         * hideMessage() を無効にする
         */
        disabledHideMessage: false,

        /**
         * ベースとなるURL
         */
        baseUrl: null,

        /**
         * 管理画面用URLプレフィックス
         */
        adminPrefix: null,

        /**
         * 初期化
         *
         * @param config
         */
        init: function (config) {
            if (config.baseUrl !== undefined) {
                $.bcUtil.baseUrl = config.baseUrl;
            }
            if (config.adminPrefix !== undefined) {
                $.bcUtil.adminPrefix = config.adminPrefix;
            }
        },
        /**
         * アラートメッセージを表示
         *
         * @param message
         */
        showAlertMessage: function (message) {
            $.bcUtil.hideMessage();
            $("#BcSystemMessage")
                .removeClass('notice-messge alert-message')
                .addClass('alert-message')
                .html(message);
            $("#BcMessageBox").fadeIn(500);
        },

        /**
         * ノーティスメッセージを表示
         *
         * @param message
         */
        showNoticeMessage: function (message) {
            $.bcUtil.hideMessage();
            $("#BcSystemMessage")
                .removeClass('notice-messge alert-message')
                .addClass('notice-message')
                .html(message);
            $("#BcMessageBox").fadeIn(500);
        },

        /**
         * メッセージを隠す
         */
        hideMessage: function () {
            if (!$.bcUtil.disabledHideMessage) {
                $("#BcMessageBox").fadeOut(200);
                $("#AlertMessage").fadeOut(200);
                $("#MessageBox").fadeOut(200);
            }
        },

        /**
         * ローダーを表示
         */
        showLoader: function (type, selector, key) {
            if (type == undefined || (type != 'none' && selector == undefined)) {
                type = 'over';
            }
            switch (type) {
                case 'over':
                    $("#Waiting").show();
                    break;
                case 'inner':
                    var div = $('<div>').css({'text-align': 'center'}).attr('id', key);
                    var img = $('<img>').attr('src', $.baseUrl + '/img/admin/ajax-loader.gif');
                    div.html(img);
                    $(selector).html(div);
                    break;
                case 'after':
                    var img = $('<img>').attr('src', $.baseUrl + '/img/admin/ajax-loader-s.gif').attr('id', key);
                    $(selector).after(img);
                    break;
                case 'target':
                    $(selector).show();
                    break;
                case 'none':
                    break;
            }
        },

        /**
         * ローダーを隠す
         */
        hideLoader: function (type, selector, key) {
            if (type == undefined || (type != 'none' && selector == undefined)) {
                type = 'over';
            }
            switch (type) {
                case 'over':
                    $("#Waiting").hide();
                    break;
                case 'inner':
                    $("#" + key).remove();
                    break;
                case 'after':
                    $("#" + key).remove();
                    break;
                case 'target':
                    $(selector).show();
                    break;
                case 'none':
                    break;
            }
        },

        /**
         * Ajax
         */
        ajax: function (url, success, config) {
            if (!config) {
                config = {};
            }
            var loaderType, loaderSelector, loaderKey;
            var hideLoader = true;
            if (typeof config.loaderType !== 'undefined') {
                loaderType = config.loaderType;
                delete config.loaderType;
            }
            if (typeof config.loaderSelector !== 'undefined') {
                loaderSelector = config.loaderSelector;
                delete config.loaderSelector;
                loaderKey = loaderSelector.replace(/\./g, '').replace(/#/g, '').replace(/\s/g, '') + 'loaderkey';
            }
            if (typeof config.hideLoader !== 'undefined') {
                hideLoader = config.hideLoader;
                delete config.loaderType;
            }
            var ajaxConfig = {
                url: url,
                type: 'POST',
                dataType: 'html',
                beforeSend: function () {
                    $.bcUtil.showLoader(loaderType, loaderSelector, loaderKey);
                },
                complete: function () {
                    if (hideLoader) {
                        $.bcUtil.hideLoader(loaderType, loaderSelector, loaderKey);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $.bcUtil.showAjaxError('処理に失敗しました。', XMLHttpRequest, errorThrown);
                },
                success: success
            };
            if (config) {
                $.extend(ajaxConfig, config);
            }
            return $.ajax(ajaxConfig);
        },

        /**
         * Ajax のエラーメッセージを表示
         *
         * @param XMLHttpRequest
         * @param errorThrown
         * @param message
         */
        showAjaxError: function (message, XMLHttpRequest, errorThrown) {
            var errorMessage = '';
            if (XMLHttpRequest !== undefined && XMLHttpRequest.status) {
                errorMessage = '<br />(' + XMLHttpRequest.status + ') ';
            }
            if (XMLHttpRequest !== undefined && XMLHttpRequest.responseText) {
                errorMessage += XMLHttpRequest.responseText;
            } else if (errorThrown !== undefined) {
                errorMessage += errorThrown;
            }
            $.bcUtil.showAlertMessage(message + errorMessage);
        }
    };
})(jQuery);
