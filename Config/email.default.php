<?php
class EmailConfig {

	public $imap = array(
		'transport' => 'Mail',
		'host' => 'imap.gmail.com',
		'port' => 993,
		'flags' => '/imap/ssl',
		'mailbox' => 'INBOX',
		'username' => 'user',
		'password' => 'secret',
	);

}