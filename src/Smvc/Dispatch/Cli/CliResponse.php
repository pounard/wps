<?php

namespace Smvc\Dispatch\Cli;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\ResponseInterface;

class CliResponse extends AbstractApplicationAware implements ResponseInterface
{
    private function sendContent($output)
    {
        if (!empty($output)) {
            echo $output;
        }
    }

    public function writeLine($line)
    {
        echo $line . "\n";
    }

    public function send(
        $output,
        $contentType   = null,
        $statusCode    = null, 
        $statusMessage = null)
    {
        $this->sendContent($output);

        if ($statusMessage) {
            $this->writeLine(sprintf("%d: %s", $statusCode, $statusMessage));
        }

        exit($statusCode);
    }
}
