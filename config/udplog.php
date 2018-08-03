<?php
/**
 * udp logger configuration
 */
return [
    'logger_name' => env('UDPLOG_NAME', 'channel_default'),
    'logger_buffer_size' => env('UDPLOG_BUFFER_SIZE', 1),
    'host_port' => env('UDPLOG_HOST_PORT', '127.0.0.1:9502'),
];
