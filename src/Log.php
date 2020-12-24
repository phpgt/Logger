<?php
namespace Gt\Logger;

class Log {
	public static function info(string $message, array $context = []):void {
		self::log(LogLevel::INFO, $message, $context);
	}

	private static function log(
		string $level,
		string $message,
		array $context = []
	):void {
		foreach(LogConfig::getHandlers() as $handler) {
			$handler->handle($level, $message, $context);
		}
	}
}