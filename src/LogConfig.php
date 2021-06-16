<?php
namespace Gt\Logger;

use Gt\Logger\LogHandler\LogHandler;
use Gt\Logger\LogHandler\StdOutHandler;

class LogConfig {
	/** @var array<LogHandler> */
	private static array $handlers = [];
	/** @var array<string> */
	private static array $handlerLevels = [];

	private static StdOutHandler $defaultHandler;
	private static string $defaultHandlerLevel = LogLevel::DEBUG;

	/** @return LogHandler[] */
	public static function getHandlers(
		string $logLevel = LogLevel::DEBUG
	):array {
		self::ensureAtLeastOneHandler();
		$logLevel = strtoupper($logLevel);
		$logLevelIndex = array_flip(LogLevel::ALL_LEVELS)[$logLevel];

		$handlerArray = [];
		foreach(self::$handlers as $i => $handler) {
			$handlerLevel = strtoupper(self::$handlerLevels[$i]);
			$handlerLevelIndex = array_flip(LogLevel::ALL_LEVELS)[$handlerLevel];

			if($logLevelIndex > $handlerLevelIndex) {
				continue;
			}

			array_push($handlerArray, $handler);
		}

		return $handlerArray;
	}

	public static function getDefaultHandler():StdOutHandler {
		if(!isset(self::$defaultHandler)) {
			self::$defaultHandler = new StdOutHandler();
		}

		return self::$defaultHandler;
	}

	public static function addHandler(LogHandler $handler, string $logLevel = null):void {
		array_push(self::$handlers, $handler);
		array_push(self::$handlerLevels, $logLevel ?? self::$defaultHandlerLevel);
	}

	private static function ensureAtLeastOneHandler():void {
		if(empty(self::$handlers)) {
			self::$handlers = array();
			array_push(
				self::$handlers,
				self::getDefaultHandler()
			);
			self::$handlerLevels = array();
			array_push(
				self::$handlerLevels,
				self::$defaultHandlerLevel
			);
		}
	}
}
