/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
import Cookies from 'js-cookie'

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
         * BaserCoreプレフィックス
         */
        baserCorePrefix: null,

        /**
         * 管理画面用URLプレフィックス
         */
        adminPrefix: null,

        /**
         * 管理画面用のベースURL
         */
        adminBaseUrl: null,

        /**
         * API用のベースURL
         */
        apiBaseUrl: null,

        /**
         * 管理画面用APIのベースURL
         */
        apiAdminBaseUrl: null,

        /**
         * Ajaxローダーのパス
         */
        ajaxLoaderPath: null,

        /**
         * Ajaxローダー（小）のパス
         */
        ajaxLoaderSmallPath: null,

        /**
         * 初期化
         *
         * @param config
         */
        init: function (config) {
            if(config === undefined) config = {};
            var adminScript = $("#AdminScript");
            $.bcUtil.baseUrl = adminScript.attr('data-baseUrl');
            $.bcUtil.baserCorePrefix = adminScript.attr('data-baserCorePrefix');
            $.bcUtil.adminPrefix = adminScript.attr('data-adminPrefix');
            $.bcUtil.ajaxLoaderPath = adminScript.attr('data-ajaxLoaderPath');
            $.bcUtil.ajaxLoaderSmallPath = adminScript.attr('data-ajaxLoaderSmallPath');
            $.bcUtil.frontFullUrl = adminScript.attr('data-frontFullUrl');
            if (config.baseUrl !== undefined) {
                $.bcUtil.baseUrl = config.baseUrl;
            }
            if (config.baserCorePrefix !== undefined) {
                $.bcUtil.baserCorePrefix = config.baserCorePrefix;
            }
            if (config.adminPrefix !== undefined) {
                $.bcUtil.adminPrefix = config.adminPrefix;
            }
            if (config.ajaxLoaderPath !== undefined) {
                $.bcUtil.ajaxLoaderPath = config.ajaxLoaderPath;
            }
            if (config.ajaxLoaderSmallPath !== undefined) {
                $.bcUtil.ajaxLoaderSmallPath = config.ajaxLoaderSmallPath;
            }
            $.bcUtil.adminBaseUrl = $.bcUtil.baseUrl + '/' + $.bcUtil.baserCorePrefix + '/' + $.bcUtil.adminPrefix + '/';
            $.bcUtil.apiBaseUrl = $.bcUtil.baseUrl + '/' + $.bcUtil.baserCorePrefix + '/api/';
            $.bcUtil.apiAdminBaseUrl = $.bcUtil.baseUrl + '/' + $.bcUtil.baserCorePrefix + '/api/admin/';
            this.setUpTextCounter();
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
                    var img = $('<img>').attr('src', $.bcUtil.ajaxLoaderPath);
                    div.html(img);
                    $(selector).html(div);
                    break;
                case 'after':
                    var img = $('<img>').attr('src', $.bcUtil.ajaxLoaderSmallPath).attr('id', key).css({
                        'width':'16px',
                        'vertical-align': 'middle',
                        'margin':'5px'
                    });
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
                    $.bcUtil.showAjaxError(bcI18n.commonExecFailedMessage, XMLHttpRequest, errorThrown);
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
                errorMessage = '<br>(' + XMLHttpRequest.status + ') ';
            }
            if(XMLHttpRequest !== undefined && XMLHttpRequest.responseJSON){
                errorMessage += XMLHttpRequest.responseJSON.message;
            }
            if (XMLHttpRequest !== undefined && XMLHttpRequest.responseText) {
                errorMessage += '<br>' + XMLHttpRequest.responseText;
            } else if (errorThrown !== undefined) {
                errorMessage += '<br>' + errorThrown;
            }
            $.bcUtil.showAlertMessage(message + errorMessage);
        },

        /**
         * フラッシュメッセージをセットする
         *
         * 一度しか表示できないメッセージ
         * @param message
         */
        setFlashMessage: function(message) {
            Cookies.set('bcFlashMessage', message);
        },

        /**
         * フラッシュメッセージを表示する
         *
         * 一度表示したら削除する
         */
        showFlashMessage: function () {
            let message = Cookies.get('bcFlashMessage');
            if(message !== undefined) {
                this.showNoticeMessage(message);
                Cookies.remove('bcFlashMessage')
            }
        },

        /**
         * ツールチップを初期化する
         *
         * @param config
         */
        initTooltip: function(config) {
            let btConfig = {
                target: '.bca-help',
                content: '.bca-helptext'
            };
            if(config !== undefined) {
                $.extend(btConfig, config);
            }
            let $help = $(btConfig.target);
            if ($help.bt) {
                $(btConfig.content).css('display', 'none');
                $.bt.options.closeWhenOthersOpen = true;
                $help.bt({
                    trigger: 'click',
                    positions: 'top',
                    shadow: true,
                    shadowOffsetX: 1,
                    shadowOffsetY: 1,
                    shadowBlur: 8,
                    shadowColor: 'rgba(101,101,101,.6)',
                    shadowOverlap: false,
                    noShadowOpts: {
                        strokeStyle: '#999',
                        strokeWidth: 1
                    },
                    width: '600px',
                    /*shrinkToFit: true,*/
                    spikeLength: 12,
                    spikeGirth: 18,
                    padding: 20,
                    cornerRadius: 0,
                    strokeWidth: 1, /*no stroke*/
                    strokeStyle: '#656565',
                    fill: 'rgba(255, 255, 255, 1.00)',
                    cssStyles: {
                        fontSize: '14px'
                    },
                    showTip: function (box) {
                        $(box).fadeIn(200);
                    },
                    hideTip: function (box, callback) {
                        $(box).animate({
                            opacity: 0
                        }, 100, callback);
                    },
                    contentSelector: `$(this).next('${btConfig.content}').html()`
                });
            }
        },

        /**
         * テキストカウンターをセットアップする
         */
        setUpTextCounter(selector) {
            if(selector === undefined) {
                selector = ".bca-text-counter";
            }
            const $textCounter = $(selector);
            $textCounter.after('<span class="bca-text-counter-value"></span>');
            $textCounter.keyup(function (){
                var len = $(this).val().length;
                var maxlen = $(this).attr('maxlength');
                if(!maxlen || maxlen === -1){
                    maxlen = '-';
                }
                $(this).next().html(len+' /<small>'+maxlen+'</small>');
            });
            $textCounter.keyup();
        },

    };
})(jQuery);
