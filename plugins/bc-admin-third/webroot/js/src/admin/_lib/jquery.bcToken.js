/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * bcTokenプラグイン
 *
 * フロントエンドでCakePHPのセキュリティコンポーネントのトークンの管理等を行う
 */

(function ($) {

    $.bcToken = {

        /**
         * トークン
         */
        key: null,

        /**
         * トークンを取得済かどうか
         */
        requested: false,

        /**
         * トークンを取得中かどうか
         */
        requesting: false,

        /**
         * トークンURL
         */
        url: null,

        /**
         * デフォルトトークンURL
         */
        defaultUrl: '/baser/bc_form/get_token?requestview=false',

        /**
         * 初期化
         */
        init: function () {
            this.setTokenUrl();
        },

        /**
         * トークンを取得しているかどうかチェックし、取得していない場合取得する
         * コールバック処理を登録する前提となっており、コールバック処理完了後、再度、新しいトークンを取得する
         *
         * @param callback
         * @param config
         */
        check: function (callback, config) {
            if (this.requesting) {
                var timer = setInterval(function () {
                    if (!$.bcToken.requesting) {
                        clearInterval(timer);
                        if (callback) {
                            $.bcToken.execCallback(callback, config);
                        }
                    }
                }, 100);
            } else {
                if (!this.key) {
                    this.update(config).done(function () {
                        if (callback) {
                            $.bcToken.execCallback(callback, config);
                        }
                    });
                } else {
                    if (callback) {
                        this.execCallback(callback, config);
                    }
                }
            }
        },

        /**
         * コールバック処理を実行
         * @param callback
         * @param config
         */
        execCallback: function (callback, config) {
            var _config = {
                useUpdate: true
            };
            if (config !== undefined) {
                config = $.extend(_config, config);
            } else {
                config = _config;
            }
            var result = callback();
            if (config.useUpdate) {
                config.hideLoader = true;
                config.loaderType = 'none';
                if (result) {
                    result.always(function () {
                        $.bcToken.update(config);
                    });
                } else {
                    this.update(config);
                }
            }
        },

        /**
         * 新しいトークンをサーバーより取得する
         *
         * @param callback
         * @param config
         */
        update: function (config) {
            var _config = {
                type: 'GET'
            };
            if (config !== undefined) {
                config = $.extend(_config, config);
            } else {
                config = _config;
            }
            this.requesting = true;
            return $.bcUtil.ajax($.baseUrl() + this.url, function (result) {
                $.bcToken.key = result;
                $.bcToken.requesting = false;
                $('input[name="data[_Token][key]"]').val($.bcToken.key);
            }, $.extend(true, {}, config));
        },

        /**
         * トークンを取得した空のフォームを取得する
         * コールバック処理の引数として利用可能
         * @param url
         * @param callback
         * @param config
         */
        getForm: function (url, callback, config) {
            var form = $('<form/>');
            form.attr('action', url).attr('method', 'post');
            this.check(function () {
                form.append($.bcToken.getHiddenToken());
                callback(form);
            }, config);
        },

        /**
         * トークン用の hidden タグを取得する
         *
         * @returns {*}
         */
        getHiddenToken: function () {
            return $('<input name="_Token[key]" type="hidden">').val(this.key);
        },

        /**
         * 指定したURLに対しトークンを付加した上でPOST送信を行う
         * @param url
         */
        submitToken: function (url) {
            this.getForm(url, function (form) {
                $('body').append(form);
                form.submit();
            }, {useUpdate: false, hideLoader: false});
        },

        /**
         * 指定したセレクターのリンクのクリックイベントについて、
         * トークン付加前提のフォーム送信処理に置き換える
         *
         * @param selector
         */
        replaceLinkToSubmitToken: function (selector) {
            $(selector).each(function () {
                if ($(this).attr('onclick')) {
                    var regex = /if \(confirm\("(.+?)"\)/;
                    var result = $(this).attr('onclick').match(regex);
                    if (result) {
                        $(this).attr('data-confirm-message', result[1]);
                        $(this).get(0).onclick = '';
                        $(this).removeAttr('onclick');
                    }
                }
            });
            $(selector).click(function () {
                if ($(this).attr('data-confirm-message')) {
                    var message = $(this).attr('data-confirm-message');
                    message = JSON.parse('"' + message + '"').replace(/\\n/g, '\n');
                    if (!confirm(message)) {
                        return false;
                    }
                }
                $.bcToken.submitToken($(this).attr('href'));
                return false;
            });
        },

        /**
         * トークン発行URLのセット
         * @param url トークン発行URL。nullの場合はデフォルトURL
         */
        setTokenUrl: function (url) {
            this.url = url != null ? url : this.defaultUrl;
            return this;
        }

    };

})(jQuery);
