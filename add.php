<?php
require_once('inclusions.php');

access_check();
//update comment
if (isset($_POST['cancel'])) {
    unset($_POST['cancel']);
    header("Location: index.php");
    return;
}

if (isset($_POST['first_name']) & isset($_POST['last_name']) 
    & isset($_POST['email']) & isset($_POST['headline']) & isset($_POST['summary'])) {
        //Routine
        $fname = $_POST['first_name']; //shorts for checks
        $lname = $_POST['last_name'];
        $email = $_POST['email'];
        $hline = $_POST['headline'];
        $summy = $_POST['summary'];
        if ($fname == null or $lname == null or $email == null or $hline == null or $summy == null) {
            $_SESSION['error'] = "All fields are required";
            header('Location: add.php');
            return;
        } elseif (! str_contains($email, '@')) {
            $_SESSION['error'] = "Email address must contain @";
            header('Location: add.php');
            return;
        } 
        $msg = validatePos();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header ('location: add.php');
            return;
        }
            //everything seems ok
            $stmt = $pdo->prepare('INSERT INTO profile
        (user_id,first_name,last_name,email,headline,summary) VALUES (:uid, :fn, :ln, :em, :hd, :sm)');
            $stmt->execute(
                array(
                    ':uid' => $_SESSION['user_id'],
                    ':fn' => $fname,
                    ':ln' => $lname,
                    ':em' => $email,
                    ':hd' => $hline,
                    ':sm' => $summy
                )
            );
            //now insert position data
            $profile_id = $pdo->lastInsertId();    
            $rank = 1;
            for($i=1; $i<=9;$i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;
                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];
                $stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description) 
            VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
            );
            $rank++;
            }
            
            $_SESSION['success'] = "Record added";
            header("Location: index.php");
            return;
        }

?>
<html>
<head>
    <title>
        Petr Dobrokhotov Resume Registry
    </title>
</head>
<body><div style="padding-left: 4%;">
<h1>Add new profile for <?= $_SESSION['name'] ?></h1>
<?php //error messages
flashMessage();
?>
<form method="post">
<p>First Name:
<input type="text" size="40" name="first_name"></p>
<p>Last Name:
<input type="text" size="40" name="last_name"></p>
<p>Email:
<input type="text" size="40" name="email"></p>
<p>Headline:<br>
<input type="text" size="80" name="headline"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="80"></textarea></p>
<p>
Position: <input type="submit" id="addPos" value="+">
</p>
<div id="position_fields"></div>
<p></p>
<p><input type="submit" value="Add"/>
<input type="submit" name="cancel" value="Cancel"/></p>
<script>
countPos = 0;
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