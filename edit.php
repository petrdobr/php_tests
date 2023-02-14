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
        } 
        $msg = validatePos();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header ("location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;
        }
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
            //now insert position data with checking if data is already there   
            $rank = 1;
            for($i=1; $i<=9;$i++) {
                $stmt_check = $pdo->prepare('SELECT 1 from position WHERE rank = :rank AND profile_id = :id');
                $stmt_check->execute(array(':rank' => $i, ':id' => $_REQUEST['profile_id']));
                $rank_exists = $stmt_check->fetch(PDO::FETCH_ASSOC); //if no record should be FALSE
                if ($rank_exists) {
                    //record exists in DB but was deleted from edit page == DELETE position
                    if ( ! isset($_POST['year'.$i]) && ! isset($_POST['desc'.$i])) {
                        $sql = "DELETE FROM position WHERE rank = :rank AND profile_id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(':rank' => $i, ':id' => $_REQUEST['profile_id']));
                        continue;
                    } else { //record exists in DB and exists on edit page == UPDATE position (even if the same)
                        $year = $_POST['year'.$i];
                        $desc = $_POST['desc'.$i];
                        $sql = "UPDATE position SET year = :yr, description = :ds 
                        WHERE rank = :rank AND profile_id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                        ':yr' => $year,
                        ':ds' => $desc,
                        ':rank' => $i, 
                        ':id' => $_REQUEST['profile_id'])
                    );}
                } else {
                    if ( ! isset($_POST['year'.$i]) ) continue; //skip actions if DB record absent and no data inserted
                    if ( ! isset($_POST['desc'.$i]) ) continue;
                    //DB record is absent but there is new data on edit page == INSERT DATA
                    if (isset($_POST['year'.$i]) && isset($_POST['desc'.$i])) {
                        $year = $_POST['year'.$i]; //yeah, once again
                        $desc = $_POST['desc'.$i];
                        $stmt = $pdo->prepare('INSERT INTO Position
                        (profile_id, rank, year, description) 
                    VALUES ( :pid, :rank, :year, :desc)');
                    $stmt->execute(array(
                        ':pid' => $_GET['profile_id'],
                        ':rank' => $rank,
                        ':year' => $year,
                        ':desc' => $desc)
                    );
                    }
                }
            $rank++;
            }
            $_SESSION['success'] = "Record added";
            header("Location: index.php");
           // header ("location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;
        }
    }



//fetch data to autofill fields
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM position where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$rows_pos = $stmt->fetchALL(PDO::FETCH_ASSOC);

?>
    <html>
<head>
    <title>
        Petr Dobrokhotov autos DB
    </title>
</head>
<body>
<div style="padding-left: 4%;">
<h1>Edit <?= htmlentities($_SESSION['names_for_del'][$_GET['profile_id']]) ?> profile data </h1>
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
<p>Position: <input type="submit" id="addPos" value="+">
</p>
<?php //a bit of coding C:
if ( isset($rows_pos)) {
    foreach($rows_pos as $r) {
        $n = htmlentities($r['rank']);
        $edit_year = htmlentities($r['year']);
        $edit_desc = htmlentities($r['description']);
        echo('<div id="position'.$n.'">
        <br><p>Year: <input type="text" name="year'.$n.'" value="'.$edit_year.'" />
        <input type="button" value="-"
            onclick="$(\'#position'.$n.'\').remove();return false;"></p>
        <textarea name="desc'.$n.'" rows="8" cols="80">'.$edit_desc.'</textarea></div>');
    }
}

?>
<div id="position_fields"></div>
<p></p>
<p><input type="submit" value="Save"/>
<input type="submit" name="cancel" value="Cancel"/></p>

<script>
countPos = <?= isset($rows_pos) ? count($rows_pos) : 0 ?>;
window.console && console.log("Position started from "+countPos);
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <br><p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>



</form>


</div>
</body>
</html>