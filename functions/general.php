<?php

function emptyTemp() {
    $files = glob('./temp/*'); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file)) {
            unlink($file);
        }
    }
}

function writeToLogFile($message)
{
    $file = dirname(__DIR__) . '/access.log';
    $date = date('d/m/Y H:i:s', time());
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $client_ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
    // For now anonymize the IP address with * instead of text, using regex
    $client_ip = preg_replace('/\d/', '*', $client_ip);
    $write_to_log = PHP_EOL . $date . ' | ' . $client_ip . ' | ' . $user_agent . ' | ' . $message;
    if (is_writable($file)) {
        file_put_contents($file, $write_to_log, FILE_APPEND | LOCK_EX);
    }
}
