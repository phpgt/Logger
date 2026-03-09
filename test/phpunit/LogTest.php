<?php
namespace Gt\Logger\Test;

use Gt\Logger\Log;
use Gt\Logger\LogConfig;
use Gt\Logger\LogHandler\FileHandler;
use Gt\Logger\LogHandler\StdErrHandler;
use Gt\Logger\LogLevel;
use Gt\Logger\Test\Helper\StdOutToEcho;
use PHPUnit\Framework\TestCase;

/** @runTestsInSeparateProcesses  */
class LogTest extends TestCase {
	public function test_staticCalls():void {
		$message = "Test info log";
		self::redirectDefaultHandlerToTestOutput();

		$expectedRegex = "";

		foreach(LogLevel::ALL_LEVELS as $level) {
			$lcLevel = strtolower($level);
			Log::$lcLevel($message);
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

	public function test_levelOutput():void {
		LogConfig::setDefaultHandlerLevel(LogLevel::ERROR);
		self::redirectDefaultHandlerToTestOutput();

		Log::debug("This is not critical!");
		Log::critical("This is critical!");
		$output = self::getActualOutputForAssertion();
		self::assertStringContainsString("This is critical!", $output);
		self::assertStringNotContainsString("This is not critical!", $output);
	}

	public function test_levelOutput_all():void {
		LogConfig::setDefaultHandlerLevel(LogLevel::DEBUG);
		self::redirectDefaultHandlerToTestOutput();

		Log::debug("This is not critical!");
		Log::critical("This is critical!");
		$output = self::getActualOutputForAssertion();
		self::assertStringContainsString("This is critical!", $output);
		self::assertStringContainsString("This is not critical!", $output);
	}

	public function test_addHandler_minMaxLevelRange():void {
		$debugLogFile = tempnam(sys_get_temp_dir(), "logger_debug_");
		$errorLogFile = tempnam(sys_get_temp_dir(), "logger_error_");
		if($debugLogFile === false || $errorLogFile === false) {
			self::fail("Unable to create temp log files");
		}

		LogConfig::addHandler(
			new FileHandler($debugLogFile),
			LogLevel::DEBUG,
			LogLevel::WARNING
		);
		LogConfig::addHandler(
			new FileHandler($errorLogFile),
			LogLevel::ERROR,
			LogLevel::EMERGENCY
		);

		Log::info("INFO to debug stream");
		Log::error("ERROR to error stream");

		$debugOutput = file_get_contents($debugLogFile);
		$errorOutput = file_get_contents($errorLogFile);
		if($debugOutput === false || $errorOutput === false) {
			self::fail("Unable to read temp log files");
		}
		self::assertStringContainsString("INFO to debug stream", $debugOutput);
		self::assertStringNotContainsString("ERROR to error stream", $debugOutput);
		self::assertStringContainsString("ERROR to error stream", $errorOutput);
		self::assertStringNotContainsString("INFO to debug stream", $errorOutput);

		unlink($debugLogFile);
		unlink($errorLogFile);
	}

	public function test_addHandler_minLevelBackwardsCompatible():void {
		$logFile = tempnam(sys_get_temp_dir(), "logger_compat_");
		if($logFile === false) {
			self::fail("Unable to create temp log file");
		}

		LogConfig::addHandler(new FileHandler($logFile), LogLevel::ERROR);
		Log::warning("warning should not be logged");
		Log::error("error should be logged");

		$output = file_get_contents($logFile);
		if($output === false) {
			self::fail("Unable to read temp log file");
		}
		self::assertStringNotContainsString("warning should not be logged", $output);
		self::assertStringContainsString("error should be logged", $output);

		unlink($logFile);
	}

	public function test_stdErrHandler_usesStdErrStream():void {
		$handler = new StdErrHandler();
		$resourceMetaData = stream_get_meta_data($handler->getResource());

		self::assertArrayHasKey("uri", $resourceMetaData);
		self::assertSame("php://stderr", $resourceMetaData["uri"]);
	}

	protected function redirectDefaultHandlerToTestOutput():void {
		stream_filter_register("intercept", StdOutToEcho::class);
		$stdout = LogConfig::getDefaultHandler()->getResource();
		stream_filter_append($stdout, "intercept");
	}
}
