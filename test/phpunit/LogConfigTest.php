<?php
namespace Gt\Logger\Test;

use Gt\Logger\LogConfig;
use Gt\Logger\LogHandler\FileHandler;
use Gt\Logger\LogHandler\StdOutHandler;
use Gt\Logger\LogLevel;
use PHPUnit\Framework\TestCase;

class LogConfigTest extends TestCase {
	public function testGetHandlers_returnsDefaultWhenEmpty():void {
		$handlerList = LogConfig::getHandlers();
		self::assertCount(1, $handlerList);
		self::assertInstanceOf(StdOutHandler::class, $handlerList[0]);
	}

	public function testGetHandlers_multipleAdded():void {
		$handler1 = self::createMock(FileHandler::class);
		$handler2 = self::createMock(FileHandler::class);
		$handler3 = self::createMock(FileHandler::class);
		LogConfig::addHandler($handler1);
		LogConfig::addHandler($handler2);
		LogConfig::addHandler($handler3);

		self::assertCount(3, LogConfig::getHandlers());
	}

	public function testGetHandlers_addWithDifferentLevels():void {
		$handler1 = self::createMock(FileHandler::class);
		$handler2 = self::createMock(FileHandler::class);
		$handler3 = self::createMock(FileHandler::class);
		LogConfig::addHandler($handler1, LogLevel::ERROR);
		LogConfig::addHandler($handler2, LogLevel::DEBUG);
		LogConfig::addHandler($handler3, LogLevel::EMERGENCY);

		$handlersAll = LogConfig::getHandlers();
		$handlersDebug = LogConfig::getHandlers(LogLevel::DEBUG);
		$handlersError = LogConfig::getHandlers(LogLevel::ERROR);
		$handlersEmergency = LogConfig::getHandlers(LogLevel::EMERGENCY);
		self::assertCount(3, $handlersAll);
		self::assertCount(3, $handlersDebug);
		self::assertCount(2, $handlersError);
		self::assertCount(1, $handlersEmergency);
	}
}
