<?php
$files = glob('./temp/*'); // get all file names
$deleted_files = [];
foreach($files as $file){ // iterate files
    if(is_file($file)) {
        if (unlink($file)) {
            array_push($deleted_files, $file);
        }
    }
}
$result = (count($deleted_files) > 0) ? 'Deleted ' . count($deleted_files) . ' files' : 'Nothing deleted';
?>