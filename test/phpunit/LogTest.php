<?php
namespace Gt\Logger\Test;

use Gt\Logger\Log;
use Gt\Logger\LogConfig;
use Gt\Logger\LogLevel;
use Gt\Logger\Test\Helper\StdOutToEcho;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase {
	public function test_staticCalls():void {
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

	public function test_context():void {
		$message = "Test info log";
		$context = [
			"test-type" => "PHPUnit",
			"this" => "that"
		];
		self::redirectDefaultHandlerToTestOutput();

		self::expectOutputRegex(
			"/\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d\s+ERROR\s+$message\s+\[test-type = PHPUnit\] \[this = that\]\n/"
		);
		Log::error($message, $context);
	}

	public function test_nestedContent():void {
		$message = "Test info log";
		$context = [
			"test-type" => "PHPUnit",
			"nested" => [
				"one" => "first",
				"two" => "second",
			],
			"this" => "that"
		];
		self::redirectDefaultHandlerToTestOutput();

		self::expectOutputRegex(
			"/\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d\s+ERROR\s+$message\s+\[test-type = PHPUnit\] \[nested = \{\[one = first\] \[two = second\]\}\] \[this = that\]\n/"
		);
		Log::error($message, $context);
	}

	protected function redirectDefaultHandlerToTestOutput():void {
		stream_filter_register("intercept", StdOutToEcho::class);
		$stdout = LogConfig::getDefaultHandler()->getResource();
		stream_filter_append($stdout, "intercept");
	}
}
