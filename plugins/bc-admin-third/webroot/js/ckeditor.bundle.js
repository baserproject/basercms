/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var t=$("#CkeditorScript"),a={ckeditorField:t.attr("data-ckeditorField"),editorDomId:t.attr("data-editorDomId"),editorEnterBr:parseInt(t.attr("data-editorEnterBr")),editorUseDraft:Boolean(t.attr("data-editorUseDraft")),editorReadonlyPublish:Boolean(t.attr("data-editorReadonlyPublish")),editorDisableDraft:Boolean(t.attr("data-editorDisableDraft")),editorDisablePublish:Boolean(t.attr("data-editorDisablePublish")),editorStylesSet:t.attr("data-editorStylesSet"),editorUrl:t.attr("data-editorUrl"),draftAreaId:t.attr("data-draftAreaId"),publishAreaId:t.attr("data-publishAreaId"),previewModeId:t.attr("data-previewModeId"),initialStyle:JSON.parse(t.attr("data-initialStyle")),editorStyle:JSON.parse(t.attr("data-editorStyle")),themeEditorCsses:JSON.parse(t.attr("data-themeEditorCsses")),editorOptions:JSON.parse(t.attr("data-editorOptions"))};$.bcCkeditor.show(a)}));
//# sourceMappingURL=ckeditor.bundle.js.map