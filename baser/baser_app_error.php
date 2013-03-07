<?php
/* SVN FILE: $Id$ */
/**
 * ErrorHandler 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
if(!class_exists('ErrorHandler')) {
	App::import('Core', 'Error');
}
/**
 * ErrorHandler 拡張クラス
 * @package baser
 */
class BaserAppError extends ErrorHandler {
/**
 * Class constructor.
 *
 * @param string $method Method producing the error
 * @param array $messages Error messages
 */
	function __construct($method, $messages) {
		App::import('Core', 'Sanitize');
		static $__previousError = null;

		if ($__previousError != array($method, $messages)) {
			$__previousError = array($method, $messages);
			$this->controller =& new CakeErrorController();
		} else {
			$this->controller =& new Controller();
			$this->controller->viewPath = 'errors';
		}

		$options = array('escape' => false);
		$messages = Sanitize::clean($messages, $options);

		if (!isset($messages[0])) {
			$messages = array($messages);
		}

		if (method_exists($this->controller, 'apperror')) {
			return $this->controller->appError($method, $messages);
		}

		if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($this)))) {
			$method = 'error';
		}

		if ($method !== 'error') {
			if (Configure::read() == 0) {
				$method = 'error404';
				if (isset($code) && $code == 500) {
					$method = 'error500';
				}
			}
		}
		
		// >>> CUSTOMIZE MODIFY 2011/08/19 ryuring
		//$this->dispatchMethod($method, $messages);
		//$this->_stop();
		// ---
		if(!isset($this->controller->params['return'])) {
			$this->dispatchMethod($method, $messages);
			$this->_stop();
		} else {
			return;
		}
		// <<<
		
	}
/**
 * クラスが見つからない
 * @param array $params
 */
	function missingClass($params) {
		if($params['className']) {
			$this->controller->set('className',$params['className']);
		}
		if($params['notice']) {
			$this->controller->set('notice', $params['notice']);
		}
		$this->_outputMessage('missing_class');
	}
/**
 * Renders the Missing Layout web page.
 *
 * @param array $params Parameters for controller
 * @access public
 */
	function missingLayout($params) {
		extract($params, EXTR_OVERWRITE);
		
		$this->controller->layout = 'default';
		
		// >>> CUSTOMIZE ADD 2011/09/23 ryuring
		$this->controller->layoutPath = '';
		$this->controller->subDir = '';
		// <<<
		
		$this->controller->set(array(
			'file' => $file,
			'title' => __('Missing Layout', true)
		));
		$this->_outputMessage('missingLayout');
	}
}
