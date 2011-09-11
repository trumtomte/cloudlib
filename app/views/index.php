<html lang="en">
<head>
<meta charset="utf-8" />
<title>test</title>
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
    echo $test;
    echo '<br>';
    echo substr('indexController', 0, -10);
?>
</body>
</html>
