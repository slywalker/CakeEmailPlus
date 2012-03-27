<?php
App::uses('CakeEmail', 'Network/Email');

class ForwardEmail extends CakeEmail {

	protected $_transportName = 'EmailPlus.ForwardSmtp';

	protected $_originalHeader = null;

	protected $_originalBody = null;

	public function originalHeader($string = null) {
		if (is_null($string)) {
			return $this->_originalHeader;
		} else {
			$this->_originalHeader = (string)$string;
			return $this;
		}
	}

	public function originalBody($string = null) {
		if (is_null($string)) {
			return $this->_originalBody;
		} else {
			$this->_originalBody = (string)$string;
			return $this;
		}
	}

	public function getHeaders($include = array()) {
		if ($include == array_values($include)) {
			$include = array_fill_keys($include, true);
		}
		$defaults = array_fill_keys(array('from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'bcc', 'subject'), false);
		$include += $defaults;

		$headers = array();
		$relation = array(
			'from' => 'From',
			'replyTo' => 'Reply-To',
			'readReceipt' => 'Disposition-Notification-To',
			'returnPath' => 'Return-Path'
		);
		foreach ($relation as $var => $header) {
			if ($include[$var]) {
				$var = '_' . $var;
				$headers[$header] = current($this->_formatAddress($this->{$var}));
			}
		}
		if ($include['sender']) {
			if (key($this->_sender) === key($this->_from)) {
				$headers['Sender'] = '';
			} else {
				$headers['Sender'] = current($this->_formatAddress($this->_sender));
			}
		}

		foreach (array('to', 'cc', 'bcc') as $var) {
			if ($include[$var]) {
				$classVar = '_' . $var;
				$headers[ucfirst($var)] = implode(', ', $this->_formatAddress($this->{$classVar}));
			}
		}

		$headers += $this->_headers;
		if (!isset($headers['X-Mailer'])) {
			$headers['X-Mailer'] = self::EMAIL_CLIENT;
		}
		if (!isset($headers['Date'])) {
			$headers['Date'] = date(DATE_RFC2822);
		}
		if ($this->_messageId !== false) {
			if ($this->_messageId === true) {
				$headers['Message-ID'] = '<' . str_replace('-', '', String::UUID()) . '@' . env('HTTP_HOST') . '>';
			} else {
				$headers['Message-ID'] = $this->_messageId;
			}
		}

		if ($include['subject']) {
			$headers['Subject'] = $this->_subject;
		}

		return $headers;
	}

}