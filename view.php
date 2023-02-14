<?php
require_once "pdo.php";
session_start();
if (isset($_GET['done'])) {
    header("Location: index.php");
    return;
}
// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }
//fetch data to show record
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
    <html>
<head>
    <title>
        Petr Dobrokhotov Resume Registry
    </title>
    <link rel="stylesheet" href="vendor\twbs\bootstrap\dist\css\bootstrap.min.css">
</head>
<body>
<div style="padding-left: 4%;">
<h1>Read profile data of <?= htmlentities($_SESSION['names_for_del'][$_GET['profile_id']]) ?> </h1>
<?php 

$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$hline = htmlentities($row['headline']);
$summy = htmlentities($row['summary']);
?>
<p>First Name: <?= $fname ?></p>
<p>Last Name: <?= $lname ?></p>
<p>E-mail: <?= $email ?></p>
<p>Headline: <?= $hline ?></p>
<p>Summary: <br><?= $summy ?></p>
<a href="view.php?profile_id=<?= $_GET['profile_id'] ?>&done=yes">Done</a>
</div>
</body>
</html>