<?php
namespace GT\Logger\Test;

use GT\Logger\Log;
use GT\Logger\LogConfig;
use GT\Logger\LogHandler\LogHandler;
use GT\Logger\LogLevel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class LogConfigTest extends TestCase {
	#[DataProvider("defaultLevels")]
	public function testAddHandler_explicitNullBoundsUseDefaultMinimumWithoutMaximum(string $minimum, array $expected):void {
		$sut = LogConfig::class;
		$sut::setDefaultHandlerLevel($minimum);
		$handler = $this->recordingHandler();
		$sut::addHandler($handler, null, null);

		Log::debug("debug");
		Log::warning("warning");
		Log::emergency("emergency");

		self::assertSame($expected, $handler->messages);
	}

	public static function defaultLevels():iterable {
		yield "all levels" => [LogLevel::DEBUG, ["debug", "warning", "emergency"]];
		yield "warning and above" => [LogLevel::WARNING, ["warning", "emergency"]];
		yield "highest level" => [LogLevel::EMERGENCY, ["emergency"]];
	}

	public function testAddHandler_omittedBoundsKeepTheDefaultAtRegistration():void {
		$sut = LogConfig::class;
		$sut::setDefaultHandlerLevel(LogLevel::WARNING);
		$handler = $this->recordingHandler();
		$sut::addHandler($handler);
		$sut::setDefaultHandlerLevel(LogLevel::DEBUG);

		Log::debug("debug");
		Log::warning("warning");
		Log::emergency("emergency");

		self::assertSame(["warning", "emergency"], $handler->messages);
	}

	public function testAddHandler_nullMinimumRetainsExplicitMaximum():void {
		$sut = LogConfig::class;
		$sut::setDefaultHandlerLevel(LogLevel::INFO);
		$handler = $this->recordingHandler();
		$sut::addHandler($handler, null, LogLevel::WARNING);

		Log::debug("debug");
		Log::info("info");
		Log::warning("warning");
		Log::error("error");

		self::assertSame(["info", "warning"], $handler->messages);
	}

	private function recordingHandler():LogHandler {
		return new class extends LogHandler {
			public array $messages = [];
			public function handle(string $level, string $message, array $context = []):void {
				$this->messages[] = $message;
			}
			protected function unwrapContext(array $context):string {
				return "";
			}
		};
	}
}
