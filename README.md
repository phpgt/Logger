Simple logger for PHP applications.
===================================

Start logging with zero configuration. By default, the logger writes to standard output, so `Log::error("You can't do that");` is all you need to get started. When you need to, you can configure the logger to send logs to different file or stream sources.

The aim of this library is minimalism. There are no large configuration objects or transport layers to learn first. We get a set of familiar log levels, a small handler system, and a straightforward way to route messages where they need to go.

Log levels
----------

This library uses the standard [PSR-3][psr3] level names. This means that throughout your code, you can decide to log at the levels listed below, and your environment can decide the minimum log level to report. For instance, it would probably be too verbose to log debug information on a production server. This is consistent with the [Syslog protocol][syslog].

+ `debug` - Detailed debugging information.
+ `info` - Interesting events.
+ `notice` - Normal, but significant events.
+ `warning` - Exceptional occurrences that are not errors.
+ `error` - Runtime errors that do not require immediate action but should typically be logged and monitored.
+ `critical` - Critical conditions.
+ `alert` - Action must be taken immediately.
+ `emergency` - System is unusable.

Basic usage
-----------

The primary entry point is the static `Log` class. Configure handlers near the start of the application, then call the matching log-level method whenever you need it.

Usage example
-------------

```php
use GT\Logger\Log;
use GT\Logger\LogConfig;
use GT\Logger\LogLevel;
use GT\Logger\LogHandler\StreamHandler;
use GT\Logger\LogHandler\FileHandler;
use GT\Logger\LogHandler\StdErrHandler;

// Send warnings and above to the remote socket.
LogConfig::addHandler(new StreamHandler("/example/remote.sock"), LogLevel::WARNING);
// Send all log types to the local log file.
LogConfig::addHandler(new FileHandler("/var/log/example.log"), LogLevel::DEBUG);
// Send only errors and above to STDERR.
LogConfig::addHandler(new StdErrHandler(), LogLevel::ERROR, LogLevel::EMERGENCY);
// Send lower-severity logs to STDOUT.
LogConfig::addHandler(LogConfig::getDefaultHandler(), LogLevel::DEBUG, LogLevel::WARNING);

$fileName = "name.txt";
if(file_exists($fileName)) {
        $name = trim(file_get_contents($fileName));
        
        if(empty($name)) {
                Log::error("Empty name loaded");        
        }
        else {
                Log::info("Loaded name: $name");
        }
}
else {
        $name = "you";
        Log::info("Using default name");
}
```

The primary namespace is `GT\Logger`. The legacy `Gt\Logger` classes remain autoloadable for backwards compatibility.

Full documentation is available in the wiki: https://github.com/PhpGt/Logger/wiki
[psr3]: https://www.php-fig.org/psr/psr-3/
[syslog]: https://tools.ietf.org/html/rfc5424

# Proudly sponsored by

[JetBrains Open Source sponsorship program](https://www.jetbrains.com/community/opensource/)

[![JetBrains logo.](https://resources.jetbrains.com/storage/products/company/brand/logos/jetbrains.svg)](https://www.jetbrains.com/community/opensource/)
