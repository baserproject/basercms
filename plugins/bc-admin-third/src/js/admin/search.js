/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

const bcSearchBox = {

    /**
     * 検索ボックス開閉ターゲットとなるID
     */
    searchOpenedTarget: null,

    /**
     * 初期化
     */
    mounted() {
        var script = $("#AdminSearchScript");
        var adminSearchOpened = script.attr('data-adminSearchOpened');
        this.searchOpenedTarget = script.attr('data-adminSearchOpenedTarget');
        this.changeSearchBox(adminSearchOpened, 0);
        this.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        // 検索ボックス開閉
        $('#BtnMenuSearch').click(function(){
            if($('#Search').css('display') === 'none'){
                bcSearchBox.changeSearchBox(true);
            } else {
                bcSearchBox.changeSearchBox(false);
            }
        });
        // クリアボタン
        $('#BtnSearchClear').click(function () {
            $('#Search input[type="text"], #Search input[type="date"], #Search input[type="tel"], #Search input[type="email"]').val('');
            $('#Search input[type="radio"], #Search input[type="checkbox"]').removeAttr('checked');
            $('#Search select').val('');
            return false;
        });
    },

    /**
     * 検索ボックスの開閉切り替え
     */
    changeSearchBox(open, time) {
        if(time === undefined) time = 300;
        var url = $.bcUtil.apiAdminBaseUrl + 'baser-core/utilities/save_search_opened/' + this.searchOpenedTarget;
        if(open){
            $('#Search').slideDown(time);
            url += '/1.json';
        } else {
            $('#Search').slideUp(time);
            url += '.json';
        }
        $.bcToken.check(function(){
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    '_csrfToken': $.bcToken.key
                },
            });
        }, {loaderType : 'none'});
    }

}

bcSearchBox.mounted();
