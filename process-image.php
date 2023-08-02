<?php
$error_class = 'text-red-500 font-semibold';
echo '<div class="text-center mx-auto">';
do {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        break 1;
    }
    //return var_dump($_FILES);
    if (!isset($_FILES["image"])) {
        echo '<p class="' . $error_class . '">There is no file to upload</p>';
        break 1;
    }

    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $allowedTypes = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/jpeg' => 'jpeg'
    ];

    if ($extension !== 'png' and $extension !== 'jpg' and $extension !== 'jpeg') {
        echo '<p class="' . $error_class . '">Incorrect file extension</p>';
        break 1;
    }
    
    $filepath = $_FILES['image']['tmp_name'];
    $fileSize = filesize($filepath);
    $fileSize_in_KBs = $fileSize / 1000;
    echo '<p>File size: ' . $fileSize_in_KBs . ' KB</p>';
    $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
    $filetype = finfo_file($fileinfo, $filepath);

    if ($fileSize === 0) {
        echo '<p class="' . $error_class . '">The file is empty</p>';
        break 1;
    }

    if ($fileSize > intval(ini_get('post_max_size')) * 1024 * 1024) {
        echo '<p class="' . $error_class . '">File exceeds 12MB/p>';
        break 1;
    }

    if (!in_array($filetype, array_keys($allowedTypes))) {
        echo '<p class="' . $error_class . '">File type not allowed/p>';
        break 1;
    }

    $filename = basename($filepath); // I'm using the original name here, but you can also change the name of the file here
    $extension = $allowedTypes[$filetype];
    $targetDirectory = __DIR__ . '/temp'; // __DIR__ is the directory of the current PHP file

    $newFilepath = $targetDirectory . "/" . $filename . "." . $extension;

    if (!copy($filepath, $newFilepath)) { // Copy the file, returns false if failed
        echo '<p class="' . $error_class . '">Can\'t move file. This is internal error</p>';
        break 1;
    }
    unlink($filepath); // Delete the temp file

    echo "<p>File uploaded successfully :)</p>";

    echo '<p>Trying to convert the file</p>';

    $newName = basename($newFilepath, $extension);

    $newName .= 'webp';

    // Create and save
    if ($extension === 'jpg') {
        $img = @imagecreatefromjpeg($newFilepath);
    } elseif ($extension === 'png') {
        $img = @imagecreatefrompng($newFilepath);
    } elseif ($extension === 'jpeg') {
        $img = @imagecreatefromjpeg($newFilepath);
    } else {
        echo '<p class="' . $error_class . '">Can\'t create reference image. Something is wrong with your source or our logic</p>';
        break 1;
    }
    imagepalettetotruecolor($img);
    imagealphablending($img, true);
    imagesavealpha($img, true);
    if (imagewebp($img, $targetDirectory . '/' . $newName, 80)) {
        echo "<p>Successfully converted</p>";
        $old_webp_filepath = $targetDirectory . '/' . $newName;
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_webp_filepath = $targetDirectory . '/' . basename($_FILES["image"]["name"], $extension) . 'webp';
        $renamed_webp = rename($old_webp_filepath, $new_webp_filepath);
        $new_fileSize = filesize($new_webp_filepath);
        $new_fileSize_in_KBs = $new_fileSize / 1000;
        echo '<p>New file size: ' . $new_fileSize_in_KBs . ' KB</p>';
        $saved_kbs = $fileSize_in_KBs - $new_fileSize_in_KBs;
        if ($saved_kbs < 0) {
            echo '<p>Unfortunately you didn\'t save any KBs (' . abs($saved_kbs) . ' KBs added extra). Sometimes it happens if the source image is not ready for conversion</p>';
        } else {
            echo '<p>You saved ' . $saved_kbs . ' KB which is <strong>' . round(($fileSize_in_KBs / $new_fileSize_in_KBs) * 100) . '% savings</strong></p>';
        }
        echo '<p>Download the converted webp <a class="underline text-green-500 hover:text-green-600 font-semibold" href="./temp/' . basename($new_webp_filepath) . '" target="_blank" download="' . basename($new_webp_filepath) . '">file</a></p>';
        echo '<p>You have less than 5 minutes to download it, then it disappears</p>';
        echo '<p>Preview:</p>';
        echo '<p><img class="text-center mx-auto" src="./temp/' . basename($new_webp_filepath) . '" alt="Your Image" width="350" height="auto" /></p>';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/functions/actionLog.php';
        writeToLogFile('Successfully converted ' . basename($new_webp_filepath));
    } else {
        echo '<p class="' . $error_class . '">Conversion unsuccessful</p>';
        break 1;
    }
    imagedestroy($img);
    //unlink($filepath);
    unlink($newFilepath);
    
} while (0);
echo '</div>';