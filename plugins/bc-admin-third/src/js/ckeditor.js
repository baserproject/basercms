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
 * Ckeditorをセットアップし表示する
 */
$(function(){
    let script = $("#CkeditorScript");
    let config = {
        ckeditorField: script.attr('data-ckeditorField'),
        editorDomId: script.attr('data-editorDomId'),
        editorEnterBr: parseInt(script.attr('data-editorEnterBr')),
        editorUseDraft: Boolean(script.attr('data-editorUseDraft')),
        editorReadonlyPublish: Boolean(script.attr('data-editorReadonlyPublish')),
        editorDisableDraft: Boolean(script.attr('data-editorDisableDraft')),
        editorDisablePublish: Boolean(script.attr('data-editorDisablePublish')),
        editorStylesSet: script.attr('data-editorStylesSet'),
        editorUrl: script.attr('data-editorUrl'),
        draftAreaId: script.attr('data-draftAreaId'),
        publishAreaId: script.attr('data-publishAreaId'),
        previewModeId: script.attr('data-previewModeId'),
        initialStyle: JSON.parse(script.attr('data-initialStyle')),
        editorStyle: JSON.parse(script.attr('data-editorStyle')),
        themeEditorCsses: JSON.parse(script.attr('data-themeEditorCsses')),
        editorOptions: JSON.parse(script.attr('data-editorOptions')),
    }
    $.bcCkeditor.show(config);
});

