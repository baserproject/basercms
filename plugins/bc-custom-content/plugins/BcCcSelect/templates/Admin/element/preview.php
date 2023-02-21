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


<span class="bca-select">
    <select name="preview[BcCcSelect]" id="preview-bcccselect" class="bca-select__select" v-model="entity.default_value">
        <option value=""><?php echo __d('baser', '指定しない') ?></option>
        <option v-for="value in arraySource" :value="value">{{value}}</option>
    </select>
    <br><span v-show="arraySource.length < 1"><?php echo __d('baser', '選択リストを入力してください。') ?></span>
</span>
