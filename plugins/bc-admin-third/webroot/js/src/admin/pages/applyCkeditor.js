window.addEventListener('DOMContentLoaded', function() {
    // すでにvar宣言されている変数をjavascript用に変換
    editorEnterBr = parseInt(editorEnterBr);
    editorUseDraft = Boolean(editorUseDraft);
    editorReadonlyPublish = Boolean(editorReadonlyPublish);
    editorDisableDraft = Boolean(editorDisableDraft);
    editorDisablePublish = Boolean(editorDisablePublish);
    var  initStatus = false;
    var  styleInitStatus = false;
    $(function() {
        if (!initStatus) {
            CKEDITOR.addStylesSet('basercms', initialStyle);
            initStatus = true;
        }
        if (!styleInitStatus && editorStyle) {
            editorStyle[editorStylesSet].map((editor, key) => CKEDITOR.addStylesSet(key, editor));
            styleInitStatus = true;
        }
        if (editorUrl) {
            CKEDITOR.config.templates_files = [editorUrl];
        }
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.extraPlugins = 'draft,showprotected';
        CKEDITOR.config.stylesCombo_stylesSet = editorStylesSet;
        CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );
        // 空「i」タグを消さないようにする
        CKEDITOR.dtd.$removeEmpty["i"] = false;
        // 空「span」タグを消さないようにする
        CKEDITOR.dtd.$removeEmpty["span"] = false;
        if (editorEnterBr) {
            CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        }
        // 複数入ることを前提に配列型に変更
        if (typeof CKEDITOR.config.contentsCss === 'string') {
            CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss];
        }
        themeEditorCsses.map((css) => {
            if (Array.isArray(CKEDITOR.config.contentsCss)) {
                CKEDITOR.config.contentsCss.push(css);
            }
        });
        CKEDITOR[ckeditorField] = CKEDITOR.replace(editorDomId, editorOptions);
        CKEDITOR[ckeditorField].on('pluginsLoaded', function(event) {
            if (editorUseDraft) {
                if (draftAreaId) {
                    CKEDITOR[ckeditorField].draftDraftAreaId = draftAreaId;
                }
                if (publishAreaId) {
                    CKEDITOR[ckeditorField].draftPublishAreaId = publishAreaId;
                }
                if (editorReadonlyPublish) {
                    CKEDITOR[ckeditorField].draftReadOnlyPublish = true;
                }
            }
        });
        if (editorUseDraft) {
            CKEDITOR[ckeditorField].on('instanceReady', function(event) {
                if (editorDisableDraft) {
                    CKEDITOR[ckeditorField].execCommand('changePublish');
                    CKEDITOR[ckeditorField].execCommand('disableDraft');
                }
                if (editorDisablePublish) {
                    CKEDITOR[ckeditorField].execCommand('changeDraft');
                    CKEDITOR[ckeditorField].execCommand('disablePublish');
                }
                CKEDITOR[ckeditorField].on('beforeCommandExec', function( ev ){
                    if(ev.data.name === 'changePublish' || ev.data.name === 'copyPublish') {
                        $(`#DraftMode${fieldCamelize}`).val('publish');
                    } else if(ev.data.name === 'changeDraft' || ev.data.name === 'copyDraft') {
                        $(`#DraftMode${fieldCamelize}`).val('draft');
                    }
                });
            });
        }
        CKEDITOR[ckeditorField].on('instanceReady', function(event) {
            if(CKEDITOR[ckeditorField].getCommand('maximize').uiItems.length > 0) {
                // ツールバーの表示を切り替え
                CKEDITOR[ckeditorField].getCommand('maximize').on( 'state' ,
                (e) => {
                    if(this.state == 1) {
                        $("#ToolBar").hide();
                    } else {
                        $("#ToolBar").show();
                    }
                });
            }
        });
    });
});
