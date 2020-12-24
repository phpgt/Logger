<?php
namespace Gt\Logger\LogHandler;

abstract class LogHandler {
	const DEFAULT_LOG_FORMAT = [
		self::LOG_PART_TIMESTAMP,
		self::LOG_PART_LEVEL,
		self::LOG_PART_MESSAGE,
		self::LOG_PART_CONTEXT,
	];
	const DEFAULT_SEPARATOR = "\t";
	const DEFAULT_LOG_LINE_ENDING = PHP_EOL;
	const LOG_PART_TIMESTAMP = "{TIMESTAMP}";
	const LOG_PART_LEVEL = "{LEVEL}";
	const LOG_PART_MESSAGE = "{MESSAGE}";
	const LOG_PART_CONTEXT = "{CONTEXT}";

	private string $timestampFormat;
	protected array $logFormat;
	protected string $separator;
	protected string $logLineEnding;

	public function __construct(
		string $timestampFormat = "Y-m-d H:i:s",
		array $logFormat = self::DEFAULT_LOG_FORMAT,
		string $separator = self::DEFAULT_SEPARATOR,
		string $logLineEnding = self::DEFAULT_LOG_LINE_ENDING
	) {
		$this->timestampFormat = $timestampFormat;
		$this->logFormat = $logFormat;
		$this->separator = $separator;
		$this->logLineEnding = $logLineEnding;
	}

	abstract public function handle(
		string $level,
		string $message,
		array $context = []
	):void;

	abstract protected function unwrapContext(
		array $context
	):string;

	protected function getLogLine(
		string $level,
		string $message,
		array $context = []
	):string {
		$logLine = implode($this->separator, $this->logFormat);

		$logLine = str_replace(
			self::LOG_PART_TIMESTAMP,
			date($this->timestampFormat),
			$logLine
		);
		$logLine = str_replace(
			self::LOG_PART_LEVEL,
			$level,
			$logLine
		);
		$logLine = str_replace(
			self::LOG_PART_MESSAGE,
			$message,
			$logLine
		);
		$logLine = str_replace(
			self::LOG_PART_CONTEXT,
			$this->unwrapContext($context),
			$logLine
		);

		return trim($logLine) . $this->logLineEnding;
	}
}