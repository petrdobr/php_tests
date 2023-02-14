<?php
$_SESSION['makes_for_del'] = array(); //just for names on screens
                echo ('<table border="1">' . "\n");
                echo ('<tr><th style="padding: 4px;">Name</th><th style="padding: 4px;">Headline</th>');
                if ($login) {
                 echo('<th style="padding: 4px;">Action</th></tr>');
                }
                else {echo('</tr>');}
                foreach ($_SESSION['rows'] as $row) {
                    $_SESSION['names_for_del'][$row['profile_id']] = $row['first_name'].' '.$row['last_name'];
                    echo ('<tr><td style="padding: 4px;">');
                    echo ('<a href="view.php?profile_id='.$row['profile_id'].'">'.
                    htmlentities($row['first_name']).' '.
                    htmlentities($row['last_name']));
                    echo ('</td><td style="padding: 4px;">');
                    echo (htmlentities($row['headline']));
                    echo ('</td>');
                    if ($login) {

                        if ($row['user_id'] == $_SESSION['user_id']) {
                     echo('<td style="padding: 4px;">');
                     echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                     echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                     echo ("</td></tr>\n"); 
                    }
                    else {
                        echo('<td style="padding: 4px;">');
                        echo ('No access');
                        echo ("</td></tr>\n"); 
                    }
                }
                    else { echo('</tr>');}
                }
                echo ('</table><br>' . "\n");
                //show next - back button
                if (!isset($_GET['skip']) || $_GET['skip'] <= 0) {
                    if ($numrows[0]>10) {
                    echo('<p><a href="index.php?skip=10">Next >>></a></p>');}
                } elseif (isset($_GET['skip']) && $_GET['skip']+10 <= $numpgs*10) {
                    $back = $_GET['skip'] <= 0 ? '' : '?skip='.($_GET['skip']-10);
                    $forward = $_GET['skip'] > $numpgs*10 ? '' : '?skip='.($_GET['skip']+10);
                    echo('<p><a href="index.php'.$back.'"><<< Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="index.php'.$forward.'">Next >>></a></p>');
                }
                elseif (isset($_GET['skip']) && $_GET['skip']+10 > $numpgs*10) {
                    $back = $_GET['skip'] <= 0 ? '' : '?skip='.($_GET['skip']-10);
                    echo('<p><a href="index.php'.$back.'"><<< Back</a>');
                }
                //search bar
                if ($login) {
                    echo ('<form method="post">
                    <p><input type="text" size="40" name="search">
                    <input type="submit" value="Search"/>  
                    <input type="submit" value="Cancel"/></p></form>');
                    echo('<p><a href="add.php">Add New Entry</a></p>');
                    echo ('<p><a href="index.php?logout=yes">Logout</a></p>');

                }
                else {
                    echo ('<form method="post">
                    <p><input type="text" size="40" name="search">
                    <input type="submit" value="Search"/>  
                    <input type="submit" value="Cancel"/></p></form>');
                    echo ('<p><a href="login.php">Please log in</a></p>');
                }
