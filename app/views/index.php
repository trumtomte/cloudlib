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
    echo '</pre>';
    if(isset($error))
        echo $error;
    echo '<br>';

    echo $this->form->open('/cloudlib/index/upload', array('type' => 'file'));
    echo $this->form->input('file', array('type' => 'file'));
    echo $this->form->button('upload', 'upload');
    echo $this->form->close();
?>
</body>
</html>
