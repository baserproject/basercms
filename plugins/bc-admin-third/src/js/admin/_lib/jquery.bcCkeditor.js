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
 * bcCkeditor プラグイン
 */
(function ($) {
    $.bcCkeditor = {

        /**
         * editor
         */
        editor: {},

        /**
         * 初期化チェックフラグ
         */
        initStatus: false,

        /**
         * スタイル初期化チェックフラグ
         */
        styleInitStatus: false,

        /**
         * エディタを表示する
         * @param config
         */
        show: function (config) {
            this.setUpConfig(config);
            if (!this.initStatus) {
                CKEDITOR.addStylesSet('basercms', config.initialStyle);
                this.initStatus = true;
            }
            if (!this.styleInitStatus && config.editorStyle.length) {
                this.editorStyle.map((editor, key) => CKEDITOR.addStylesSet(key, editor));
                this.styleInitStatus = true;
            }
            config.themeEditorCsses.map((css) => {
                if (Array.isArray(CKEDITOR.config.contentsCss)) {
                    CKEDITOR.config.contentsCss.push(css);
                }
            });
            this.editor[config.ckeditorField] = CKEDITOR.replace(config.editorDomId, config.editorOptions);
            this.setUpDraft(config);
            this.setUpToolBar(config);
        },

        /**
         * 基本設定
         * @param config
         */
        setUpConfig: function (config) {
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.config.extraPlugins = 'draft,showprotected';
            CKEDITOR.config.stylesCombo_stylesSet = config.editorStylesSet;
            CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
            // 空「i」タグを消さないようにする
            CKEDITOR.dtd.$removeEmpty["i"] = false;
            // 空「span」タグを消さないようにする
            CKEDITOR.dtd.$removeEmpty["span"] = false;
            if (config.editorUrl) {
                CKEDITOR.config.templates_files = [config.editorUrl];
            }
            if (config.editorEnterBr) {
                CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
            }
            // 複数入ることを前提に配列型に変更
            if (typeof CKEDITOR.config.contentsCss === 'string') {
                CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss];
            }
        },

        /**
         * 下書き設定
         * @param config
         */
        setUpDraft: function(config)
        {
            if (!config.editorUseDraft) return;
            this.editor[config.ckeditorField].on('pluginsLoaded', function () {
                if (config.editorUseDraft) {
                    if (config.draftAreaId) {
                        this.draftDraftAreaId = config.draftAreaId;
                    }
                    if (config.publishAreaId) {
                        this.draftPublishAreaId = config.publishAreaId;
                    }
                    if (config.editorReadonlyPublish) {
                        this.draftReadOnlyPublish = true;
                    }
                }
            });
            this.editor[config.ckeditorField].on('instanceReady', function () {
                if (config.editorDisableDraft) {
                    this.execCommand('changePublish');
                    this.execCommand('disableDraft');
                }
                if (config.editorDisablePublish) {
                    this.execCommand('changeDraft');
                    this.execCommand('disablePublish');
                }
                this.on('beforeCommandExec', function (e) {
                    if (e.data.name === 'changePublish' || e.data.name === 'copyPublish') {
                        $(`#${config.previewModeId}`).val('default');
                    } else if (e.data.name === 'changeDraft' || e.data.name === 'copyDraft') {
                        $(`#${config.previewModeId}`).val('draft');
                    }
                });
            });
        },

        /**
         * ツールバー設定
         * @param config
         */
        setUpToolBar: function(config)
        {
            this.editor[config.ckeditorField].on('instanceReady', function () {
                if (this.getCommand('maximize').uiItems.length > 0) {
                    // ツールバーの表示を切り替え
                    this.getCommand('maximize').on('state', () => {
                        if (this.state === 1) {
                            $("#ToolBar").hide();
                        } else {
                            $("#ToolBar").show();
                        }
                    });
                }
            });
        }

    };
})(jQuery);
