<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>index_test</title>
</head>
<body>
<?php
    echo '<br>';
    echo timer::boot();
    echo '<br>';
    echo '<pre>';
    debug_print_backtrace();
    echo '<br>';
    echo '</pre>';
    if(isset($error))
        echo $error;
    echo '<br>';
    echo '<pre>';
    echo print_r($_SERVER);
    echo '</pre>';
    echo '<pre>';
    echo print_r($_REQUEST);
    echo '</pre>';
    echo '<br>';
    echo $test;
?>
</body>
</html>
