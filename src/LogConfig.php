<?php
namespace Gt\Logger;

use Gt\Logger\LogHandler\LogHandler;
use Gt\Logger\LogHandler\StdOutHandler;

class LogConfig {
	/** @var array<LogHandler> */
	private static array $handlers = [];
	/** @var array<string> */
	private static array $handlerMinLevels = [];
	/** @var array<string|null> */
	private static array $handlerMaxLevels = [];

	private static StdOutHandler $defaultHandler;
	private static string $defaultHandlerLevel = LogLevel::DEBUG;

	/** @return LogHandler[] */
	public static function getHandlers(string $minimumLogLevel):array {
		self::ensureAtLeastOneHandler();
		$minimumLogLevel = strtoupper($minimumLogLevel);
		$minimumLogLevelIndex = self::getLogLevelIndex($minimumLogLevel);

		if($minimumLogLevelIndex === null) {
			return [];
		}

		$handlerArray = [];
		foreach(self::$handlers as $i => $handler) {
			$handlerMinLevel = strtoupper(self::$handlerMinLevels[$i]);
			$handlerMinLevelIndex = self::getLogLevelIndex($handlerMinLevel);
			$handlerMaxLevel = self::$handlerMaxLevels[$i];
			$handlerMaxLevelIndex = $handlerMaxLevel
				? self::getLogLevelIndex(strtoupper($handlerMaxLevel))
				: null;

			if($handlerMinLevelIndex === null || $minimumLogLevelIndex < $handlerMinLevelIndex) {
				continue;
			}

			if($handlerMaxLevelIndex !== null && $minimumLogLevelIndex > $handlerMaxLevelIndex) {
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

	public static function setDefaultHandlerLevel(string $level):void {
		self::$defaultHandlerLevel = $level;
	}

	public static function addHandler(
		LogHandler $handler,
		?string $logLevel = null,
		?string $maxLogLevel = null,
	):void {
		array_push(self::$handlers, $handler);
		array_push(self::$handlerMinLevels, $logLevel ?? self::$defaultHandlerLevel);
		array_push(self::$handlerMaxLevels, $maxLogLevel);
	}

	private static function ensureAtLeastOneHandler():void {
		if(empty(self::$handlers)) {
			self::$handlers = array();
			array_push(
				self::$handlers,
				self::getDefaultHandler()
			);
			self::$handlerMinLevels = array();
			array_push(
				self::$handlerMinLevels,
				self::$defaultHandlerLevel
			);
			self::$handlerMaxLevels = [null];
		}
	}

	private static function getLogLevelIndex(string $level):?int {
		$index = array_search($level, LogLevel::ALL_LEVELS, true);

		return $index === false ? null : $index;
	}
}
