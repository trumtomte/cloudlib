<?php
    echo '<pre>';
    debug_print_backtrace();
    echo '</pre><pre>';
    echo var_dump($_SERVER);
    echo '</pre><pre>Test variable: <hr>';
    if(isset($test)) {
    echo print_r($test);
    }
    echo '</pre><hr>';
