<?php
/**
 * udp logger configuration
 */
return [
    'logger_name' => env('UDPLOG_NAME', 'channel_default'),
    'logger_buffer_size' => env('UDPLOG_BUFFER_SIZE', 5),
    'host' => env('UDPLOG_HOST', '127.0.0.1'),
    'port' => env('UDPLOG_PORT', 9502),
];
