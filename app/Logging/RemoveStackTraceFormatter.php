<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class RemoveStackTraceFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            // Create a custom line formatter without stack traces
            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context%\n",
                'Y-m-d H:i:s',
                true,  // Allow inline line breaks
                true   // Ignore empty context
            );
            
            // Don't include stack traces in context
            $formatter->includeStacktraces(false);
            
            $handler->setFormatter($formatter);
        }
    }
}
