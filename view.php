<?php
require_once "inclusions.php";
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
$stmt->execute(array(":id" => $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM position where profile_id = :id order by rank");
$stmt->execute(array(":id" => $_REQUEST['profile_id']));
$rows_pos = $stmt->fetchALL(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM education where profile_id = :id order by rank");
$stmt->execute(array(":id" => $_REQUEST['profile_id']));
$rows_edu = $stmt->fetchALL(PDO::FETCH_ASSOC);

//get inst name from inst table and put it in the array

for ($i=0; $i<count($rows_edu); $i++) {
    $stmt = $pdo->prepare("SELECT name FROM institution where institution_id = :id");
    $stmt->execute(array(":id" => $rows_edu[$i]['institution_id']));
    $inst_name = $stmt->fetch(PDO::FETCH_ASSOC);
    $rows_edu[$i]['name'] = $inst_name['name'];
}
?>
    <html>
<head>
    <title>
        Petr Dobrokhotov Resume Registry
    </title>
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
<?php 
if ($rows_pos) {
echo('<p>Positions: </p>');
echo('<p><ul>');
foreach($rows_pos as $r) {
    echo('<li>'.htmlentities($r['year']).': '.htmlentities($r['description']).'</li>');
}
echo('</ul></p>');
}
if ($rows_edu) {
    echo('<p>Education: </p>');
    echo('<p><ul>');
    foreach($rows_edu as $r) {
        echo('<li>'.htmlentities($r['year']).': '.htmlentities($r['name']).'</li>');
    }
    echo('</ul></p>');
    }
?>
<a href="view.php?profile_id=<?= $_GET['profile_id'] ?>&done=yes">Done</a>
</div>
</body>
</html>