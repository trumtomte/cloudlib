<!DOCTYPE html>
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
    echo '<br>';
    echo security::encrypt('asdqsaasdaswe', 'sasdasdijoqwe');
    echo '<br>';

    echo '</pre>';
?>
</body>
</html>
