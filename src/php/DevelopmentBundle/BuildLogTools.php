<?php

namespace Frontastic\Common\DevelopmentBundle;

class BuildLogTools
{
    public static function trimParallelBuildLog(string $inputBuildLog): string
    {
        // Remove (maybe available) parallel build output from the build log
        $omit = false;
        $filteredBuildLog = '';
        $separator = "\r\n";

        $line = strtok($inputBuildLog, $separator);
        while ($line !== false) {
            if (preg_match('(\[PARALLEL BUILD START\])', $line)) {
                $omit = true;
            }

            if (!$omit) {
                $filteredBuildLog .= $line . "\n";
            }

            if (preg_match('(\[PARALLEL BUILD END\])', $line)) {
                $omit = false;
            }

            $line = strtok($separator);
        }

        return $filteredBuildLog;
    }
}
