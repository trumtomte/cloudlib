<?php
    echo '<br>';
    echo Benchmark::boot();
    echo '<br>';
    echo '<pre>';
    debug_print_backtrace();
    echo '</pre>';
    echo '<pre>';
    echo print_r($_SERVER);
    echo '<br>';
    echo print_r($_REQUEST);
    echo '</pre>';
    echo '<br>';
?>
