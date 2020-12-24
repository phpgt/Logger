<?php
namespace Gt\Logger;

class LogLevel {
	const DEBUG = "DEBUG";
	const INFO = "INFO";
	const NOTICE = "NOTICE";
	const WARNING = "WARNING";
	const ERROR = "ERROR";
	const CRITICAL = "CRITICAL";
	const ALERT = "ALERT";
	const EMERGENCY = "EMERGENCY";

	const ALL_LEVELS = [
		self::DEBUG,
		self::INFO,
		self::NOTICE,
		self::WARNING,
		self::ERROR,
		self::CRITICAL,
		self::ALERT,
		self::EMERGENCY,
	];
}