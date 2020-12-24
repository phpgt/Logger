<?php
namespace Gt\Logger;

class Log {
	/** @param array<string> $context */
	public static function debug(string $message, array $context = []):void {
		self::log(LogLevel::DEBUG, $message, $context);
	}

	/** @param array<string> $context */
	public static function info(string $message, array $context = []):void {
		self::log(LogLevel::INFO, $message, $context);
	}

	/** @param array<string> $context */
	public static function notice(string $message, array $context = []):void {
		self::log(LogLevel::NOTICE, $message, $context);
	}

	/** @param array<string> $context */
	public static function warning(string $message, array $context = []):void {
		self::log(LogLevel::WARNING, $message, $context);
	}

	/** @param array<string> $context */
	public static function error(string $message, array $context = []):void {
		self::log(LogLevel::ERROR, $message, $context);
	}

	/** @param array<string> $context */
	public static function critical(string $message, array $context = []):void {
		self::log(LogLevel::CRITICAL, $message, $context);
	}

	/** @param array<string> $context */
	public static function alert(string $message, array $context = []):void {
		self::log(LogLevel::ALERT, $message, $context);
	}

	/** @param array<string> $context */
	public static function emergency(string $message, array $context = []):void {
		self::log(LogLevel::EMERGENCY, $message, $context);
	}

	/** @param array<string> $context */
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