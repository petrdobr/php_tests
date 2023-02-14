<?php
require_once "inclusions.php";
if (isset($_POST['cancel'])) {
    unset($_POST['cancel']);
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
if (isset($_POST['email']) or isset($_POST['pass'])) { //if entry exists
    unset($_SESSION['name']); //logout old session
    $em = htmlentities($_POST['email']); // short variables for checkups
    $ps = htmlentities($_POST['pass']);
    if ($em == null OR $ps == null) {
        $_SESSION['error'] = "User name and password are required";
        header('Location: login.php');
        return;
    }
    elseif (! str_contains($em, '@')) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header('Location: login.php');
        return;
    }
    else {        
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false ) {
            $_SESSION['name'] = $row['name'];       
            $_SESSION['user_id'] = $row['user_id'];
            error_log("Login success ".$_POST['email']);
            // Redirect the browser to view.php
            $_SESSION['success'] = "Logged In.";
            header("Location: index.php");
            return;
        } 
        else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect password";
            header('Location: login.php');
            return;
        }         
    }

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
<p>Please Login</p>
<?php
flashMessage(); 
?>
<form method="post">
<p>Email:
<input type="text" size="40" name="email" id="id_1724"></p>
<p>Password:
<input type="text" size="40" name="pass" id="id_1723"></p>
<p><input type="submit" onclick="return doValidate();" value="Log In"/>
<input type="submit" name="cancel"  value="Cancel"/></p>
<script type="application/javascript">
function doValidate() {
         console.log('Validating...');
         try {
             pw = document.getElementById('id_1723').value;
             lg = document.getElementById('id_1724').value;
             console.log("Validating pw="+pw);
             if (pw == null || pw == "") {
                 alert("Both fields must be filled out");
                 return false;
             }
             if (lg == null || lg == "") {
                 alert("Both fields must be filled out");
                 return false;
             }
             if (!lg.includes("@")) {
                 alert("Invalid Email adress");
                 return false;
             }
             return true;
         } catch(e) {
             return false;
         }
         return false;
     }
    </script>
</form></div>
</body>
</html>