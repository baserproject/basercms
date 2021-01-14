<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller.Component
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('EmailComponent', 'Controller/Component');

/**
 * Class BcEmailComponent
 *
 * Email 拡張モデル
 *
 * @package Baser.Controller.Component
 */
class BcEmailComponent extends EmailComponent
{

	/**
	 * プラグイン名
	 * CUSTOMIZE ADD 2011/05/07 ryuring
	 * プラグインのテンプレートを指定できるようにした
	 *
	 * @var string
	 */
	public $plugin = null;

	/**
	 * Send an email using the specified content, template and layout
	 *
	 * @param mixed $content Either an array of text lines, or a string with contents
	 * @param string $template Template to use when sending email
	 * @param string $layout Layout to use to enclose email body
	 * @return    boolean Success
	 * @access    public
	 */
	public function send($content = null, $template = null, $layout = null)
	{
		$this->__createHeader();

		if ($template) {
			$this->template = $template;
		}

		if ($layout) {
			$this->layout = $layout;
		}

		if (is_array($content)) {
			$content = implode("\n", $content) . "\n";
		}

		$message = $this->__wrap($content);

		if ($this->template === null) {
			$message = $this->__formatMessage($message);
		} else {
			$message = $this->__renderTemplate($message);
		}

		// テンプレート内の変数がラップされるように再ラップ
		$message = $this->___wrap($message);

		$message[] = '';

		foreach($message as $key => $line) {
			// 文字コード変換
			$enc = mb_detect_encoding($line);
			// 半角カタカナを全角カタカナに変換
			if (strtolower($this->charset) !== 'jis') {
				$line = mb_convert_kana($line, 'K', $enc);
			}
			$message[$key] = mb_convert_encoding($line, $this->charset, $enc);
		}

		$this->__message = $message;

		if (!empty($this->attachments)) {
			$this->__attachFiles();
		}

		if (!is_null($this->__boundary)) {
			$this->__message[] = '';
			$this->__message[] = '--' . $this->__boundary . '--';
			$this->__message[] = '';
		}

		if ($this->_debug) {
			return $this->__debug();
		}
		$__method = '__' . $this->delivery;
		$sent = $this->$__method();

		$this->__header = [];
		$this->__message = [];

		return $sent;
	}

	/**
	 * Wrap the message using EmailComponent::$lineLength
	 *
	 * @param string $message Message to wrap
	 * @return    string Wrapped message
	 */
	private function __wrap($message)
	{
		$message = $this->__strip($message);

		// MODIFIED 2008/6/22 ryuring
		//$message = str_replace(array("\r\n","\r","\n"), "", $message);
		//$message = str_replace("<br />", "\n", $message);
		// MODIFIED 2008/7/1
		// CakePHPは、PHPの閉じタグの直後の改行を削除する仕様だという事がわかった。
		// メールなど、明示的な改行タグがないものについては、PHP閉じタグの直後に半角スペースなど
		// を挿入する事により、改行が有効となる。よってテンプレート側で対応する事にし、
		// 処理を元にに戻した。
		$message = str_replace(["\r\n", "\r"], "\n", $message);

		$lines = explode("\n", $message);

		return $this->___wrap($lines);
	}

	/**
	 * テンプレートを整形後に再度ラップする必要があるのでラップ処理の部分だけを分離
	 *
	 * @param array $lines
	 * @return array
	 */
	private function ___wrap($lines)
	{
		$formatted = [];
		if ($this->_lineLength !== null) {
			trigger_error('_lineLength cannot be accessed please use lineLength', E_USER_WARNING);
			$this->lineLength = $this->_lineLength;
		}
		foreach($lines as $line) {
			if (substr($line, 0, 1) == '.') {
				$line = '.' . $line;
			}
			$enc = mb_detect_encoding($line);
			$formatted = array_merge($formatted, $this->mbFold($line, $this->lineLength, $enc));
		}
		$formatted[] = '';
		return $formatted;
	}

	/**
	 * Encode the specified string using the current charset
	 *
	 * @param string $subject String to encode
	 * @return    string    Encoded string
	 * @access    private
	 */
	private function __encode($subject)
	{
		$subject = $this->__strip($subject);

		if (strtolower($this->charset) !== 'iso-8859-15') {

			$enc = mb_detect_encoding($subject);
			$_enc = mb_internal_encoding();
			mb_internal_encoding($enc);

			/*
			  $start = "=?" . $this->charset . "?B?";
			  $end = "?=";
			  $spacer = $end . "\n " . $start;

			  $length = 75 - strlen($start) - strlen($end);
			  $length = $length - ($length % 4);

			  $subject = base64_encode($subject);
			  $subject = chunk_split($subject, $length, $spacer);
			  $spacer = preg_quote($spacer);
			  $subject = preg_replace("/" . $spacer . "$/", "", $subject);
			  $subject = $start . $subject . $end;
			 */

			$subject = mb_encode_mimeheader($subject, $this->charset, 'B', Configure::read('BcEmail.lfcode'));

			mb_internal_encoding($_enc);
		}
		return $subject;
	}

	/**
	 * マルチバイト文字を考慮したfolding(折り畳み)処理
	 *
	 * @param mixed $str foldingを行う文字列or文字列の配列
	 *                   文字列に改行が含まれている場合は改行位置でも分割される
	 * @param integer $width 一行の幅(バイト数)。4以上でなければならない
	 * @param string $encoding $strの文字エンコーディング
	 *                         省略した場合は内部文字エンコーディングを使用する
	 * @return array 一行ずつに分けた文字列の配列
	 *
	 * NOTE: いわゆる半角/全角といった見た目ではなく、
	 *       バイト数によって処理が行われるので、文字エンコーディングによって
	 *       結果が変わる可能性がある。
	 *
	 *       例えば半角カナはShift-JISでは1バイトだが、EUC-JPでは2バイトなので、
	 *       $width=10の場合Shift-JISなら10文字だが、EUC-JPでは5文字になる。
	 *
	 *       全角/半角といった見た目で処理をするにはmb_strwidth()を利用した
	 *       実装が必要となる。
	 *
	 * TODO: 日本語禁則処理(Japanese Hyphenation)
	 *       行頭禁則文字は濁点/半濁点の応用でいけるので
	 *       行末禁則文字の処理を加えれば対応できそう
	 *
	 *       ……と思ったけど、禁則文字が$widthを超える分だけ並んでたら
	 *       どうすればいいんだろう
	 *       禁則処理をした結果、桁あふれを起こす場合は禁則処理を無視して
	 *       強制的に$widthで改行する、とか？
	 */
	public function mbFold($str, $width, $encoding = null)
	{
		assert('$width >= 4');

		if (!isset($str)) {
			return null;
		}

		if (!isset($encoding)) {
			$encoding = mb_internal_encoding();
		}

		// 元々の配列も文字列中の改行もとにかく展開してひとつの配列にする
		$strings = [];
		foreach((array)$str as $s) {
			// NOTE: 何故かmb_split()だと改行でうまく分割できない
			//       どうせメジャーなエンコーディングなら制御コードは
			//       leading byteにもtrailing byteにもかぶらないので
			//       preg_split()で良しとする ※JISはアウト
			// NOTE: mb_regex_encoding()を適切に設定してやることで
			//       mb_split()でも正常に分割できるようになったが、
			//       何故かmb_regex_encoding()がJISを受け入れてくれない
			$strings = array_merge($strings, preg_split('/\x0d\x0a|\x0d|\x0a/', $s));
		}

		$lines = [];
		foreach($strings as $string) {
			// 1文字ずつに分解して足していって、
			// バイト数が$widthを超えたら次の行に回す
			$len = mb_strlen($string, $encoding);
			for($i = 0, $line = ''; $i < $len; $i++) {
				$char = mb_substr($string, $i, 1, $encoding);

				// 濁点や半濁点が続いていた場合のいい加減な禁則処理
				// ものすごく日本語依存...
				// TODO: Unicodeの結合文字の判定とかで汎用的に処理したい
				if ($i + 1 < $len) {
					$next = mb_substr($string, $i + 1, 1, $encoding);
					$uc = mb_convert_encoding($next, 'UCS-2', $encoding);
					if (in_array($uc, ["\x30\x99", "\x30\x9B", "\x30\x9C",
						"\xFF\x9E", "\xFF\x9F"])) {
						$char .= $next;
						$i++;
					}
				}

				if (strlen($line . $char) > $width) {
					$lines[] = $line;
					$line = $char;
				} else {
					$line .= $char;
				}
			}
			$lines[] = $line;    // 端数or空行
		}

		return $lines;
	}

	/**
	 * Format a string as an email address
	 *
	 * @param string $string String representing an email address
	 * @return string Email address suitable for email headers or smtp pipe
	 */
	private function __formatAddress($string, $smtp = false)
	{
		$hasAlias = preg_match('/((.*)\s)?<(.+)>/', $string, $matches);
		if ($smtp && $hasAlias) {
			return $this->__strip('<' . $matches[3] . '>');
		} elseif ($smtp) {
			return $this->__strip('<' . $string . '>');
		}
		if ($hasAlias && !empty($matches[2])) {
			// >>> CUSTOMIZE MODIFY 2010/12/06 ryuring
			// 送信者名をエンコード
			//return $this->__strip($matches[2] . ' <' . $matches[3] . '>');
			// ---
			return $this->__strip($this->__encode($matches[2]) . ' <' . $matches[3] . '>');
			// <<<
		}
		return $this->__strip($string);
	}

	/**
	 * Render the contents using the current layout and template.
	 *
	 * @param string $content Content to render
	 * @return array Email ready to be sent
	 */
	private function __renderTemplate($content)
	{
		$viewClass = $this->Controller->viewClass;

		if ($viewClass != 'View') {
			if (strpos($viewClass, '.') !== false) {
				list($plugin, $viewClass) = explode('.', $viewClass);
				$viewClass = $viewClass . 'View';
				App::uses($viewClass, $plugin . '.View');
			} else {
				$viewClass = $viewClass . 'View';
			}
			App::uses($viewClass, 'View');
		}

		$View = new $viewClass($this->Controller);
		$View->layout = $this->layout;
		$msg = [];

		// CUSTOMIZE ADD 2012/04/23 ryuring
		// layoutPath / subDir を指定できるようにした
		// >>>
		$layoutPath = $subDir = '';
		if (!empty($this->layoutPath)) {
			$layoutPath = $this->layoutPath . DS;
		}
		if (!empty($this->subDir)) {
			$subDir = $this->subDir . DS;
		}
		// <<<

		$content = implode("\n", $content);

		if ($this->sendAs === 'both') {
			$htmlContent = $content;
			if (!empty($this->attachments)) {
				$msg[] = '--' . $this->__boundary;
				$msg[] = 'Content-Type: multipart/alternative; boundary="alt-' . $this->__boundary . '"';
				$msg[] = '';
			}
			$msg[] = '--alt-' . $this->__boundary;
			$msg[] = 'Content-Type: text/plain; charset=' . $this->charset;
			$msg[] = 'Content-Transfer-Encoding: 7bit';
			$msg[] = '';

			// CUSTOMIZE MODIRY 2012/04/23 ryuring
			// layoutPath / subDir を指定できるようにした
			// >>>
			//$content = $View->element('email' . DS . 'text' . DS . $this->template, array('content' => $content));
			//$View->layoutPath = 'email' . DS . 'text';
			// ---
			$content = $View->element($subDir . 'Emails' . DS . 'text' . DS . $this->template, ['content' => $content]);
			$View->layoutPath = $layoutPath . 'Emails' . DS . 'text';
			// >>>

			$content = explode("\n", str_replace(["\r\n", "\r"], "\n", $View->renderLayout($content)));
			$msg = array_merge($msg, $content);

			$msg[] = '';
			$msg[] = '--alt-' . $this->__boundary;
			$msg[] = 'Content-Type: text/html; charset=' . $this->charset;
			$msg[] = 'Content-Transfer-Encoding: 7bit';
			$msg[] = '';

			// CUSTOMIZE MODIRY 2012/04/23 ryuring
			// layoutPath / subDir を指定できるようにした
			// >>>
			//$htmlContent = $View->element('email' . DS . 'html' . DS . $this->template, array('content' => $htmlContent));
			//$View->layoutPath = 'email' . DS . 'html';
			// ---
			$htmlContent = $View->element($subDir . 'Emails' . DS . 'html' . DS . $this->template, ['content' => $htmlContent]);
			$View->layoutPath = $layoutPath . 'Emails' . DS . 'html';
			// <<<

			$htmlContent = explode("\n", str_replace(["\r\n", "\r"], "\n", $View->renderLayout($htmlContent)));
			$msg = array_merge($msg, $htmlContent);
			$msg[] = '';
			$msg[] = '--alt-' . $this->__boundary . '--';
			$msg[] = '';

			ClassRegistry::removeObject('view');
			return $msg;
		}

		if (!empty($this->attachments)) {
			if ($this->sendAs === 'html') {
				$msg[] = '';
				$msg[] = '--' . $this->__boundary;
				$msg[] = 'Content-Type: text/html; charset=' . $this->charset;
				$msg[] = 'Content-Transfer-Encoding: 7bit';
				$msg[] = '';
			} else {
				$msg[] = '--' . $this->__boundary;
				$msg[] = 'Content-Type: text/plain; charset=' . $this->charset;
				$msg[] = 'Content-Transfer-Encoding: 7bit';
				$msg[] = '';
			}
		}

		// CUSTOMIZE MODIFY 2011/04/25 ryuring
		// プラグインのテンプレートを指定できるようにした
		// CUSTOMIZE MODIRY 2012/04/23 ryuring
		// layoutPath / subDir を指定できるようにした
		// >>>
		//$content = $View->element('email' . DS . $this->sendAs . DS . $this->template, array('content' => $content));
		//$View->layoutPath = 'email' . DS . $this->sendAs;
		// ---
		if ($this->plugin) {
			$options = ['content' => $content, 'plugin' => $this->plugin];
		} else {
			$options = ['content' => $content];
		}
		$content = $View->element($subDir . 'Emails' . DS . $this->sendAs . DS . $this->template, $options);
		$View->layoutPath = $layoutPath . 'Emails' . DS . $this->sendAs;
		// <<<

		$content = explode("\n", str_replace(["\r\n", "\r"], "\n", $View->renderLayout($content)));
		$msg = array_merge($msg, $content);
		ClassRegistry::removeObject('view');

		return $msg;
	}

}
