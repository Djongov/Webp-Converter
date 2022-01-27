<h1>Webp Converter</h1>
<form name="upload" action="" method="POST" enctype="multipart/form-data">
	Select image to upload: <input type="file" name="image" />
	<input type="submit" name="upload" value="upload" />
</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_FILES["image"])) {
        die("There is no file to upload.");
    }

    //echo "<p>" . var_dump($_FILES) . "</p>";

    $allowedTypes = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg'
    ];
    
    $targetDirectory = __DIR__ ; // __DIR__ is the directory of the current PHP file
    
    echo '<p>Processing upload...</p>';

    if (!isset($_FILES["image"])) {
        die("There is no file to upload.");
    }

    $filepath = $_FILES['image']['tmp_name'];
    $fileSize = filesize($filepath);
    $fileSize_in_KBs = $fileSize / 1000;
    echo '<p>File size: ' . $fileSize_in_KBs . ' KB</p>';
    $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
    $filetype = finfo_file($fileinfo, $filepath);

    if ($fileSize === 0) {
        die("The file is empty.");
    }

    if ($fileSize > 12582912) { // 12 MB (1 byte * 1024 * 1024 * 12 (for 12 MB))
        die("The file is too large");
    }

    if (!in_array($filetype, array_keys($allowedTypes))) {
        die("File not allowed.");
    }

    $filename = basename($filepath); // I'm using the original name here, but you can also change the name of the file here
    $extension = $allowedTypes[$filetype];
    $targetDirectory = __DIR__ . '/temp'; // __DIR__ is the directory of the current PHP file

    $newFilepath = $targetDirectory . "/" . $filename . "." . $extension;

    if (!copy($filepath, $newFilepath)) { // Copy the file, returns false if failed
        die("Can't move file.");
    }
    unlink($filepath); // Delete the temp file

    echo "File uploaded successfully :)";

    echo '<p>Trying to convert the file</p>';

    $ext = pathinfo($newFilepath, PATHINFO_EXTENSION);

    echo '<p>Extension is '.$ext . '</p>';

        if ($ext === 'png' || $ext === 'jpg') {

            $newName = basename($newFilepath, $ext);

            $newName .= 'webp';

            // Create and save
            if ($ext === 'jpg') {
                $img = imagecreatefromjpeg($newFilepath);
            } elseif ($ext === 'png') {
                $img = imagecreatefrompng($newFilepath);
            } else {
                die("BOOM");
            }
            
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            if (imagewebp($img, $targetDirectory . '/' . $newName, 100)) {
                echo "<p>Successfully converted</p>";
                $old_webp_filepath = $targetDirectory . '/' . $newName;
                $new_webp_filepath = $targetDirectory . '/' . basename($_FILES["image"]["name"], $ext) . 'webp';
                $renamed_webp = rename($old_webp_filepath, $new_webp_filepath);
                $new_fileSize = filesize($new_webp_filepath);
                $new_fileSize_in_KBs = $new_fileSize / 1000;
                echo '<p>New file size: ' . $new_fileSize_in_KBs . ' KB</p>';
                echo '<p>Download the converted webp <a href="./temp/' . basename($_FILES["image"]["name"], $ext) . 'webp' . '" target="_blank">file</a></p>';
                $saved_kbs = $fileSize_in_KBs - $new_fileSize_in_KBs;
                if ($saved_kbs < 0) {
                    echo '<p>Unfortunately you didn\'t save any KBs (' . abs($saved_kbs) . ' KBs added extra)</p>';
                } else {
                    echo 'You saved ' . $saved_kbs . ' KB which is ' . ($fileSize_in_KBs / $new_fileSize_in_KBs) * 100 . ' % savings</p>';
                }
            } else {
                die("Conversion unsuccessful"); 
            }
            imagedestroy($img);
            unlink($filepath);
            unlink($newFilepath);

        } else {
            die("File not png or jpg. Cannot convert to webp");
        }

}


?>