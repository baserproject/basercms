<?php

App::uses('ErrorHandler', 'Error');

class BcErrorHandler extends ErrorHandler
{
	/**
	 * @param Exception|ParseError $exception The exception to render.
	 * @return void
	 */
	public static function handleException($exception)
	{
		if ($exception->getCode() == 404) {
			CakeLog::write('error404', self::error404msg() . "\n");
		}

		$renderer = Configure::read('Exception.renderer')?: 'ExceptionRenderer';
		if ($renderer !== 'ExceptionRenderer') {
			list($plugin, $renderer) = pluginSplit($renderer, true);
			App::uses($renderer, $plugin . 'Error');
		}
		try {
			$error = new $renderer($exception);
			$error->render();
		} catch (Exception $e) {

			set_error_handler(Configure::read('Error.handler'));
			Configure::write('Error.trace', false);
			$message = sprintf(
				"[%s] %s\n%s",
				get_class($e),
				$e->getMessage(),
				self::readableTrace()
			);
			static::$_bailExceptionRendering = 1;
			trigger_error($message, E_USER_ERROR);
		}
	}

	public static function handleError($code, $description, $file_path = null, $line = null, $context = null)
	{
		if (error_reporting() === 0) {
			return false;
		}
		$errorSource = self::getErrorSource($file_path, $line);
		list($error, $log) = static::mapErrorCode($code);
		if ($log === LOG_ERR) {
			return static::handleFatalError($code, $description, $file_path, $line);
		}

		$debug = Configure::read('debug');
		if ($debug) {
			$option = array(
				'level'       => $log,
				'code'        => $code,
				'error'       => $error,
				'description' => $description,
				'file'        => self::trimPath($file_path),
				'line'        => $line,
				'context'     => $context,
				'start'       => 2,
				'source'      => $errorSource,
				'path'        => self::trimPath($file_path)
			);
			Debugger::getInstance()->outputError($option);
		}
		return CakeLog::write(
			$log,
			self::makeMessage($error, $code, $description, $file_path, $line)
		);
	}

	public static function handleFatalError($error_code, $description, $file_path, $line)
	{
		static $renderException = 1;

		if (!$renderException) {
			return;
		}

		CakeLog::write(
			LOG_ERR,
			self::makeMessage('Fatal Error', $error_code, $description, $file_path, $line)
		);

		$exceptionHandler = Configure::read('Exception.handler');
		if (!is_callable($exceptionHandler)) {
			return false;
		}

		if (ob_get_level()) {
			ob_end_clean();
		}

		if (Configure::read('debug')) {
			$exception = new FatalErrorException($description, 500, $file_path, $line);
		} else {
			$exception = new InternalErrorException();
		}

		$renderException = 0;

		call_user_func($exceptionHandler, $exception);

		return false;
	}

	private static function error404msg()
	{
		$rs = [];
		$rs[] = 'Uri: ' . env('REQUEST_URI');
		if (env('HTTP_REFERER')) {
			$rs[] = 'Referer: ' . env('HTTP_REFERER');
		}
		$hostname = gethostbyaddr(env('REMOTE_ADDR'));
		$rs[] = 'IP: ' . env('REMOTE_ADDR') . (preg_match('@[a-z]+@', $hostname) ? ' by ' . $hostname : '');
		if (env('HTTP_USER_AGENT')) {
			$rs[] = 'ua: ' . env('HTTP_USER_AGENT');
		}
		if (!empty($_POST)) {
			$rs[] = 'method: POST';
		}
		return implode("\n", $rs);
	}

	private static function getErrorSource($file_path, $line)
	{
		if (!is_readable($file_path)) {
			return null;
		}
		$source = file($file_path);
		return trim($source[$line - 1]);
	}

	private static function makeMessage($error, $error_code, $description, $file_path, $line)
	{
		$rs = [];
		$rs[] = 'Description: ' . (self::hasStackTrace($description) ? self::description($description) : $description);
		$rs[] = 'Source: ' . self::getErrorSource($file_path, $line);
		$rs[] = self::hasStackTrace($description) ? self::errorType($description) : sprintf('Error Type: %s[%s]', $error, $error_code);
		if (env('REQUEST_URI')) {
			$rs[] = 'Uri: ' . env('REQUEST_URI');
		}
		$rs[] = 'File: ' . self::trimPath($file_path);
		$rs[] = 'Line: ' . $line;
		if (env('REMOTE_ADDR')) {
			$hostname = gethostbyaddr(env('REMOTE_ADDR'));
			$rs[] = 'IP: ' . env('REMOTE_ADDR') . (preg_match('@[a-z]+@', $hostname) ? ' by ' . $hostname : '');
		}
		if (env('HTTP_USER_AGENT')) {
			$rs[] = 'ua: ' . env('HTTP_USER_AGENT');
		}
		if (!empty($_POST)) {
			$rs[] = 'method: POST';
		}
		if (env('HTTP_REFERER')) {
			$rs[] = 'Referer: ' . env('HTTP_REFERER');
		}

		if (empty(Configure::read('Error.trace'))) {
			return implode("\n", $rs);
		}

		// https://bugs.php.net/bug.php?id=65322
		if (version_compare(PHP_VERSION, '5.4.21', '<')) {
			if (!class_exists('Debugger')) {
				App::load('Debugger');
			}
			if (!class_exists('CakeText')) {
				App::uses('CakeText', 'Utility');
				App::load('CakeText');
			}
		}
		return sprintf(
			"%s\nStack Trace:\n%s\n",
			implode("\n", $rs),
			!self::hasStackTrace($description) ? self::readableTrace() : self::stackTraceFromString($description)
		);
	}

	private static function hasStackTrace($description) {
		if (strpos($description, 'Error:')===false) {
			return false;
		}
		if (strpos($description, 'Stack trace:')===false) {
			return false;
		}
		if (strpos($description, '#0')===false) {
			return false;
		}
		return true;
	}

	private static function description($description) {
		$_ = explode('Stack trace:', $description);
		$_ = explode('Error:', str_replace([ROOT, '\\'], ['', '/'], trim($_[0])));
		return trim($_[1]);
	}

	private static function errorType($description) {
		$_ = explode('Error:', $description);
		return 'Error Type: ' . rtrim($_[0],':');
	}

	private static function stackTraceFromString($description) {
		$_ = explode('Stack trace:', $description);
		$stackTrace = explode("\n", $_[1]);
		$rs = [];
		foreach($stackTrace as $k=>$v) {
			if(substr($v,0,1)!=='#') {
				continue;
			}
			$rs[] = self::trimPath(preg_replace('@^#[0-9]+ @', '', $v));
		}
		return implode("\n", $rs);
	}

	private static function trimPath($path) {

		if (!defined('CAKE_CORE_INCLUDE_PATH') || !defined('APP')) {
			return str_replace('\\', '/', $path);
		}

		if (strpos($path, APP) === 0) {
			return str_replace([APP, '\\'], ['{APP}/', '/'], $path);
		}

		if (strpos($path, CAKE_CORE_INCLUDE_PATH) === 0) {
			return str_replace([CAKE_CORE_INCLUDE_PATH, '\\'], ['{CORE}', '/'], $path);
		}

		if (strpos($path, ROOT) === 0) {
			return str_replace([ROOT, '\\'], ['{ROOT}', '/'], $path);
		}

		return $path;
	}

	private static function readableTrace() {
		$backtrace = debug_backtrace();
		$default = array(
			'line' => '??',
			'file' => '[internal]',
			'class' => null,
			'function' => '[main]'
		);
		foreach($backtrace as $row) {
			if(!empty($row['class']) && $row['class'] === __CLASS__) {
				continue;
			}
			$row = array_merge($default, $row);
			$row['reference'] = 'reference';
			$row['path'] = self::trimPath($row['file']);
			$row['method'] = sprintf(
				'%s%s%s',
				!empty($row['class']) ? $row['class'] : '',
				!empty($row['type']) ? $row['type'] : '',
				!empty($row['function']) ? $row['function'] . '()' : ''
			);
			unset($row['object'], $row['args']);
			$back[] = CakeText::insert(
				'{:path}, line {:line} - {:method}',
				$row,
				array('before' => '{:', 'after' => '}')
			);
		}
		return implode("\n", $back);
	}
}
