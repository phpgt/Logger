Simple logger for PHP applications.
===================================

Start logging with zero configuration. By default, the logger works as a static singleton - `Log::error("You can't do that");` is all you need to get started. When you need to, you can configure the logger to send logs to different file/socket sources.

The aim of this library is minimalism. There are no plans to implement more logging sources than files and sockets. This simplifies the code into having a single responsibility, but remains modular to hook up to external scripts that handle logging to email addresses, Slack messages, AWS SQS, databases, etc.

Log levels
----------

This library implements the [PSR-3 interface][psr3]. This means that throughout your code, you can decide to log at the levels listed below, and your environment can decide the minimum log level to report. For instance, it would probably be too verbose to log debug information on a production server. This is consistent with the [Syslog protocol][syslog].

+ `debug` - Detailed debugging information.
+ `info` - Interesting events.
+ `notice` - Normal, but significant events.
+ `warning` - Exceptional occurrences that are not errors.
+ `error` - Runtime errors that do not require immediate action but should typically be logged and monitored.
+ `critical` - Critical conditions.
+ `alert` - Action must be taken immediately.
+ `emergency` - System is unusable.

Static usage
------------

[Static classes should only be used when truly stateless][styleguide-static]. Logging is one example of a class that has no side effects on the running program, so the primary usage expectation is to use static methods of the `Log` class to perform logging.

It would be unnecessary to require passing an instance of the `Log` class around throughout all classes of your program, and it would be too opinionated to require the use of a dependency injection framework everywhere that logging is possible.

However, certain programs require advanced logging features that are only satisfiable with instances of the `Log` class, such as having different log sources for different areas of the program. Take a look at the [examples directory][examples] to see how instantiation can be used for this purpose.

Usage example
-------------

```php
use Gt\Logger\Log;
use Gt\Logger\LogConfig;
use Gt\Logger\LogLevel;

// Send warnings and above to the remote socket.
LogConfig::addSocketHandler("/example/remote.sock", LogLevel::WARNING);
// Send all log types to the local log file.
LogConfig::addFileHandler("/var/log/example.log", LogLevel::DEBUG);

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

[psr3]: https://www.php-fig.org/psr/psr-3/
[syslog]: https://tools.ietf.org/html/rfc5424
[examples]: https://github.com/PhpGt/Logger/tree/master/examples