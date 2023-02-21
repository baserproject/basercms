/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


let vm = new Vue({

    /**
     * Element
     */
    el: '#AdminCustomFieldsForm',

    /**
     * data
     * @returns Object
     */
    data: function () {
        const script = $('#AdminCustomFieldsFormScript');
        return {
            settings: JSON.parse(script.attr('data-setting')),
            entity: JSON.parse(script.attr('data-entity')),
            showRowDefaultValue: false,
            showRowSource: false,
            showRowText: false,
            showRowCheck: false,
            showRowRegex: false,
            showRowPlaceholder: false,
            showControlEmailConfirm: false,
            showControlMaxFileSize: false,
            showControlFileExt: false,
            showControlSize: false,
            showControlLine: false,
            showControlMaxLength: false,
            showControlAutoConvert: false,
            showControlCounter: false,
            showPreview: [],
            sourceMultiCheckbox: [],
            sourceRadio: [],
            sourceSelect: [],
            checkboxLabel: ''
        }
    },

    /**
     * Mounted
     */
    mounted: function () {
        this.initView();
    },

    /**
     * Computed
     */
    computed: {
        /**
         * 配列の選択リストを取得
         *
         * @returns {{length}|*|*[]}
         */
        arraySource: function() {
            if(!this.entity.source) return [];
            let arraySource = this.entity.source.split("\n").map(function(v){
                return v.replace('\r', '');
            });
            if(arraySource.length && arraySource[0] === '') {
                return [];
            }
            return arraySource;
        },

        /**
         * 配列の初期値を取得
         *
         * @returns {string[]}
         */
        arrayDefaultValue: function() {
            if(!this.entity.default_value) return;
            return this.entity.default_value.replace('\r', '').split("\n");
        },

        /**
         * チェックボックスのラベルを取得
         *
         * @todo プラグインへの依存を解決する
         * @returns {string|*}
         */
        checkboxLabel: {
            getter: function () {
                if(this.entity.meta && this.entity.meta.BcCcCheckbox !== undefined) {
                    return this.entity.meta.BcCcCheckbox.label;
                }
                return '';
            },
            setter: function(value) {
                if(this.entity.meta) {
                    if(this.entity.meta.BcCcCheckbox !== undefined) {
                        this.entity.meta.BcCcCheckbox.label = value;
                    } else {
                        this.entity.meta.BcCcCheckbox = {
                            label: value
                        }
                    }
                } else {
                    this.entity.meta = {
                        BcCcCheckbox: {
                            label: value
                        }
                    };
                }
            }
        }
    },

    /**
     * Methods
     */
    methods: {

        /**
         * 表示を初期化する
         */
        initView() {
            if(!this.entity.validate) {
                this.entity.validate = [];
            }
            this.initByType();
            this.initValidateOptionControls();
            $preview = $("#CustomFieldPreview");
            $preview.appendTo('body');
            $(window).on('scroll', function () {
                var bottom = $(document).innerHeight() - $(window).innerHeight();
                if (bottom <= $(window).scrollTop()) {
                    $preview.fadeOut(500);
                } else {
                    if ($preview.css('display') === 'none') {
                        $preview.fadeIn(500);
                    }
                }
            });
        },

        /**
         * 全てのオプション行を非表示にする
         */
        hideAllOptionsRow: function () {
            this.showRowDefaultValue = false;
            this.showRowSource = false;
            this.showRowText = false;
            this.showRowCheck = false;
            this.showRowRegex = false;
            this.showRowPlaceholder = false;
        },
        /**
         * フィールドタイプによって表示を変更する
         */
        initByType: function () {

            const type = $('#type').val();
            let $rowType = $(`#RowMeta${type}`);

            $('.bca-row-meta').hide();

            // プラグインのメタフィールド
            if ($rowType.length) {
                $rowType.show();
            }

            const setting = this.settings[this.entity.type];

            if (setting === undefined) {
                this.hideAllOptionsRow();
                return;
            }

            this.initRow(setting);
            this.initValidateControl(setting);
            this.initPreview(this.entity.type, setting);
        },

        /**
         * 行の表示を初期化する
         */
        initRow: function (setting) {

            // 初期値（デフォルト：表示）
            if (setting.useDefaultValue === undefined || setting.useDefaultValue === true) {
                this.showRowDefaultValue = true;
            } else {
                this.showRowDefaultValue = false;
                this.entity.default_value = '';
            }

            // 選択リスト（デフォルト：非表示）
            if (setting.useSource === true) {
                this.showRowSource = true;
            } else {
                this.showRowSource = false;
                this.entity.source = '';
            }

            // テキスト関連（デフォルト：非表示）
            if (setting.useSize === true || setting.useLines === true ||
                setting.useMaxLength === true || setting.useCounter === true) {
                this.showRowText = true;
            } else {
                this.showRowText = false;
            }

            // サイズ（デフォルト：非表示）
            if (setting.useSize === true) {
                this.showControlSize = true;
            } else {
                this.showControlSize = false;
                this.entity.size = '';
            }
            // ライン（デフォルト：非表示）
            if (setting.useLine === true) {
                this.showControlLine = true;
            } else {
                this.showControlLine = false;
                this.entity.line = '';
            }
            // 最大値（デフォルト：非表示）
            if (setting.useMaxLength === true) {
                this.showControlMaxLength = true;
            } else {
                this.showControlMaxLength = false;
                this.entity.max_length = '';
            }

            // 自動変換（デフォルト：非表示）
            if (setting.useAutoConvert === true) {
                this.showControlAutoConvert = true;
            } else {
                this.showControlAutoConvert = false;
                this.entity.auto_convert = '';
            }

            // カウンター（デフォルト：非表示）
            if (setting.useCounter === true) {
                this.showControlCounter = true;
            } else {
                this.showControlCounter = false;
                this.entity.counter = false;
            }

            // 正規表現（デフォルト：非表示）
            if (setting.useCheckRegex === true) {
                this.showRowRegex = true;
            } else {
                this.showRowRegex = false;
                this.entity.regex = '';
                this.entity.regex_error_message = '';
            }

            // プレースホルダー（デフォルト：非表示）
            if (setting.usePlaceholder === true) {
                this.showRowPlaceholder = true;
            } else {
                this.showRowPlaceholder = false;
                this.entity.placeholder = '';
            }

            // 入力チェック（デフォルト：非表示）
            if (setting.useCheckEmail === true || setting.useCheckEmailConfirm === true ||
                setting.useCheckEmailConfirm === true || setting.useCheckNumber === true ||
                setting.useCheckHankaku === true || setting.useCheckZenkakuKatakana === true ||
                setting.useCheckZenkakuHiragana === true || setting.useCheckDatetime === true ||
                setting.useCheckMaxFileSize === true || setting.useCheckFileExt === true) {
                this.showRowCheck = true;
            } else {
                this.showRowCheck = false;
            }
        },

        /**
         * プレビューを初期化する
         *
         * @param type
         * @param setting
         */
        initPreview: function (type, setting) {
            this.showPreview['NonSupport'] = false;
            Object.keys(this.settings).forEach(function (key) {
                this.showPreview[key] = false;
            }, this);
            if (setting.preview) {
                this.showPreview[type] = true;
            } else {
                this.showPreview['NonSupport'] = true;
            }
        },

        /**
         * 入力チェックフィールドの指定した値のチェックを外す
         * @param target
         */
        uncheckValidateControl: function (target) {
            if (!this.entity.validate) return;
            const index = this.entity.validate.indexOf(target);
            if(index !== -1) {
                this.entity.validate.splice(index, 1);
            }
        },

        /**
         * 入力チェックフィールドを初期化する
         *
         * 複数チェックボックスの options をコントロールするにあたり、
         * 時間的な問題でjQueryのままで一旦実装。
         *
         * @param setting
         */
        initValidateControl(setting) {
            // Eメールチェック（デフォルト：非表示）
            if (setting.useCheckEmail === true) {
                $("#validate-email").parent().show();
            } else {
                $("#validate-email").parent().hide();
                this.uncheckValidateControl('EMAIL');
            }

            // Eメール確認チェック（デフォルト：非表示）
            if (setting.useCheckEmailConfirm === true) {
                $("#validate-email_confirm").parent().show();
            } else {
                $("#validate-email_confirm").parent().hide();
                this.uncheckValidateControl('EMAIL_CONFIRM');
            }

            // 数値チェック（デフォルト：非表示）
            if (setting.useCheckNumber === true) {
                $("#validate-number").parent().show();
            } else {
                $("#validate-number").parent().hide();
                this.uncheckValidateControl('NUMBER');
            }

            // 半角英数チェック（デフォルト：非表示）
            if (setting.useCheckHankaku === true) {
                $("#validate-hankaku").parent().show();
            } else {
                $("#validate-hankaku").parent().hide();
                this.uncheckValidateControl('HANKAKU');
            }

            // 全角カタカナチェック（デフォルト：非表示）
            if (setting.useCheckZenkakuKatakana === true) {
                $("#validate-zenkaku_katakana").parent().show();
            } else {
                $("#validate-zenkaku_katakana").parent().hide();
                this.uncheckValidateControl('ZENKAKU_KATAKANA');
            }

            // 全角ひらがなチェック（デフォルト：非表示）
            if (setting.useCheckZenkakuHiragana === true) {
                $("#validate-zenkaku_hiragana").parent().show();
            } else {
                $("#validate-zenkaku_hiragana").parent().hide();
                this.uncheckValidateControl('ZENKAKU_HIRAGANA');
            }

            // 日付チェック（デフォルト：非表示）
            if (setting.useCheckDatetime === true) {
                $("#validate-datetime").parent().show();
            } else {
                $("#validate-datetime").parent().hide();
                this.uncheckValidateControl('DATETIME');
            }

            // ファイルアップロードサイズ制限（デフォルト：非表示）
            if (setting.useCheckMaxFileSize === true) {
                $("#validate-max_file_size").parent().show();
            } else {
                $("#validate-max_file_size").parent().hide();
                this.uncheckValidateControl('MAX_FILE_SIZE');
            }

            // ファイル拡張子チェック（デフォルト：非表示）
            if (setting.useCheckFileExt === true) {
                $("#validate-file_ext").parent().show();
            } else {
                $("#validate-file_ext").parent().hide();
                this.uncheckValidateControl('FILE_EXT');
            }
        },

        /**
         * 入力チェックのオプションのコントロールを初期化する
         */
        initValidateOptionControls() {
            this.initEmailConfirm();
            this.initMaxFileSize();
            this.initFileExt();
        },

        /**
         * Eメール比較先フィールド名の表示を切り替える
         */
        initEmailConfirm: function () {
            if(this.entity.validate === null || this.entity.validate === undefined) return;
            if (this.entity.validate.includes('EMAIL_CONFIRM')) {
                this.showControlEmailConfirm = true;
            } else {
                this.showControlEmailConfirm = false;
                if(this.entity.meta && this.entity.meta.BcCustomContent !== undefined) {
                    this.entity.meta.BcCustomContent.email_confirm = '';
                }
            }
        },

        /**
         * ファイルアップロードサイズ上限の表示を切り替える
         */
        initMaxFileSize: function () {
            if(this.entity.validate === null || this.entity.validate === undefined) return;
            if (this.entity.validate.includes('MAX_FILE_SIZE')) {
                $('#ControlMaxFileSize').show();
                this.showControlMaxFileSize = true;
            } else {
                this.showControlMaxFileSize = false;
                if(this.entity.meta && this.entity.meta.BcCustomContent !== undefined) {
                    this.entity.meta.BcCustomContent.max_file_size = '';
                }
            }
        },

        /**
         * アップロードを許可する拡張子の表示を切り替える
         */
        initFileExt: function () {
            if(this.entity.validate === null || this.entity.validate === undefined) return;
            if (this.entity.validate.includes('FILE_EXT')) {
                this.showControlFileExt = true;
            } else {
                this.showControlFileExt = false;
                if(this.entity.meta && this.entity.meta.BcCustomContent !== undefined) {
                    this.entity.meta.BcCustomContent.file_ext = '';
                }
            }
        }
    }
});
