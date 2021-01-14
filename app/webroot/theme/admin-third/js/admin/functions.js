/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Javascript 共通関数
 */

/**
 * console.log のラッパー
 *
 * @param mixed data
 * @returns void
 */
function p(data) {
    console.log(data);
}

/**
 * var_dump デバッグ用
 * @return
 */
function var_dump(obj) {
    if (typeof obj == "object") {
        return "Type: " + typeof (obj) + ((obj.constructor) ? "\nConstructor: " + obj.constructor : "") + "\nValue: " + obj;
    } else {
        return "Type: " + typeof (obj) + "\nValue: " + obj;
    }
}

/**
 * jQuery向けvar_dump
 * @param  object   jQueryオブジェクト
 * @return void
 */
function jquery_dump($obj) {
    var dumphtml = [];
    if (jQuery.browser.msie) {
        for (var i = 0; i < $obj.length; i++) {
            dumphtml.push('[' + i + '] ');
            dumphtml.push($obj[i].outerHTML.replace(/^[\r\n\t]+/, ''));
            dumphtml.push("\n");
        }
    } else {
        for (var i = 0; i < $obj.length; i++) {
            dumphtml.push('[' + i + '] '
                + '<' + $obj[i].nodeName.toLowerCase());
            for (var j = 0; j < $obj[i].attributes.length; j++) {
                dumphtml.push(' ' + $obj[i].attributes[j].nodeName + '="'
                    + $obj[i].attributes[j].nodeValue + '"');
            }
            dumphtml.push('>' + $obj[i].innerHTML);
            dumphtml.push('<\/' + $obj[i].nodeName.toLowerCase() + '>');
            dumphtml.push("\n");
        }
    }
    alert(dumphtml.join(''));
}

/**
 * ウインドウをポップアップで開く
 * @return void
 */
function openWindow(FileName, WindowName, Wsize, Hsize) {

    window.open(FileName, WindowName, "width=" + Wsize + ",height=" + Hsize + ",toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes,resizable=no");

}

/**
 * 閉じると同時に開き元のウインドウをリロードする
 * @return void
 */
function closeAndOpenerReload() {

    opener.location.reload();
    window.close();

}

/**
 * 別窓でURLを開く
 * @return void
 */
function openUrl(url) {
    window.open(url, "_top");
}

/**
 * ウィンドウを閉じる
 * @return void
 */
function closeWindow() {
    window.close();
}

/**
 * 印刷ダイアログを表示する
 * @return void
 */
function printPage() {
    if (document.getElementById || document.layers) {
        window.print();		//印刷をします
    }
}

/**
 * キャメライズして返す
 * @return string
 */
function camelize(string) {
    var parts = string.split('_'), len = parts.length;
    if (len == 1) return parts[0];
    var camelized = string.charAt(0) == '_'
        ? parts[0].charAt(0).toUpperCase() + parts[0].substring(1)
        : parts[0];

    for (var i = 1; i < len; i++)
        camelized += parts[i].charAt(0).toUpperCase() + parts[i].substring(1);
    return camelized;
}

/**
 * 現在の日時を文字列で取得する
 *
 * @returns {string}
 */
function getNowDateTime() {
    return getNowDate() + ' ' + getNowTime();
}

/**
 * 現在の日付を文字列で取得する
 *
 * @returns {string}
 */
function getNowDate() {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    if (month < 10) month = '0' + month;
    if (day < 10) day = '0' + day;
    return year + '/' + month + '/' + day;
}

/**
 * 現在の時間を文字列で取得する
 *
 * @returns {string}
 */
function getNowTime() {
    var date = new Date();
    var hour = date.getHours();
    var minute = date.getMinutes();
    if (hour < 10) hour = '0' + hour;
    if (minute < 10) minute = '0' + minute;
    return hour + ':' + minute;
}

/**
 * String 拡張
 * sprintf の文字列置き換えのみ対応
 *
 * @returns {string}
 */
String.prototype.sprintf = function () {
    var str = this + '';
    var args = Array.prototype.slice.call(arguments);

    var ph = true;
    if (str.indexOf('%s', 0) != -1) {
        ph = false;
    }

    if (args.length === 1) {
        if (ph) {
            return str.replace(/%1$s/g, args[0]);
        } else {
            return str.replace(/%s/g, args[0]);
        }
    } else {
        for (var i = 0; i < args.length; i++) {
            var n = i + 1;
            if (ph) {
                str = str.replace('%' + n + '$s', args[i]);
            } else {
                str = str.replace('%s', args[i]);
            }
        }
    }
    return str;
};

