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
    echo 'Deleted ' . count($deleted_files);
    if (isset($_SERVER['HTTP_CRONHOOKS_SIGNATURE'])) {
        echo '\n' .$_SERVER['HTTP_CRONHOOKS_SIGNATURE'];
    }
}
?>
