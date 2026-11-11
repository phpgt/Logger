<?php
namespace GT\Logger\Test\LogHandler;

use GT\Logger\LogHandler\FileHandler;
use GT\Logger\LogHandler\LogHandler;
use GT\Logger\LogLevel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class FileHandlerTest extends TestCase {
	public function testHandle_appendsMessagesWithoutOverwritingEarlierLines():void {
		$sut = new FileHandler("php://memory", logFormat: [LogHandler::LOG_PART_LEVEL, LogHandler::LOG_PART_MESSAGE]);

		$sut->handle(LogLevel::INFO, "First message");
		$sut->handle(LogLevel::ERROR, "Second message");

		rewind($sut->getResource());
		self::assertSame("INFO\tFirst message" . PHP_EOL . "ERROR\tSecond message" . PHP_EOL, stream_get_contents($sut->getResource()));
	}

	public function testConstruct_appliesCustomFormatSeparatorAndLineEnding():void {
		$sut = new FileHandler(
			"php://memory",
			timestampFormat: "\\T\\I\\M\\E",
			logFormat: [LogHandler::LOG_PART_MESSAGE, LogHandler::LOG_PART_LEVEL, LogHandler::LOG_PART_TIMESTAMP],
			separator: " | ",
			newLine: "\r\n",
		);

		$sut->handle(LogLevel::NOTICE, "Custom output");

		rewind($sut->getResource());
		self::assertSame("Custom output | NOTICE | TIME\r\n", stream_get_contents($sut->getResource()));
	}

	#[DataProvider("contexts")]
	public function testHandle_formatsContextWithoutLosingTypes(array $context, string $expected):void {
		$sut = new FileHandler("php://memory", logFormat: [LogHandler::LOG_PART_MESSAGE, LogHandler::LOG_PART_CONTEXT]);

		$sut->handle(LogLevel::DEBUG, "Context", $context);

		rewind($sut->getResource());
		self::assertSame("Context\t$expected" . PHP_EOL, stream_get_contents($sut->getResource()));
	}

	public static function contexts():iterable {
		yield "scalars" => [["id" => 42, "ratio" => 1.5, "active" => true], "[id = 42] [ratio = 1.5] [active = 1]"];
		yield "nested array" => [["request" => ["id" => 42, "tags" => ["a", "b"]]], "[request = {[id = 42] [tags = {[0 = a] [1 = b]}]}]"];
		yield "object" => [["value" => new stdClass()], "[value = stdClass]"];
		yield "null and false" => [["null" => null, "false" => false], "[null = ] [false = ]"];
	}

	public function testHandle_formatsResourceContextWithoutReadingOrClosingIt():void {
		$sut = new FileHandler("php://memory", logFormat: [LogHandler::LOG_PART_CONTEXT]);
		$resource = fopen("php://memory", "w+");
		fwrite($resource, "unchanged");

		try {
			$sut->handle(LogLevel::DEBUG, "Resource", ["stream" => $resource]);

			rewind($sut->getResource());
			self::assertSame("[stream = resource (stream)]" . PHP_EOL, stream_get_contents($sut->getResource()));
			self::assertSame(9, ftell($resource));
		}
		finally {
			fclose($resource);
		}
	}

	public function testHandle_omitsTrailingSeparatorWhenContextIsEmpty():void {
		$sut = new FileHandler("php://memory", logFormat: [LogHandler::LOG_PART_LEVEL, LogHandler::LOG_PART_MESSAGE, LogHandler::LOG_PART_CONTEXT]);

		$sut->handle(LogLevel::WARNING, "No context");

		rewind($sut->getResource());
		self::assertSame("WARNING\tNo context" . PHP_EOL, stream_get_contents($sut->getResource()));
	}
}
