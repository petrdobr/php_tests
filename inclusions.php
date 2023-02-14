<?php
session_start();
require_once "pdo.php";

function flashMessage() {
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red;">'. htmlentities($_SESSION['error'])."</p>\n";
        unset($_SESSION['error']);
}
    if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green;">'. htmlentities($_SESSION['success'])."</p>\n";
    unset($_SESSION['success']);
}
}
function access_check() {
if (! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}
}

function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            //$_SESSION['error'] = "All fields are required";
            //return False;
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            //$_SESSION['error'] = "Position year must be numeric";
            //return False;
            return "Position year must be numeric";
        }
    }
    return True;
}

echo('<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">');

echo('<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">'); // if I want to use it someday

    echo('<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>');