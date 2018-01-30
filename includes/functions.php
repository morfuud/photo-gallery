<?php
function redirect_to($location = NULL) {
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}

function __autoload($class_name) {
    $class_name = strtolower($class_name);
    $path = LIB_PATH.DS."{$class_name}.php";
    if (file_exists($path)) {
        require_once($path);
    } else {
        die("The file {$class_name}.php colud not be found");
    }
}

function include_layout_template($template="") {
    include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

function log_action($action, $message="") {
    $file = SITE_ROOT.DS."logs".DS."log_file.txt";

    if ($handle = fopen($file, 'a')) {// write
        if (is_writable($file)) {
            date_default_timezone_set('UTC');
            $content = strftime("%Y-%m-%d %H:%M:%S", time()) . " " . $action . ": " . $message . "\n";
            fwrite($handle, $content);
        } else {
            echo "Could not write to file.<br>";
        }
        fclose($handle);
    } 
}

function datetime_to_text($datetime="") {
    $unixdatetime = strtotime($datetime);
    return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}
?>