<?php
App::uses('CakeEmail', 'Network/Email');

class Base64Email extends CakeEmail {

	protected function _renderTemplates($content) {
		$rendered = parent::_renderTemplates($content);
		array_walk($rendered, function(&$val, $key) {
			$val = base64_encode($val);
		});
		return $rendered;
	}

	protected function _getContentTransferEncoding() {
		return 'base64';
	}

}