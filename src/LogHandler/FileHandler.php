<?php
namespace Gt\Logger\LogHandler;


class FileHandler extends LogHandler {
	/** @var resource */
	protected $resource;

	/** @param array<string> $logFormat */
	public function __construct(
		string $path,
		string $timestampFormat = "Y-m-d H:i:s",
		array $logFormat = self::DEFAULT_LOG_FORMAT,
		string $separator = self::DEFAULT_SEPARATOR,
		string $newLine = self::DEFAULT_LOG_LINE_ENDING
	) {
		$this->resource = fopen($path, "a");

		parent::__construct(
			$timestampFormat,
			$logFormat,
			$separator,
			$newLine
		);
	}

	public function handle(
		string $level,
		string $message,
		array $context = []
	):void {
		fwrite(
			$this->resource,
			$this->getLogLine($level, $message, $context)
		);
	}

	/** @return resource */
	public function getResource() {
		return $this->resource;
	}

	/** @param array<string|array<string>> $context */
	protected function unwrapContext(array $context):string {
		$unwrapped = "";

		foreach($context as $key => $value) {
			if(strlen($unwrapped) > 0) {
				$unwrapped .= " ";
			}

			if(is_array($value)) {
				$value = "{"
					. $this->unwrapContext($value)
					. "}";
			}

			$unwrapped .= "[$key = $value]";
		}

		return $unwrapped;
	}
}
