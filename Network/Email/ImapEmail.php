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

	public function search($criteria = 'UNSEEN') {
		$results = (imap_search($this->_resource, $criteria)) ?: array();
		return $results;
	}

	public function fetchOverview($messageId) {
		$overview = current(imap_fetch_overview($this->_resource, $messageId));
		$from = current(imap_rfc822_parse_adrlist($overview->from, null));
		$to = current(imap_rfc822_parse_adrlist($overview->to, null));
		return array(
			'from' => $from->mailbox . '@' . $from->host,
			'to' => $to->mailbox . '@' . $to->host,
			'subject' => mb_decode_mimeheader($overview->subject),
			'date' => $overview->date,
		);
	}

	public function fetchHeader($messageId) {
		return imap_fetchheader($this->_resource, $messageId);
	}

	public function body($messageId) {
		return imap_body($this->_resource, $messageId);
	}

	public function setFlag($messageId, $flag = '\Seen') {
		return imap_setflag_full($this->_resource, $messageId, $flag);
	}

	public function __destruct() {
		imap_close($this->_resource);
	}

}