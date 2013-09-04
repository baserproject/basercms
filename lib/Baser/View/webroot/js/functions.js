/* SVN FILE: $Id$ */
/**
 * Javascript 共通関数ライブラリ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * var_dump デバッグ用
 * @return
 */
	function var_dump(obj) {
		if(typeof obj == "object") {
			return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
		} else {
			return "Type: "+typeof(obj)+"\nValue: "+obj;
		}
	}
/**
 * jQuery向けvar_dump
 * @param  object   jQueryオブジェクト
 * @return void
 */
	function jquery_dump($obj) {
		var dumphtml = [];
		if(jQuery.browser.msie) {
			for(var i = 0; i < $obj.length; i++) {
				dumphtml.push('[' + i + '] ');
				dumphtml.push($obj[i].outerHTML.replace(/^[\r\n\t]+/, ''));
				dumphtml.push("\n");
			}
		} else {
			for(var i = 0; i < $obj.length; i++) {
				dumphtml.push('[' + i + '] '
					+ '<' + $obj[i].nodeName.toLowerCase());
				for(var j = 0; j < $obj[i].attributes.length; j++) {
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
	function openWindow(FileName,WindowName,Wsize,Hsize){

		window.open(FileName,WindowName,"width=" + Wsize + ",height=" + Hsize + ",toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes,resizable=no");

	}
/**
 * 閉じると同時に開き元のウインドウをリロードする
 * @return void
 */
	function closeAndOpenerReload(){

		opener.location.reload();
		window.close();

	}
/**
 * 別窓でURLを開く
 * @return void
 */
	function openUrl(url){
		window.open(url,"_top");
	}
/**
 * ウィンドウを閉じる
 * @return void
 */
	function closeWindow()
	{
		window.close();
	}
/**
 * 印刷ダイアログを表示する
 * @return void
 */
	function printPage(){
		if(document.getElementById || document.layers){
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