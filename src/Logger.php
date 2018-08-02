<?php

namespace Bulaohe\Udplog;

use Monolog\Logger as BaseLogger;
use Monolog\Handler\StreamHandler;

/**
 * Monolog log channel
 *
 * rewrite the method addRecord of class Monolog Logger for customized purpose
 * @author Simon Quan <qqmmmqq@gmail.com>
 */
class Logger extends BaseLogger
{

    /**
     * Adds a log record.
     *
     * @param  int     $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addRecord($level, array $context = array())
    {
        if (!$this->handlers) {
            $this->pushHandler(new StreamHandler('php://stderr', static::DEBUG));
        }

        $levelName = static::getLevelName($level);

        // check if any handler will handle this message so we can return early and save cycles
        $handlerKey = null;
        reset($this->handlers);
        while ($handler = current($this->handlers)) {
            if ($handler->isHandling(array('level' => $level))) {
                $handlerKey = key($this->handlers);
                break;
            }

            next($this->handlers);
        }

        if (null === $handlerKey) {
            return false;
        }

        if (!static::$timezone) {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        }

        // php7.1+ always has microseconds enabled, so we do not need this hack
        if ($this->microsecondTimestamps && PHP_VERSION_ID < 70100) {
            $ts = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone);
        } else {
            $ts = new \DateTime(null, static::$timezone);
        }
        $ts->setTimezone(static::$timezone);

        $record = array(
            'content' => $context,
            'level' => $level,
            'level_name' => $levelName,
            'channel' => $this->name,
            'date' => date('Y/m/d H:i:s'),
        );

        foreach ($this->processors as $processor) {
            $record = call_user_func($processor, $record);
        }

        while ($handler = current($this->handlers)) {
            if (true === $handler->handle($record)) {
                break;
            }

            next($this->handlers);
        }

        return true;
    }
}
