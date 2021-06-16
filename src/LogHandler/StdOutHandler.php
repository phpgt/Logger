<?php
namespace Gt\Logger\LogHandler;

class StdOutHandler extends FileHandler {
	public function __construct(
		string $timestampFormat = "Y-m-d H:i:s",
		array $logFormat = self::DEFAULT_LOG_FORMAT,
		string $separator = self::DEFAULT_SEPARATOR,
		string $newLine = self::DEFAULT_LOG_LINE_ENDING
	) {
		parent::__construct(
			"php://stdout",
			$timestampFormat,
			$logFormat,
			$separator,
			$newLine
		);
	}
}
