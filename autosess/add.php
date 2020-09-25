<?php
require_once "pdo.php";
session_start();
require_once "pdo.php";
if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}
// If the user requested logout go back to login.php
if (isset($_POST['cancel'])) {
    header('Location: view.php');
    return;
}

if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make'])<1) {
        $_SESSION['error'] = "Make is required";
        header("Location: add.php");
        return;
    }elseif (is_numeric($_POST['mileage']) == false || is_numeric($_POST['year']) == false){
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        return;
    }else{
        $stmt = $pdo->prepare('INSERT INTO autos
        (make, year, mileage) VALUES ( :mk, :yr, :mi)');
        $stmt->execute(array(
                ':mk' => $_POST['make'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage'])
        );
        $_SESSION['success'] = "Record inserted";
        header("Location: view.php");
        return;
    }
}
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
    // Note triple not equals and think how badly double
    // not equals would work here...
    if ( isset($_SESSION['error'])) {
        // Look closely at the use of single and double quotes
        echo('<p style="color: #ff0000;">' .htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
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
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>

</html>
