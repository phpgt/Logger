<?php
namespace Gt\Logger\LogHandler;

class StdOutHandler extends FileHandler {
	public function __construct() {
		parent::__construct("php://stdout");
	}
}