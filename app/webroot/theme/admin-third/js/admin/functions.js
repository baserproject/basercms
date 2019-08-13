/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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
		if ( month < 10 ) month = '0' + month;
		if ( day < 10 ) day = '0' + day;
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
		if ( hour < 10 ) hour = '0' + hour;
		if ( minute < 10 ) minute = '0' + minute;
		return hour + ':' + minute;
	}

/**
 * String 拡張
 * sprintf の文字列置き換えのみ対応
 *
 * @returns {string}
 */
	String.prototype.sprintf = function() {
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
			for (var i=0; i<args.length; i++) {
				var n = i + 1;
				if (ph) {
					str = str.replace('%'+n+'$s', args[i]);
				} else {
					str = str.replace('%s', args[i]);
				}
			}
		}
		return str;
	};


  /**
   * クリップボードにURLをコピーする
   *
   * @returns false
   */
$(function(){
  var fullUrl = $.bcUtil.frontFullUrl;
  $("input[type=text]").each(function(){
    $(this).keypress(function(e){
      if(e.which && e.which === 13) {
        return false;
      }
      return true;
    });
  });

  if (!document.queryCommandSupported('copy')) {
    $("#BtnCopyUrl").hide();
  } else if(fullUrl) {
	  // URLコピー： クリック後にツールチップの表示内容を切替え
	  $("#BtnCopyUrl").on({
		  'click': function () {
			  var copyArea = $("<textarea style=\" opacity:0; width:1px; height:1px; margin:0; padding:0; border-style: none;\"/>");
			  copyArea.text(fullUrl);
			  $(this).after(copyArea);
			  copyArea.select();
			  document.execCommand("copy");
			  copyArea.remove();

			  // コピー完了のツールチップ表示 bootstrap tooltip
			  $("#BtnCopyUrl").tooltip('dispose'); // 一度削除
			  $("#BtnCopyUrl").tooltip({title: 'コピーしました'});
			  $("#BtnCopyUrl").tooltip('show');
			  return false;
		  },
		  'mouseenter': function () {
			  // console.log('マウス ホバー');
			  $("#BtnCopyUrl").tooltip('dispose'); // 一度削除
			  $("#BtnCopyUrl").tooltip({title: '公開URLをコピー'});
			  $("#BtnCopyUrl").tooltip('show');
		  },
		  'mouseleave': function () {
			  // console.log('マウス アウト');
			  $("#BtnCopyUrl").tooltip('hide');
		  }
	  });
  }
});


/**
 * collapse　オプション、詳細設定の折りたたみ開閉
 *
 * @returns false
 */
$(function(){
  // URLコピー： クリック後にツールチップの表示内容を切替え
  $("[data-bca-collapse='collapse']").on({
    'click': function() {
      const target = $(this).attr('data-bca-target');
      // data-bca-state属性でtoggle
      if($(target).attr('data-bca-state') == 'open') {
        // 対象ID要素:非表示
        $(target).attr('data-bca-state','').slideUp();
        // ボタンの制御
        $(this).attr('data-bca-state','').attr('aria-expanded','true');
      } else {
        // 対象ID要素:表示
        $(target).attr('data-bca-state','open').slideDown();
        // ボタンの制御
        $(this).attr('data-bca-state','open').attr('aria-expanded','false');
      }
      return false;
    }
  });
  $("[data-bca-collapse='favorite-collapse']").on({
    'click': function() {
      const target = $(this).attr('data-bca-target');
      changeOpenFavorite('#btn-favorite-expand', target);
      initFavorite('#btn-favorite-expand', target);
      return false;
    }
  });
  function initFavorite(button, target) {
      if($(button).attr('data-bca-state') == 'open') {
        $(target).show();
      } else {
        $(target).hide();
      }
  }
  function changeOpenFavorite(button, target) {
      if($(button).attr('data-bca-state') == 'open') {
        // ボタンの制御
        $(button).attr('data-bca-state','').attr('aria-expanded','true');
        $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html()+'/'});
      } else {
        // ボタンの制御
        $(button).attr('data-bca-state','open').attr('aria-expanded','false');
        $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html()+'/1'});
      }
  }
  initFavorite('#btn-favorite-expand', '#favoriteBody');
});