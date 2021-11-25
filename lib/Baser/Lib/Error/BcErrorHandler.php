<?php

App::uses('ErrorHandler', 'Error');

class BcErrorHandler extends ErrorHandler
{
	/**
	 * Set as the default exception handler by the CakePHP bootstrap process.
	 *
	 * This will either use custom exception renderer class if configured,
	 * or use the default ExceptionRenderer.
	 *
	 * @param Exception|ParseError $exception The exception to render.
	 * @return void
	 * @see http://php.net/manual/en/function.set-exception-handler.php
	 */
	public static function handleException($exception)
	{
		if (Configure::read('Exception.log')) {
			static::log($exception);
		}

		$renderer = Configure::read('Exception.renderer') ?: 'ExceptionRenderer';
		if ($renderer !== 'ExceptionRenderer') {
			list($plugin, $renderer) = pluginSplit($renderer, true);
			App::uses($renderer, $plugin . 'Error');
		}
		try {
			$error = new $renderer($exception);
			$error->render();
		} catch (Exception $e) {
			set_error_handler(Configure::read('Error.handler')); // Should be using configured ErrorHandler
			Configure::write('Error.trace', false); // trace is useless here since it's internal
			$message = sprintf(
				"[%s] %s\n%s", // Keeping same message format
				get_class($e),
				$e->getMessage(),
				$e->getTraceAsString()
			);

			static::$_bailExceptionRendering = true;
			trigger_error($message, E_USER_ERROR);
		}
	}

	/**
	 * Generates a formatted error message
	 *
	 * @param Exception $exception Exception instance
	 * @return string Formatted message
	 */
	protected static function _getMessage($exception)
	{
		$message = sprintf(
			"[%s] %s",
			get_class($exception),
			$exception->getMessage()
		);
		if (method_exists($exception, 'getAttributes')) {
			$attributes = $exception->getAttributes();
			if ($attributes) {
				$message .= "\nException Attributes: " . var_export($exception->getAttributes(), true);
			}
		}
		if (PHP_SAPI !== 'cli') {
			$request = Router::getRequest();
			if ($request) {
				$message .= "\nRequest URL: " . $request->here();
			}
		}
		return $message . "\nStack Trace:\n" . $exception->getTraceAsString();
	}

	/**
	 * Handles exception logging
	 *
	 * @param Exception|ParseError $exception The exception to render.
	 * @param array $config An array of configuration for logging.
	 * @return bool
	 */
	protected static function log($exception)
	{
		$config = Configure::read('Exception');
		if (!empty($config['skipLog'])) {
			foreach ((array)$config['skipLog'] as $class) {
				if ($exception instanceof $class) {
					return false;
				}
			}
		}
		if ($exception->getCode() == 404 ) {
			return CakeLog::write('error404', self::error404msg());
		}
		return CakeLog::write(LOG_ERR, static::_getMessage($exception));
	}

	/**
	 * Set as the default error handler by CakePHP. Use Configure::write('Error.handler', $callback), to use your own
	 * error handling methods. This function will use Debugger to display errors when debug > 0. And
	 * will log errors to CakeLog, when debug == 0.
	 *
	 * You can use Configure::write('Error.level', $value); to set what type of errors will be handled here.
	 * Stack traces for errors can be enabled with Configure::write('Error.trace', true);
	 *
	 * @param int $code Code of error
	 * @param string $description Error description
	 * @param string $file File on which error occurred
	 * @param int $line Line that triggered the error
	 * @param array $context Context
	 * @return bool true if error was handled
	 */
	public static function handleError($code, $description, $file = null, $line = null, $context = null)
	{
		if (error_reporting() === 0) {
			return false;
		}
		list($error_type, $log_level) = static::mapPhpErrorCode($code);
		if ($log_level === LOG_ERR) {
			return static::handleFatalError($code, $description, $file, $line);
		}

		$debug = Configure::read('debug');
		if ($debug) {
			$data = array(
				'level' => $log_level,
				'code' => $code,
				'error' => $error_type,
				'description' => $description,
				'file' => $file,
				'line' => $line,
				'context' => $context,
				'start' => 2,
				'path' => Debugger::trimPath($file)
			);
			return Debugger::getInstance()->outputError($data);
		}
		return CakeLog::write(
			$log_level,
			static::_getErrorMessage(
				$error_type,
				$code,
				$description,
				$file,
				$line
			)
		);
	}

	/**
	 * Generate an error page when some fatal error happens.
	 *
	 * @param int $code Code of error
	 * @param string $description Error description
	 * @param string $file File on which error occurred
	 * @param int $line Line that triggered the error
	 * @return bool
	 * @throws FatalErrorException If the Exception renderer threw an exception during rendering, and debug > 0.
	 * @throws InternalErrorException If the Exception renderer threw an exception during rendering, and debug is 0.
	 */
	public static function handleFatalError($code, $description, $file, $line)
	{
		$logMessage = sprintf("Fatal Error (%d): %s in [%s, line %d]", $code, $description, $file, $line);
		CakeLog::write(LOG_ERR, $logMessage);

		$exceptionHandler = Configure::read('Exception.handler');
		if (!is_callable($exceptionHandler)) {
			return false;
		}

		if (ob_get_level()) {
			ob_end_clean();
		}

		if (Configure::read('debug')) {
			$exception = new FatalErrorException($description, 500, $file, $line);
		} else {
			$exception = new InternalErrorException();
		}

		if (static::$_bailExceptionRendering) {
			static::$_bailExceptionRendering = false;
			throw $exception;
		}

		call_user_func($exceptionHandler, $exception);

		return false;
	}

	/**
	 * Map an error code into an Error word, and log location.
	 *
	 * @param int $code Error code to map
	 * @return array Array of error word, and log location.
	 */
	public static function mapPhpErrorCode($code)
	{
		$error_type = $log_level= null;
		switch ($code) {
			case E_PARSE:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$error_type = 'Fatal Error';
				$log_level = LOG_ERR;
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_COMPILE_WARNING:
			case E_RECOVERABLE_ERROR:
				$error_type = 'Warning';
				$log_level = LOG_WARNING;
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$error_type = 'Notice';
				$log_level = LOG_NOTICE;
				break;
			case E_STRICT:
				$error_type = 'Strict';
				$log_level = LOG_NOTICE;
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$error_type = 'Deprecated';
				$log_level = LOG_NOTICE;
				break;
		}
		return array($error_type, $log_level);
	}

	/**
	 * Generate the string to use to describe the error.
	 *
	 * @param string $error The error type (e.g. "Warning")
	 * @param int $code Code of error
	 * @param string $description Error description
	 * @param string $file File on which error occurred
	 * @param int $line Line that triggered the error
	 * @return string
	 */
	protected static function _getErrorMessage($error, $code, $description, $file, $line)
	{
		$errorConfig = Configure::read('Error');
		$message = sprintf("%s (%d): %s in [%s, line %d]", $error, $code, $description, $file, $line);
		if (!empty($errorConfig['trace'])) {
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
			$trace = Debugger::trace(array('start' => 1, 'format' => 'log'));
			$message .= "\nTrace:\n" . $trace . "\n";
		}
		return $message;
	}

	private static function exceptionToMessage($exception)
	{
		$rs = [];
		$rs[] = sprintf('Description: %s - %s', get_class($exception), $exception->getMessage());
		if (env('REQUEST_URI')) {
			$rs[] = 'Uri: ' . env('REQUEST_URI');
		}
		if (env('REMOTE_ADDR')) {
			$hostname = gethostbyaddr(env('REMOTE_ADDR'));
			$rs[] = sprintf("IP: %s%s", env('REMOTE_ADDR'), preg_match('@[a-z]+@', $hostname) ? ' by ' . $hostname : '');
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
		return implode("\n", $rs);
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
		return implode("\n", $rs)."\n";
	}

	private static function getErrorSource($file_path, $line)
	{
		if (!is_readable($file_path)) {
			return null;
		}
		$source = file($file_path);
		return trim($source[$line - 1]);
	}

	private static function makeMessage($errorType, $errorCode, $description, $file_path, $line)
	{
		$rs = [];
		$rs[] = 'Description: ' . (self::hasStackTrace($description) ? self::description($description) : $description);
		$rs[] = 'Source: ' . self::getErrorSource($file_path, $line);
		$rs[] = self::hasStackTrace($description) ? self::errorType($description) : sprintf('Error Type: %s[%s]', $errorType, $errorCode);
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

	private static function hasStackTrace($description)
	{
		if (strpos($description, 'Error:') === false) {
			return false;
		}
		if (strpos($description, 'Stack trace:') === false) {
			return false;
		}
		if (strpos($description, '#0') === false) {
			return false;
		}
		return true;
	}

	private static function description($description)
	{
		$_ = explode('Stack trace:', $description);
		$_ = explode('Error:', str_replace([ROOT, '\\'], ['', '/'], trim($_[0])));
		return trim($_[1]);
	}

	private static function errorType($description)
	{
		$_ = explode('Error:', $description);
		return 'Error Type: ' . rtrim($_[0], ':');
	}

	private static function stackTraceFromString($description)
	{
		$_ = explode('Stack trace:', $description);
		$stackTrace = explode("\n", $_[1]);
		$rs = [];
		foreach ($stackTrace as $k => $v) {
			if (substr($v, 0, 1) !== '#') {
				continue;
			}
			$rs[] = self::trimPath(preg_replace('@^#[0-9]+ @', '', $v));
		}
		return implode("\n", $rs);
	}

	private static function trimPath($path)
	{

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

	private static function readableTrace($backtrace = null)
	{
		if (!$backtrace) {
			$backtrace = debug_backtrace();
		}
		$default = array(
			'line' => '??',
			'file' => '[internal]',
			'class' => null,
			'function' => '[main]'
		);
		foreach ($backtrace as $row) {
			if (!empty($row['class']) && $row['class'] === __CLASS__) {
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
