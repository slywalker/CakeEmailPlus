<?php
App::uses('CakeEmail', 'Network/Email');

class ImapEmail extends CakeEmail {

	protected $_resource = null;

	protected $_originalHeader = null;

	public function __construct($config = null) {
		parent::__construct($config);
		if ($config) {
			$this->open();
		}
	}

	public function open() {
		$mailbox = vsprintf('{%s:%d%s}%s', array(
			$this->_config['host'],
			$this->_config['port'],
			$this->_config['flags'],
			$this->_config['mailbox'],
		));
		if (!$this->_resource = imap_open($mailbox, $this->_config['username'], $this->_config['password'])) {
			throw new SocketException(__d('cake_dev', 'Imap could not open.'));
		}
		return $this;
	}

	public function __call($name, $arguments) {
		$function = 'imap_' . strtolower($name);
		array_unshift($arguments, $this->_resource);
		return call_user_func_array($function, $arguments);
	}

	public function __destruct() {
		imap_close($this->_resource);
	}

}