<?php
if (isset($_POST['delete']) and $_POST['delete'] === 'all') {
    $files = glob('./temp/*'); // get all file names
    $deleted_files = [];
    foreach($files as $file){ // iterate files
        if(is_file($file)) {
            if (unlink($file)) {
                array_push($deleted_files, $file);
            }
        }
    }
    echo (count($deleted_files) > 0) ? 'Deleted ' . count($deleted_files) : null;
    if (isset($_SERVER['HTTP_CRONHOOKS_SIGNATURE'])) {
        echo PHP_EOL . $_SERVER['HTTP_CRONHOOKS_SIGNATURE'];
        echo PHP_EOL;
        $payload = @file_get_contents('php://input');
        echo $payload;
    }
}
?>
