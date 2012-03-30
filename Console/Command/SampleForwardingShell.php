<?php
App::uses('AppShell', 'Console/Command');
App::uses('ImapEmail', 'EmailPlus.Network/Email');
App::uses('ForwardEmail', 'EmailPlus.Network/Email');

class SampleForwardingShell extends AppShell {

	public $to = 'someone@example.com';

	public function main() {
		$email = new ForwardEmail;

		$imap = new ImapEmail('forwardImap');

		$results = ($imap->search('UNSEEN')) ?: array();

		foreach ($results as $mid) {
			$headerInfo = $imap->headerInfo($mid);
			$objTo = current($headerInfo->to);
			$to = $objTo->mailbox . '@' . $objTo->host;
			$subject = mb_decode_mimeheader($headerInfo->subject);

			$header = $imap->fetchHeader($mid);
			$body = $imap->body($mid);

			$res = $email->reset()
				->config('forwardSmtp')
				->originalHeader($header)
				->originalBody($body)
				->from($to)
				->to($this->to)
				->subject($subject)
				->send();
		}
	}

}
