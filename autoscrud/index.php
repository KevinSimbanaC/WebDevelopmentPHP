<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kevin Dario Simbaña Cusicagua</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>
<body>
<div class="container">
    <h1>Welcome to the Automobiles Database</h1>
    <p>
        <?php
        if ( ! isset($_SESSION['name']) ) {
            echo ('<a href="login.php">Please Log In</a>');
            echo ('<p>Attempt to <a href="add.php">add data</a> without logging in</p>');
        }else{
            if ( isset($_SESSION['success']) ) {
                echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
                unset($_SESSION['success']);
            }
            $stmt = $pdo->query("SELECT make, model, year, mileage, autos_id  FROM autos");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rows == false){
                echo('<p>No rows found</p>');
            }else{
                echo('<table border="1">'."\n");
                echo('<tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr>');
                foreach ( $rows as $row ) {
                    echo "<tr><td>";
                    echo(htmlentities($row['make']));
                    echo("</td><td>");
                    echo(htmlentities($row['model']));
                    echo("</td><td>");
                    echo(htmlentities($row['year']));
                    echo("</td><td>");
                    echo(htmlentities($row['mileage']));
                    echo("</td><td>");
                    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
                    echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
                    echo("</td></tr>\n");
                }
                echo('</table>');

                }
            echo('<a href="add.php">Add New Entry</a><br><br>');
            echo('<a href="logout.php">Logout</a>');
            }
        ?>
    </p>


</div>
</body>
</html>
