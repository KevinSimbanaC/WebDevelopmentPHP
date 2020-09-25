<?php
require_once "pdo.php";
// Demand a GET parameter
if (!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die('Name parameter missing');
}
// If the user requested logout go back to login.php
if (isset($_POST['logout'])) {
    header('Location: login.php');
    return;
}
$failure = false;
$successDatabase = false;
if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if ( is_numeric($_POST['mileage']) == false || is_numeric($_POST['year']) == false ) {
        $failure = "Mileage and year must be numeric";
    }elseif (strlen($_POST['make'])<1){
        $failure = "Make is required";
    }else{
        $stmt = $pdo->prepare('INSERT INTO autos
        (make, year, mileage) VALUES ( :mk, :yr, :mi)');
        $stmt->execute(array(
                ':mk' => $_POST['make'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage'])
        );
        $successDatabase = "Record inserted";
    }
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
    <h1>Tracking Autos for <?php echo $_GET['name']; ?></h1>
    <?php
    // Note triple not equals and think how badly double
    // not equals would work here...
    if ( $failure !== false ) {
        // Look closely at the use of single and double quotes
        echo('<p style="color: #ff0000;">' .htmlentities($failure)."</p>\n");
    }elseif ($successDatabase !== false){
        echo('<p style="color: #107a10;">' .htmlentities($successDatabase)."</p>\n");
    }
    ?>
    <form method="post">
        <p>Make:
            <input type="text" name="make" size="60"/></p>
        <p>Year:
            <input type="text" name="year"/></p>
        <p>Mileage:
            <input type="text" name="mileage"/></p>
        <input type="submit" value="Add">
        <input type="submit" name="logout" value="Logout">
    </form>

    <h2>Automobiles</h2>
    <ul>
    <?php
    foreach ( $rows as $row ) {
        echo ("<li>".$row['year']." ".htmlentities($row['make'])." / ".$row['mileage']."</li>\n");
    }
    ?>
    </ul>
</div>

</html>
