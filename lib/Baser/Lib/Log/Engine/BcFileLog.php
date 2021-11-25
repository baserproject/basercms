<?php

App::uses('FileLog', 'Log/Engine');

class BcFileLog extends FileLog
{
	/**
	 * @param string $type The type of log you are making.
	 * @param string $message The message you want to log.
	 * @return bool success of write.
	 */
	public function write($type, $message)
	{
		// return parent::write($type, $message);
		$output = sprintf(
			"[%s] %s ---------------------------------------------------------\n%s\n",
			ucfirst($type),
			date('Y-m-d H:i:s'),
			$message
		);
		$filename = $this->_getFilename($type);
		if (!empty($this->_size)) {
			$this->_rotateFile($filename);
		}

		$pathname = $this->_path . $filename;
		if (empty($this->_config['mask'])) {
			return file_put_contents($pathname, $output, FILE_APPEND);
		}

		$exists = is_file($pathname);
		$result = file_put_contents($pathname, $output, FILE_APPEND);
		static $selfError = false;
		if (!$selfError && !$exists && !chmod($pathname, (int)$this->_config['mask'])) {
			$selfError = true;
			trigger_error(
				__d(
					'cake_dev',
					'Could not apply permission mask "%s" on log file "%s"',
					array(
						$this->_config['mask'],
						$pathname
					)
				),
				E_USER_WARNING
			);
			$selfError = false;
		}
		return $result;
	}

	/**
	 * Get filename
	 *
	 * @param string $type The type of log.
	 * @return string File name
	 */
	protected function _getFilename($type)
	{
		if (!empty($this->_file)) {
			return $this->_file;
		}
		return $type . '.log';
	}
}
