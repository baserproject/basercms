/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

(function ($) {
    $.bcJwt = {

        /**
         * Access Token
         */
        accessToken: null,

        /**
         * Init
         */
        init: function() {
            let refreshToken = localStorage.getItem('refreshToken');
            if(refreshToken) {
                this.getToken(refreshToken)
            }
        },

        /**
         * Login
         */
        login: function (email, password) {
            $.ajax({
                url: $.bcUtil.apiBaseUrl + 'baser-core/users/login.json',
                type: 'post',
                async: false,
                data: {
                    email: email,
                    password: password
                },
                dataType: 'json',
            }).done(function (response) {
                if (response) {
                    this.setToken(response.access_token, response.refresh_token)
                }
            }.bind(this)).fail(function () {
                alert('システムエラーが発生しました。ブラウザをリロードしてください。')
            })
        },

        /**
         * Get Token
         * @param refreshToken
         */
        getToken: function (refreshToken) {
            $.ajax({
                url: $.bcUtil.apiBaseUrl + 'baser-core/users/refresh_token.json',
                type: 'get',
                async: false,
                headers: {
                    "Authorization": refreshToken,
                    'Content-Type': 'application/json'
                },
                dataType: 'json',
            }).done(function (response) {
                if (response) {
                    this.setToken(response.access_token, response.refresh_token)
                } else {
                    alert('APIトークンが取得できませんでした。ブラウザをリロードしてください。')
                }
            }.bind(this)).fail(function(error){
                if (error.response.status === 401) {
                    localStorage.setItem('refreshToken', '')
                }
            })
        },

        /**
         * Set Token
         * @param accessToken
         * @param refreshToken
         */
        setToken: function (accessToken, refreshToken) {
            this.accessToken = accessToken
            localStorage.setItem('refreshToken', refreshToken);
        },

        /**
         * Logout
         */
        logout: function () {
            this.removeToken()
        },

        /**
         * Remove Token
         */
        removeToken: function () {
            localStorage.setItem('refreshToken', null);
            this.accessToken = null
        }

    };
})(jQuery);
