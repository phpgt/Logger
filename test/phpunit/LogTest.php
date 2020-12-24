<?php
namespace Gt\Logger\Test;

use Gt\Logger\Log;
use Gt\Logger\LogConfig;
use Gt\Logger\LogLevel;
use Gt\Logger\Test\Helper\StdOutToEcho;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase {
	public function testStaticUsage() {
		$message = "Test info log";
		self::redirectDefaultHandlerToTestOutput();

		$expectedRegex = "";

		foreach(LogLevel::ALL_LEVELS as $level) {
			$lcLevel = strtolower($level);
			call_user_func(Log::class . "::$lcLevel", $message);
			$expectedRegex .= "\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d\s+$level\s+$message\n";
		}
		self::expectOutputRegex("/$expectedRegex/");
	}

	protected function redirectDefaultHandlerToTestOutput():void {
		stream_filter_register("intercept", StdOutToEcho::class);
		$stdout = LogConfig::getDefaultHandler()->getResource();
		stream_filter_append($stdout, "intercept");
	}
}