<?php
use fmihel\console;

function heavy1($count=10){
    $out = 0;
    for($i=0;$i<$count;$i++){
        for($j=0;$j<$count;$j++){
            for($k=0;$k<$count;$k++){
                $out++;
            }
        }
    }
    return $out;
}



$timers = [];
function start($name){
    global $timers;
    $timers[$name] = microtime(true);
}
function stop($name,$out=true){
    global $timers;
    $out = $out === true?$name:$out;

    $diff = sprintf( '%.6f sec.', microtime( true ) - $timers[$name] );
    unset($timers[$name]);
    if ($out){
        console::log($out.' '.$diff);
    }
    return $diff;
}