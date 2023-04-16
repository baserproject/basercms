<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
?>

<span class="bca-radio">
    <span class="bca-radio-group">
        <span class="bca-radio" v-for="value in arraySource">
            <input type="radio" name="preview[BcCcRadio]" :value="value" :id="'preview-bcccradio-' + value" class="bca-radio__input" v-model="entity.default_value">
            <label :for="'preview-bcccradio-' + value" class="bca-radio__label">{{value}}&nbsp;</label>
        </span>
        <span v-show="arraySource.length < 1">選択リストを入力してください。</span>
    </span>
</span>
