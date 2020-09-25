<?php
require_once "pdo.php";
session_start();
//Se revisa si se inicio sesion
if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}
//Se redirige si se cancela
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}
//Se hacen las respectivas validaciones
if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year'])
    && isset($_POST['mileage']) && isset($_POST['autos_id'])) {
    //Validacion de datos
    if (strlen($_POST['make'])<1 || strlen($_POST['model'])<1 || strlen($_POST['year'])<1 ||
        strlen($_POST['mileage'])<1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?autos_id=".$_POST['autos_id']);
        return;
    }elseif (is_numeric($_POST['mileage']) == false ){
        $_SESSION['error'] = "Mileage must be numeric";
        header("Location: edit.php?autos_id=".$_POST['autos_id']);
        return;
    }elseif (is_numeric($_POST['year']) == false){
        $_SESSION['error'] = "Year must be numeric";
        header("Location: edit.php?autos_id=".$_POST['autos_id']);
        return;
    }
    else{
        $sql = "UPDATE autos SET make = :make,
            model = :model, year = :year, mileage = :mileage
            WHERE autos_id = :autos_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
                ':make' => $_POST['make'],
                ':model' => $_POST['model'],
                ':year' => $_POST['year'],
                ':mileage' => $_POST['mileage'],
                ':autos_id' => $_POST['autos_id'])
        );
        $_SESSION['success'] = "Record edited";
        header("Location: index.php");
        return;
    }
}
// Guardian: Make sure that user_id is present
if ( ! isset($_GET['autos_id']) ) {
    $_SESSION['error'] = "Missing autos_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header( 'Location: index.php' ) ;
    return;
}else{
    $mk = htmlentities($row['make']);
    $md = htmlentities($row['model']);
    $ye = htmlentities($row['year']);
    $mi = htmlentities($row['mileage']);
    $autos_id = $row['autos_id'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Autos</title>
</head>
<body>
<div class="container">
    <h1>Editing Automobile</h1>
    <?php
    if ( isset($_SESSION['error'])) {
        // Look closely at the use of single and double quotes
        echo('<p style="color: #ff0000;">' .htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <p>Make:
            <input type="text" name="make" value="<?= $mk ?>"/>
        </p>
        <p>Model:
            <input type="text" name="model" value="<?= $md ?>"/>
        </p>
        <p>Year:
            <input type="text" name="year" value="<?= $ye ?>"/>
        </p>
        <p>Mileage:
            <input type="text" name="mileage" value="<?= $mi ?>"/>
        </p>
        <input type="hidden" name="autos_id" value="<?= $autos_id ?>"/>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>

</html>
