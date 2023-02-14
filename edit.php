<?php
require_once "inclusions.php";
access_check();

if (isset($_POST['cancel'])) {
    unset($_POST['cancel']);
    header("Location: index.php");
    return;
}

// Guardian: Make sure that profile_id is set
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }

if (isset($_POST['first_name']) & isset($_POST['last_name']) 
  & isset($_POST['email']) & isset($_POST['headline']) & isset($_POST['summary'])) {
        //Check the user
        $stmt = $pdo->prepare('SELECT user_id FROM profile
        WHERE profile_id = :pid');
        $stmt->execute(array( ':pid' => $_GET['profile_id']));
        $row_ch = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($_SESSION['user_id'] != $row_ch['user_id']) {
            $_SESSION['error'] = "You don't have permission to edit this profile!";
            header("Location: index.php");
            return;
        }
        else {
    //Check entries
        $fname = $_POST['first_name']; //shorts for checks
        $lname = $_POST['last_name'];
        $email = $_POST['email'];
        $hline = $_POST['headline'];
        $summy = $_POST['summary'];
        if ($fname == null or $lname == null or $email == null or $hline == null or $summy == null) {
            $_SESSION['error'] = "All fields are required";
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;
        } elseif (! str_contains($email, '@')) {
            $_SESSION['error'] = "Email address must contain @";
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;
        } else {
            //everything seems ok
            $sql = "UPDATE profile SET first_name = :fn,
            last_name = :ln, email = :em, headline = :hi, summary = :sm
            WHERE profile_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(
                array(
                    ':fn' => $fname,
                    ':ln' => $lname,
                    ':em' => $email,
                    ':hi' => $hline,
                    ':sm' => $summy,
                    'id' => $_REQUEST['profile_id']
                )
            );
            $_SESSION['success'] = "Record added";
            header("Location: index.php");
            return;
        }
    }
}


//fetch data to autofill fields
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

?>
    <html>
<head>
    <title>
        Petr Dobrokhotov autos DB
    </title>
</head>
<body>
<div style="padding-left: 4%;">
<h1>Edit profile data for <?= htmlentities($_SESSION['names_for_del'][$_GET['profile_id']]) ?> </h1>
<?php //error messages
flashMessage();

$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$hline = htmlentities($row['headline']);
$summy = htmlentities($row['summary']);
?>
<form method="post">
<p>First Name:
<input type="text" size="40" name="first_name" value="<?= $fname ?>"></p>
<p>Last Name:
<input type="text" size="40" name="last_name" value="<?= $lname ?>"></p>
<p>Email:
<input type="text" size="40" name="email" value="<?= $email ?>"></p>
<p>Headline:<br>
<input type="text" size="80" name="headline" value="<?= $hline ?>"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="80"><?= $summy ?></textarea></p>
<p><input type="submit" value="Save"/>
<input type="submit" name="cancel" value="Cancel"/></p>
</form>


</div>
</body>
</html>