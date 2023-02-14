<?php
require_once "inclusions.php";
if (isset($_POST['cancel'])) {
    unset($_POST['cancel']);
    header("Location: index.php");
    return;
}
access_check();

// Make sure that profile id is also set
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }

if ( isset($_POST['delete']) ) {
    $stmt = $pdo->prepare('SELECT user_id FROM profile
    WHERE profile_id = :pid');
    $stmt->execute(array( ':pid' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['user_id'] == $_SESSION['user_id']) {
      $sql = "DELETE FROM profile WHERE profile_id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':id' => $_GET['profile_id']));
      $_SESSION['success'] = 'Record deleted';
     header( 'Location: index.php' ) ;
     return;
    }
    else {
        $_SESSION['error'] = "You can't delete this user!";
        header('Location: index.php');
        return;
    }
}

$nm = htmlentities($_SESSION['names_for_del'][$_GET['profile_id']])

?>
<html>
<head>
    <title>
        Petr Dobrokhotov autos DB
    </title>
</head>
<body>
<div style="padding-left: 4%;">
    <h1>
    Confirm: Deleting <?= $nm ?>
</h1>
    <form method="post">
<p><input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel"></p>
</form></div></body></html>