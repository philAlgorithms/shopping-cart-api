<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Logs in message with an ip address
 * 
 * @param string $ip
 * @param string $message
 * @param mixed $contex
 * @param string $level
 * @param string $channel
 * 
 * @return void
 */
function logWithIP( string $ip, string $message, string $level="info", mixed $context=[], string $channel="app")
{
    $logMessage = "[{$ip}]: {$message}";
    $logger = Log::channel($channel);
    switch($level)
    {
        case 'debug':
            $logger->debug($logMessage, $context);
            break;
        case 'info':
            $logger->info($logMessage, $context);
            break;
        case 'notice':
            $logger->notice($logMessage, $context);
            break;
        case 'warning':
            $logger->warning($logMessage, $context);
            break;
        case 'error':
            $logger->error($logMessage, $context);
            break;
        case 'critical':
            $logger->critical($logMessage, $context);
            break;
        case 'alert':
            $logger->alert($logMessage, $context);
            break;
        case 'emergency':
            $logger->emergency($logMessage, $context);
            break;
    }
}

function getpaginator(Request $request, int $default=20)
{
    return $request->has('paginate') && is_numeric($request->paginate) ? $request->paginate : env('PAGINATION_LENGTH', $default);
}