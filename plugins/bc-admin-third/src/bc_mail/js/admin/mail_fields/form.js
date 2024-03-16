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
 * mail fields form
 */
const mailFieldsForm = {

    /**
     * Eメールチェックコントロール
     */
    $validEmail: null,

    /**
     * 数値チェックコントロール
     */
    $validNumber: null,

    /**
     * Eメール確認チェックコントロール
     */
    $validEmailConfirm: null,

    /**
     * 日時チェックコントロール
     */
    $validDatetime: null,

    /**
     * ファイルサイズチェックコントロール
     */
    $validMaxFileSize: null,

    /**
     * ファイル拡張子チェックコントロール
     */
    $validFileExt: null,

    /**
     * 全角カタカナチェックコントロール
     */
    $validZenkakuKatakana: null,

    /**
     * 全角ひらがなチェックコントロール
     */
    $validZenkakuHiragana: null,

    /**
     * 正規表現チェックコントロール
     */
    $validRegex: null,

    /**
     * グループ完了チェックコントロール
     */
    $validGroupComplete: null,

    /**
     * 行数コントロール
     */
    $textRows: null,

    /**
     * ソースコントロール
     */
    $source: null,

    /**
     * 最大文字数コントロール
     */
    $maxlength: null,

    /**
     * サイズコントロール
     */
    $size: null,

    /**
     * 自動変換コントロール
     */
    $autoConvert: null,

    /**
     * EメールチェックSPAN
     */
    $spanValidEmail: null,

    /**
     * 数値チェックSPAN
     */
    $spanValidNumber: null,

    /**
     * Eメール確認チェックSPAN
     */
    $spanValidEmailConfirm: null,

    /**
     * 日時チェックSPAN
     */
    $spanValidDatetime: null,

    /**
     * ファイルサイズチェックSPAN
     */
    $spanValidMaxFileSize: null,

    /**
     * ファイル拡張子チェックSPAN
     */
    $spanValidFileExt: null,

    /**
     * 全角カタカナチェックSPAN
     */
    $spanValidZenkakuKatakana: null,

    /**
     * 全角ひらがなチェックSPAN
     */
    $spanValidZenkakuHiragana: null,

    /**
     * 正規表現チェックSPAN
     */
    $spanValidRegex: null,

    /**
     * グループ完了チェックSPAN
     */
    $spanValidGroupComplete: null,

    /**
     * サイズROW
     */
    $rowSize: null,

    /**
     * 行数ROW
     */
    $rowRows: null,

    /**
     * 最大文字数ROW
     */
    $rowMaxlength: null,

    /**
     * ソースROW
     */
    $rowSource: null,

    /**
     * 自動変換ROW
     */
    $rowAutoConvert: null,

    /**
     * Mounted
     */
    mounted() {
        this.$validEmail = $("#valid-ex-valid_email");
        this.$validNumber = $("#valid-ex-valid_number");
        this.$validEmailConfirm = $("#valid-ex-valid_email_confirm");
        this.$validDatetime = $("#valid-ex-valid_datetime");
        this.$validMaxFileSize = $("#valid-ex-valid_max_file_size");
        this.$validFileExt = $("#valid-ex-valid_file_ext");
        this.$validZenkakuKatakana = $("#valid-ex-valid_zenkaku_katakana");
        this.$validZenkakuHiragana = $("#valid-ex-valid_zenkaku_hiragana");
        this.$validRegex = $("#valid-ex-valid_regex");
        this.$validGroupComplete = $("#valid-ex-valid_group_complate");
        this.$textRows = $("#text-rows");
        this.$source = $("#source");
        this.$maxlength = $("#max-length");
        this.$size = $("#size");
        this.$autoConvert = $("#auto-convert");
        this.$spanValidEmail = this.$validEmail.parent();
        this.$spanValidNumber = this.$validNumber.parent();
        this.$spanValidEmailConfirm = this.$validEmailConfirm.parent();
        this.$spanValidDatetime = this.$validDatetime.parent();
        this.$spanValidMaxFileSize = this.$validMaxFileSize.parent();
        this.$spanValidFileExt = this.$validFileExt.parent();
        this.$spanValidZenkakuKatakana = this.$validZenkakuKatakana.parent();
        this.$spanValidZenkakuHiragana = this.$validZenkakuHiragana.parent();
        this.$spanValidRegex = this.$validRegex.parent();
        this.$spanValidGroupComplete = this.$validGroupComplete.parent();
        this.$rowSize = $("#RowSize");
        this.$rowRows = $("#RowRows");
        this.$rowMaxlength = $("#RowMaxlength");
        this.$rowSource = $("#RowSource");
        this.$rowAutoConvert = $("#RowAutoConvert");
        this.initView();
    },

    /**
     * 表示初期化
     */
    initView() {
        this.registerEvents();
        this.loadSetting($("#type").val(), false);
    },

    /**
     * コントロールの初期化
     * @param changed
     */
    initControls(changed) {
        if(changed) {
            this.$validEmail.prop('checked', false);
            this.$validNumber.prop('checked', false);
            this.$validEmailConfirm.prop('checked', false);
            this.$validDatetime.prop('checked', false);
            this.$validMaxFileSize.prop('checked', false);
            this.$validFileExt.prop('checked', false);
            this.$validZenkakuKatakana.prop('checked', false);
            this.$validZenkakuHiragana.prop('checked', false);
            this.$validRegex.prop('checked', false);
            this.$validGroupComplete.prop('checked', false);
        }
        this.$spanValidEmail.hide();
        this.$spanValidNumber.hide();
        this.$spanValidEmailConfirm.hide();
        this.$spanValidDatetime.hide();
        this.$spanValidMaxFileSize.hide();
        this.$spanValidFileExt.hide();
        this.$spanValidZenkakuKatakana.hide();
        this.$spanValidZenkakuHiragana.hide();
        this.$spanValidRegex.hide();
        this.$spanValidGroupComplete.show();
        this.$rowSize.hide();
        this.$rowRows.hide();
        this.$rowMaxlength.hide();
        this.$rowSource.hide();
        this.$rowAutoConvert.hide();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        // タイプを選択すると入力するフィールドが切り替わる
        $("#type").change(function () {
            mailFieldsForm.loadSetting($("#type").val(), true);
        });
        // 項目名を入力時に項目見出しを自動入力
        $("#name").change(function () {
            const $head = $("#head");
            if (!$head.val()) {
                $head.val($("#name").val());
            }
        });
        $("#BtnSave").click(function () {
            $.bcUtil.showLoader();
        });
    },

    /**
     * タイプの値によってフィールドの表示設定を行う
     */
    loadSetting(type, changed) {
        this.initControls(changed);
        switch (type) {
            case 'text':
            case 'number':
            case 'password':
                this.initViewForText();
                break;
            case 'email':
                this.initViewForEmail();
                break;
            case 'tel':
                this.initViewForTel();
                break;
            case 'textarea':
                this.initViewForTextarea();
                break;
            case 'select':
            case 'radio':
            case 'multi_check':
                this.initViewForSelect();
                break;
            case 'date_time_wareki':
            case 'date_time_calender':
                this.initViewForDatetime();
                break;
            case 'file':
                this.initViewForFile();
                break;
            case 'pref':
                this.initViewForPref();
                break;
            case 'autozip':
                this.initViewForAutozip();
                break;
            case 'hidden':
                this.initViewForHidden();
                break;
        }
    },

    /**
     * テキストフィールド用の表示設定
     */
    initViewForText() {
        this.$spanValidEmail.show();
        this.$spanValidNumber.show();
        this.$spanValidDatetime.show();
        this.$spanValidZenkakuKatakana.show();
        this.$spanValidZenkakuHiragana.show();
        this.$spanValidRegex.show();
        this.$rowSize.show();
        this.$rowMaxlength.show();
        this.$rowAutoConvert.show();
        this.$textRows.val('');
        this.$source.val('');
    },

    /**
     * Eメールフィールド用の表示設定
     */
    initViewForEmail() {
        this.$spanValidEmail.show();
        this.$spanValidNumber.show();
        this.$spanValidEmailConfirm.show();
        this.$spanValidRegex.show();
        this.$rowSize.show();
        this.$rowMaxlength.show();
        this.$rowAutoConvert.show();
        this.$textRows.val('');
        this.$source.val('');
    },

    /**
     * 電話番号フィールド用の表示設定
     */
    initViewForTel() {
        this.$spanValidNumber.show();
        this.$rowSize.show();
        this.$rowMaxlength.show();
        this.$rowAutoConvert.show();
        this.$textRows.val('');
        this.$source.val('');
    },

    /**
     * テキストエリアフィールド用の表示設定
     */
    initViewForTextarea() {
        this.$spanValidEmail.show();
        this.$spanValidNumber.show();
        this.$spanValidDatetime.show();
        this.$spanValidZenkakuKatakana.show();
        this.$spanValidZenkakuHiragana.show();
        this.$spanValidRegex.show();
        this.$rowSize.show();
        this.$rowRows.show();
        this.$rowMaxlength.show();
        this.$rowAutoConvert.show();
        this.$maxlength.val('');
        this.$source.val('');
    },

    /**
     * セレクトボックスフィールド用の表示設定
     */
    initViewForSelect() {
        this.$rowSource.show();
        this.$size.val('');
        this.$textRows.val('');
        this.$maxlength.val('');
        this.$autoConvert.val('');
    },

    /**
     * 日時フィールド用の表示設定
     */
    initViewForDatetime() {
        this.$spanValidDatetime.show();
        this.$size.val('');
        this.$textRows.val('');
        this.$maxlength.val('');
        this.$source.val('');
        this.$autoConvert.val('');
    },

    /**
     * ファイルフィールド用の表示設定
     */
    initViewForFile() {
        this.$spanValidMaxFileSize.show();
        this.$spanValidFileExt.show();
        this.$size.val('');
        this.$textRows.val('');
        this.$maxlength.val('');
        this.$source.val('');
        this.$autoConvert.val('');
    },

    /**
     * 都道府県フィールド用の表示設定
     */
    initViewForPref() {
        this.$size.val('');
        this.$textRows.val('');
        this.$maxlength.val('');
        this.$source.val('');
        this.$autoConvert.val('');
    },

    /**
     * 自動変換フィールド用の表示設定
     */
    initViewForAutozip() {
        this.$spanValidRegex.show();
        this.$rowSize.show();
        this.$rowMaxlength.show();
        this.$rowSource.show();
        this.$rowAutoConvert.show();
        this.$textRows.val('');
        this.$autoConvert.val('CONVERT_HANKAKU');
    },

    /**
     * 隠しフィールド用の表示設定
     */
    initViewForHidden() {
        this.$spanValidEmail.show();
        this.$spanValidNumber.show();
        this.$spanValidRegex.show();
    }

}

mailFieldsForm.mounted();
