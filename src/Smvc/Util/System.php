<?php

namespace Smvc\Util;

final class System
{
    /**
     * Alias of the system() command using popen()
     *
     * @param string $command
     * @param int $return
     * @param string $output
     * @return int
     */
    static public function run($command, &$return = null, &$output = '')
    {
        if ($proc = popen("($command)2>&1","r")) {
            while (!feof($proc)) {
                $output .= fgets($proc, 1000);
            }
            return pclose($proc);
        }
    }
}
