<?php
    echo Benchmark::compare(BOOTTIME);
    echo '<br>';
    echo '<pre>';
    debug_print_backtrace();
    echo '</pre>';
    echo '<pre>';
    echo var_dump($_SERVER);
    echo '</pre>';
    echo '<pre>';
    if(isset($test))
    {
    echo var_dump($test);
    }
    echo '</pre>';
    echo '<br>';
    echo Benchmark::compare(BOOTTIME);

