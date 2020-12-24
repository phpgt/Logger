<?php
namespace Gt\Logger\Test;

use Gt\Logger\Log;
use Gt\Logger\LogConfig;
use Gt\Logger\Test\Helper\StdOutToEcho;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase {
	public function testStaticUsage() {
		$message = "Test info log";
		self::redirectDefaultHandlerToTestOutput();
		self::expectOutputRegex("/\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d\tINFO\t$message/");
		Log::info($message);
	}

	protected function redirectDefaultHandlerToTestOutput():void {
		stream_filter_register("intercept", StdOutToEcho::class);
		$stdout = LogConfig::getDefaultHandler()->getResource();
		stream_filter_append($stdout, "intercept");
	}
}