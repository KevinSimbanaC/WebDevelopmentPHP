<?php
session_start();
require_once "pdo.php";
if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Autos</title>
</head>
<body>
<div class="container">
    <h1>Tracking Autos for <?php echo $_SESSION['name']; ?></h1>
    <?php
    if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
    }
    ?>

    <h2>Automobiles</h2>
    <ul>
        <?php
        foreach ( $rows as $row ) {
            echo ("<li>".$row['year']." ".htmlentities($row['make'])." / ".$row['mileage']."</li>\n");
        }
        ?>
    </ul>
    <p>
        <a href="add.php">Add New</a> |
        <a href="logout.php">Logout</a>
    </p>
</div>

</html>
