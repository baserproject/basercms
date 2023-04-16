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
    $.bcTimeUtil = {

        /**
         * 現在の日時を文字列で取得する
         *
         * @returns {string}
         */
        getNowDateTime : function () {
            return $.bcTimeUtil.getNowDate() + ' ' + $.bcTimeUtil.getNowTime();
        },

        /**
         * 現在の日付を文字列で取得する
         *
         * @returns {string}
         */
        getNowDate : function () {
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            if (month < 10) month = '0' + month;
            if (day < 10) day = '0' + day;
            return year + '/' + month + '/' + day;
        },

        /**
         * 現在の時間を文字列で取得する
         *
         * @returns {string}
         */
        getNowTime : function () {
            var date = new Date();
            var hour = date.getHours();
            var minute = date.getMinutes();
            if (hour < 10) hour = '0' + hour;
            if (minute < 10) minute = '0' + minute;
            return hour + ':' + minute;
        }

    };
})(jQuery);
