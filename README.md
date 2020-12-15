Simple logger for PHP applications.
===================================

Start Logging to standard out with zero configuration, and add the configuration when you need to. By default, the logger works as a static singleton - `Log::error("You can't do that");` is all you need to get started. When you need to, you can configure the logger to send logs to different file/socket sources.

Log levels
----------

This library implements the [PSR-3 interface][psr3]. This means that throughout your code, you can decide to log at the levels listed below, and your environment can decide the minimum log level to report. For instance, it would probably be too verbose to log debug information on a production server. 

+ `debug` - Detailed debugging information.
+ `info` - Interesting events.
+ `notice` - Normal, but significant events.
+ `warning` - Exceptional occurrences that are not errors.
+ `error` - Runtime errors that do not require immediate action but should typically be logged and monitored.
+ `critical` - Critical conditions.
+ `alert` - Action must be taken immediately.
+ `emergency` - System is unusable.

[psr3]: https://www.php-fig.org/psr/psr-3/