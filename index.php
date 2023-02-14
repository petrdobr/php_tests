<?php
require_once "inclusions.php";
$login = False;

if (isset($_POST['cancel'])) {
    unset($_POST['search']);
    unset($_SESSION['search']);
    unset($_GET['skip']);
    unset($_POST['cancel']);
    header("Location: index.php");
    return;
}

if (isset($_SESSION['name'])) {
    $login = True; //switch on/off actions
}

if (isset($_POST['search'])) {
    unset($_GET['skip']);
    $_SESSION['search'] = $_POST['search'];
    header("Location: index.php");
    return;
}

if (!isset($_SESSION['search'])) {
    $stmt_nm = $pdo->query("SELECT COUNT(profile_id) FROM profile");
    $numrows = $stmt_nm->fetch(PDO::FETCH_NUM);
    $numpgs = intdiv($numrows[0],10); //how many 3s there are
    $sql_nosrch = "SELECT profile_id, user_id, first_name, last_name, headline FROM profile
    LIMIT 10 OFFSET :dn";
    $stmt = $pdo->prepare($sql_nosrch);
    $dn = !isset($_GET['skip']) ? 0 : $_GET['skip'];
    $stmt->bindValue(':dn', $dn, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['rows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql_search = "SELECT profile_id, user_id, first_name, last_name, headline 
    FROM profile WHERE first_name LIKE :srch OR last_name LIKE :srch OR headline LIKE :srch 
    LIMIT 10 OFFSET :dn";
    $sql_num = "SELECT COUNT(profile_id) FROM profile 
    WHERE first_name LIKE :srch OR last_name LIKE :srch OR headline LIKE :srch";
    $stmt = $pdo->prepare($sql_search);
    $stmt_nm = $pdo->prepare($sql_num);
    $dn = !isset($_GET['skip']) ? 0 : $_GET['skip'];
    $search_statment = '%'.$_SESSION['search'].'%';
    $stmt->bindValue(':dn', $dn, PDO::PARAM_INT);
    $stmt->bindValue(':srch', $search_statment, PDO::PARAM_STR);
    $stmt->execute();
    $stmt_nm->bindValue(':dn', $dn, PDO::PARAM_INT);
    $stmt_nm->bindValue(':srch', $search_statment, PDO::PARAM_STR);
    $stmt_nm->execute();
    $_SESSION['rows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $numrows = $stmt_nm->fetch(PDO::FETCH_NUM);
    $numpgs = intdiv($numrows[0],10); //how many 3s there are
}

if (isset($_GET['done'])) {
    unset($_GET['done']);
    header('Location: index.php');
    return;
}
if (isset($_GET['logout'])) {
    unset($_GET['logout']);
    unset($_SESSION['name']);
    $login = False;
    header('Location: index.php');
    return;
}

?>
<html>
    <head>
    <title>
Petr Dobrokhotov - Resume Regisrty
    </title>
    <link rel="stylesheet" href="vendor\twbs\bootstrap\dist\css\bootstrap.min.css">
    </head>
    <body><div style="padding-left: 4%;">
        <h1>Welcome to Resume Registry</h1>
        <?php
        if (isset($_SESSION['rows'])) {
            flashMessage(); //message
            if ($_SESSION['rows'] == null) {
                print("<p>No rows found</p>");
                if (isset($_POST['search'])) {
                    echo ('<form method="post">
                    <p><input type="submit" value="Cancel"/></p></form>');
                }
                if (isset($_SESSION['name'])) {
                echo('<p><a href="add.php">Add New Entry</a></p>');
                echo ('<p><a href="index.php?logout=yes">Logout</a></p>');
                }
                else {
                    echo ('<p><a href="login.php">Please log in</a></p>');
                }
            } else {
                include("content.php");
            }
        }
        else {
            echo ('<p><a href="login.php">Please log in</a></p>');
        }
        ?>
        </div>
    </body>
</html>