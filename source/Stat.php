<?php
namespace fmihel\cache;

use fmihel\console;

class Stat
{

    static $timers = [];

    public static function start($name)
    {
        global $timers;
        $timers[$name] = microtime(true);
    }

    public static function stop($name, $out = true)
    {
        global $timers;
        $out = $out === true ? $name : $out;

        $diff = sprintf('%.6f sec.', microtime(true) - $timers[$name]);
        unset($timers[$name]);
        if ($out) {
            console::log($out . ' ' . $diff);
        }
        return $diff;
    }
}
