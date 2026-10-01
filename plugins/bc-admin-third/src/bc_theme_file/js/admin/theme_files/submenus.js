/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {
    // 選択中プラグインの見出しの実DOMインデックスを active に渡す。
    // data-id（$k - 1）の算術は、先頭(-1)やスキップ描画でDOM順とズレ、
    // 選択中が無いと NaN になって正しく開かないため使用しない。
    var $menu = $('#ThemeFilesMenu');
    var $selected = $menu.find('.selected-plugin');
    var active = $selected.length
        ? $menu.children('.bca-main__submenu-title').index($selected)
        : false;
    $menu.accordion({
        collapsible: true,
        heightStyle: "content",
        active: active
    });
});

