<?php
$file = './action.log';

if (is_file($file)) {
    if (is_readable($file)) {
        echo '<h3 class="text-2xl text-bold">' . realpath($file) . '</h3>';
        $f = file($file);
        $f = implode(PHP_EOL, $f);
        $f = explode(PHP_EOL, $f);
            foreach ($f as $line) {
                if ($line === "") {
                    continue;
                }
                echo '<p>' . $line . '</p><hr />';
            }
    } else {
        echo '<p class="red">File not readable</p>';
    }
} else {
    echo '<p class="red">File does not exist</p>';
}
?>