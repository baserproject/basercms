/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
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
            if(refreshToken && refreshToken !== 'null') {
                this.getToken(refreshToken)
            }
        },

        /**
         * Login
         */
        login: function (email, password, saved, successCallback, errorCallback) {
            $.ajax({
                url: $.bcUtil.apiAdminBaseUrl + 'baser-core/users/login.json',
                type: 'post',
                data: {
                    email: email,
                    password: password,
                    saved: (saved !== undefined && saved)? 1 : ''
                },
                dataType: 'json'
            }).done(function (response) {
                if (response) {
                    this.setToken(response.access_token, response.refresh_token)
                    if(successCallback) {
                        successCallback(response);
                    }
                }
            }.bind(this)).fail(function () {
                if(errorCallback) {
                    errorCallback()
                }
            })
        },

        /**
         * Get Token
         * @param refreshToken
         */
        getToken: function (refreshToken) {
            if(!refreshToken) {
                return
            }
            $.ajax({
                url: $.bcUtil.apiAdminBaseUrl + 'baser-core/users/refresh_token.json',
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
                if (error.status === 401) {
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
