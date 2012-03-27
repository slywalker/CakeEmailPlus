<?php
App::uses('AppShell', 'Console/Command');
App::uses('ImapEmail', 'EmailPlus.Network/Email');
App::uses('ForwardEmail', 'EmailPlus.Network/Email');

class SampleForwardingShell extends AppShell {

	public $to = 'someone@example.com';

	public function main() {
		$email = new ForwardEmail;

		$imap = new ImapEmail('forwardImap');

		$results = $imap->search('UNSEEN');

		foreach ($results as $mid) {
			$overview = $imap->fetchOverview($mid);

			$header = $imap->fetchHeader($mid);

			$body = $imap->body($mid);

			$res = $email->reset()
				->config('forwardSmtp')
				->originalHeader($header)
				->originalBody($body)
				->from($overview['to'])
				->to($this->to)
				->subject($overview['subject'])
				->send();
		}
	}

}
