<?php
/**
 * bootstrap
 *
 * Webページの呼出時、テーマを読み込む前に実行したいプログラムを書きたい場合には、ここに記述します。
 */

/**
 * baserCMS v3.0.7 では、ユーザーエージェント判定による、スマートフォンのオートリダイレクト機能が有効になっている場合
 * スマートフォンでウィジェットが表示できない不具合がある為、トップページ以外へのリクエストの場合、オートリダイレクト機能をオフに設定
 */
if(Configure::read('BcRequest.pureUrl') != '') {
	Configure::write('BcAgent.smartphone.autoRedirect', false);
}
