/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright Copyright (c) baserCMS Users Community
 * @link http://basercms.net baserCMS Project
 * @since baserCMS v 0.1.0
 * @license http://basercms.net/license/index.html
 */

/**
 * JavaScript 共通関数
 */

/**
 * console.log のラッパー
 *
 * TODO: グローバル汚染 bcTree.js で使用中
 *
 * @param {any} data
 */
function p (data) {
  console.log(data)
}
/**
 * var_dump デバッグ用
 *
 * TODO: グローバル汚染 使用場所不明
 *
 * @param {any} obj
 */
function var_dump (obj) {
  if (typeof obj === 'object') {
    return 'Type: ' + (typeof obj) + ((obj.constructor) ? '\nConstructor: ' + obj.constructor : '') + '\nValue: ' + obj
  } else {
    return 'Type: ' + (typeof obj) + '\nValue: ' + obj
  }
}

/**
 * jQuery向けvar_dump
 *
 * TODO: グローバル汚染 使用場所不明
 *
 * @param {JQuery} $obj jQueryオブジェクト
 */
function jquery_dump ($obj) {
  var dumphtml = []
  var i
  if (jQuery.browser.msie) {
    for (i = 0; i < $obj.length; i++) {
      dumphtml.push('[' + i + '] ')
      dumphtml.push($obj[i].outerHTML.replace(/^[\r\n\t]+/, ''))
      dumphtml.push('\n')
    }
  } else {
    for (i = 0; i < $obj.length; i++) {
      dumphtml.push('[' + i + '] ' + '<' + $obj[i].nodeName.toLowerCase())
      for (var j = 0; j < $obj[i].attributes.length; j++) {
        dumphtml.push(' ' + $obj[i].attributes[j].nodeName + '=' + $obj[i].attributes[j].nodeValue + '')
      }
      dumphtml.push('>' + $obj[i].innerHTML)
      dumphtml.push('</' + $obj[i].nodeName.toLowerCase() + '>')
      dumphtml.push('\n')
    }
  }
  alert(dumphtml.join(''))
}

/**
 * ウインドウをポップアップで開く
 *
 * TODO: グローバル汚染 使用場所不明
 *
 * @param {string} fileName
 * @param {string} windowName
 * @param {number} width
 * @param {number} height
 */
function openWindow (fileName, windowName, width, height) {
  open(fileName, windowName, 'width=' + width + ',height=' + height + ',toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes,resizable=no')
}

/**
 * 閉じると同時に開き元のウインドウをリロードする
 *
 * TODO: グローバル汚染 使用場所不明
 *
 */
function closeAndOpenerReload () {
  opener.location.reload()
  close()
}

/**
 * 別窓でURLを開く
 *
 * TODO: グローバル汚染 使用場所不明
 */
function openUrl (url) {
  open(url, '_top')
}

/**
 * ウィンドウを閉じる
 *
 * TODO: グローバル汚染 使用場所不明
 */
function closeWindow () {
  close()
}

/**
 * 印刷ダイアログを表示する
 *
 * TODO: グローバル汚染 使用場所不明
 */
function printPage () {
  print()
}

/**
 * キャメライズして返す
 *
 * TODO: グローバル汚染 使用場所不明
 *
 * @param {string} str
 * @return {string}
 */
function camelize (str) {
  var parts = str.split('_')
  var len = parts.length
  if (len === 1) return parts[0]
  var camelized = str.charAt(0) === '_' ? parts[0].charAt(0).toUpperCase() + parts[0].substring(1) : parts[0]
  for (var i = 1; i < len; i++) camelized += parts[i].charAt(0).toUpperCase() + parts[i].substring(1)
  return camelized
}

/**
 * 現在の日時を文字列で取得する
 *
 * TODO: グローバル汚染 bcTree.jsで使用
 *
 * @returns {string}
 */
function getNowDateTime () {
  return getNowDate() + ' ' + getNowTime()
}

/**
 * 現在の日付を文字列で取得する
 *
 * TODO: グローバル汚染 当ファイルで使用
 *
 * @returns {string}
 */
function getNowDate () {
  var date = new Date()
  var year = date.getFullYear()
  var month = date.getMonth() + 1
  var day = date.getDate()
  if (month < 10) month = '0' + month
  if (day < 10) day = '0' + day
  return year + '/' + month + '/' + day
}

/**
 * 現在の時間を文字列で取得する
 *
 * TODO: グローバル汚染 当ファイルで使用
 *
 * @returns {string}
 */
function getNowTime () {
  var date = new Date()
  var hour = date.getHours()
  var minute = date.getMinutes()
  if (hour < 10) hour = '0' + hour
  if (minute < 10) minute = '0' + minute
  return hour + ':' + minute
}
