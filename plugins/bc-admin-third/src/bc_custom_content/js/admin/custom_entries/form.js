/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * Custom Entries Form
 */
const customEntriesForm = {

    /**
     * フルURL
     */
    fullUrl: null,

    /**
     * 初期化
     */
    mounted() {
        this.fullUrl = $("#AdminCustomEntriesFormScript").attr('data-fullUrl');
        this.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnPreview").click(this.preview);
        $("#BtnAddLoop").click(this.addLoop);
        $(".btn-delete-loop").click(customEntriesForm.deleteLoop);
    },

    /**
     * プレビュー
     * @returns {boolean}
     */
    preview() {
        window.open('', 'preview');
        const form = $(this).parents('form');
        const action = form.attr('action');
        const previewUrl = $.bcUtil.adminBaseUrl + 'baser-core/preview/view?url=' + customEntriesForm.fullUrl + '&preview=default';
        const tokenUrl = $.bcUtil.baseUrl + '/baser-core/bc_form/get_token?requestview=false'
        form.attr('target', 'preview').attr('action', previewUrl).submit();
        form.attr('target', '_self').attr('action', action);

        $.get(tokenUrl, function (result) {
            $('input[name="_csrfToken"]').val(result);
        });
        return false;
    },

    /**
     * ループを追加する
     */
    addLoop() {
        var srcFieldName = $(this).attr('data-src');
        var count = $(this).attr('data-count');
        var clone = $("#BcCcLoopSrc" + srcFieldName).clone();

        clone.find('input, select, textarea, hidden').each(function () {
            $(this).attr('name', $(this).attr('name').replace('__loop-src__', count));
            if($(this).attr('id') !== undefined) {
                $(this).attr('id', $(this).attr('id').replace('loop-src', count));
            }
        });

        // label for属性もループ番号に変更
        clone.find('label').each(function () {
            $(this).attr('for', $(this).attr('for').replace('loop-src', count));
        });

        const id = "BcCcLoop" + srcFieldName + '-' + count;
        clone.attr('id', id);
        clone.find('.btn-delete-loop').each(function () {
            $(this).attr('data-delete-target', id);
            $(this).click(customEntriesForm.deleteLoop);
        });

        $("#loop-" + srcFieldName).append(clone);
        clone.slideDown(150);
        $(this).attr('data-count', Number(count) + 1);

        $("#" + id + " .bca-text-counter-value").remove();
        $.bcUtil.setUpTextCounter("#" + id + ' .bca-text-counter');
    },

    /**
     * ループを削除する
     */
    deleteLoop() {
        if (!confirm('ループブロックを削除します。本当によろしいですか？')) {
            return;
        }
        $("#" + $(this).attr('data-delete-target')).slideUp(150, function () {
            $(this).remove();
        });
    }

}

customEntriesForm.mounted();

