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


<span class="bca-checkbox-group">
    <span class="bca-checkbox" v-for="value in arraySource">
        <input
            type="checkbox"
            name="preview[BcCcMultiple][]"
            :value="value"
            :id="'text-' + value"
            class="bca-checkbox__input"
            v-model="multipleDefaultValue">
        <label class="bca-checkbox__label" :for="'text-' + value">{{ value }}</label>
    </span>
    <span v-show="arraySource.length < 1">選択リストを入力してください。</span>
</span>
