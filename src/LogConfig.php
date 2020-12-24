<?php
namespace Gt\Logger;

use Gt\Logger\LogHandler\LogHandler;
use Gt\Logger\LogHandler\StdOutHandler;

class LogConfig {
	/** @var LogHandler[] */
	private static array $handlers;
	private static StdOutHandler $defaultHandler;

	/** @return LogHandler[] */
	public static function getHandlers():array {
		self::ensureAtLeastOneHandler();
		return self::$handlers;
	}

	public static function getDefaultHandler():StdOutHandler {
		if(!isset(self::$defaultHandler)) {
			self::$defaultHandler = new StdOutHandler();
		}

		return self::$defaultHandler;
	}

	private static function ensureAtLeastOneHandler():void {
		if(!isset(self::$handlers)) {
			self::$handlers = array();
			array_push(
				self::$handlers,
				self::getDefaultHandler()
			);
		}
	}
}