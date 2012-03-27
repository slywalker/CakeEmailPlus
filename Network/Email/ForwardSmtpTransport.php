<?php
App::uses('SmtpTransport', 'Network/Email');

class ForwardSmtpTransport extends SmtpTransport {

	public function send(CakeEmail $email) {
		$this->_cakeEmail = $email;

		$this->_connect();
		$this->_auth();
		$this->_sendRcpt();
		$this->_sendForwordData();
		$this->_disconnect();

		return $this->_content;
	}

	protected function _sendForwordData() {
		$this->_smtpSend('DATA', '354');

		$headers = $this->_cakeEmail->getHeaders(array('from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'subject'));
		$headers = $this->_headersToString($headers);

		$originalHeader = $this->_cakeEmail->originalHeader();
		$originalHeader = preg_replace(array(
			'/^(To)/im',
			'/^(Cc)/im',
			'/^(From)/im',
			'/^(Date)/im',
			'/^(Sender)/im',
			'/^(Subject)/im',
			'/^(Reply-To)/im',
			'/^(X-Mailer)/im',
			'/^(Message-ID)/im',
			'/^(Return-Path)/im',
			'/^(Disposition-Notification-To)/im',
		), 'Original-$1', $originalHeader);
		$headers .= "\r\n" . $originalHeader;

		$message = $this->_cakeEmail->originalBody();

		$this->_smtpSend($headers . $message . ".");
		$this->_content = array('headers' => $headers, 'message' => $message);
	}
}