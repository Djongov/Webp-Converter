<?php
if (isset($_POST['delete']) and $_POST['delete'] === 'all') {
    $files = glob('./temp/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
        }
    }
}
?>