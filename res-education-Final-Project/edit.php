<?php
//Se agrega las siguientes utilidades
require_once "pdo.php";//Contiene la conexión a la base de datos MySQL
require_once "util.php";//Especie de librería que contiene distintas funciones

session_start();
//Se revisa si se inicio sesión
if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
}
//Se redirige si se cancela
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}
// Guardian: Make sure that profile_id is present
if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
//It retrieves the data from the database
$stmt = $pdo->prepare("SELECT * FROM profile 
        where profile_id = :prof AND user_id = :uid");
$stmt->execute(array(":prof" => $_REQUEST['profile_id'],
    ":uid"=>$_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
//In case it does not retrieve the profile
if ( $profile === false ) {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
}
// Handle the incoming data
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) &&
    isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {
    //It validates the form
    $msg = validateProfile();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
    //Validate position entries if present
    $msg = validatePos();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
    //Se usa una sentencia SQL para actualizar la base de datos
    $sql = "UPDATE profile SET first_name = :first_name,
            last_name = :last_name, email = :email, 
            headline = :headline, summary = :summary
            WHERE profile_id = :profile_id AND user_id=:uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary'],
            ':profile_id' => $_REQUEST['profile_id'],
            ':uid' => $_SESSION['user_id'])
    );

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
    //Insert the positions entries
    insertPositions($pdo, $_REQUEST['profile_id']);

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Education
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
    //Insert the education entries
    insertEducations($pdo, $_REQUEST['profile_id']);


    /*En caso de que todo haya ido bien se actualiza el perfil*/
    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;

}

//Se usa htmlentities para evitar htm injection
$fn = htmlentities($profile['first_name']);
$ln = htmlentities($profile['last_name']);
$em = htmlentities($profile['email']);
$he = htmlentities($profile['headline']);
$su = htmlentities($profile['summary']);
$profile_id = $profile['profile_id'];
//Load Positions función ubicada en util.php
$positions = loadPos($pdo, $_REQUEST['profile_id']);
//Load Educations función ubicada en util.php
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kevin Dario Simbaña Cusicagua</title>
    <?php require_once "head.php"?>
</head>
<body>
<div class="container">
    <h1>Editing Profile <?php echo $_SESSION['name']; ?> </h1>
    <?php flashMessages();//Para mostrar los mensajes de las validaciones ?>
    <form method="post">
        <p>First Name:
            <input type="text" name="first_name" size="60" value="<?= $fn ?>"/></p>
        <p>Last Name:
            <input type="text" name="last_name" size="60" value="<?= $ln ?>"/></p>
        <p>Email:
            <input type="text" name="email" size="30" value="<?= $em ?>"/></p>
        <p>Headline:<br/>
            <input type="text" name="headline" size="80" value="<?= $he ?>"/></p>
        <p>Summary:<br/>
            <textarea name="summary" rows="8" cols="80" t><?= $su ?></textarea>
        <?php
        //Contador para ubicar las nuevas entradas se usa Jquery
        $countEdu = 0;
        echo ('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
        echo ('<div id="edu_fields">'."\n");
        if (count($schools) > 0){
            foreach ($schools as $school){
                $countEdu++;
                $ye=htmlentities($school['year']);
                $name=htmlentities($school['name']);
                echo('<div id="edu'.$countEdu.'">'."\n");
                echo('<p>Year: <input type="text" name="edu_year'.$countEdu.'"');
                echo(' value="'.$ye.'" />'."\n");
                echo('<input type="button" value="-" ');
                //Se usa jquery para eliminar el div
                echo('onclick="$(\'#edu'.$countEdu.'\').remove();return false;">'."\n");
                echo("</p>\n");
                echo ('<p>School: <input type="text" size="80" name="edu_school'.$countEdu.'"');
                echo (' class="school" value="'.$name.'" />');
                echo("\n</div>\n");
            }

        }
        echo("</div></p>\n");
        //Contador para ubicar las nuevas entradas se usa Jquery
        $pos = 0;
            echo ('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
            echo ('<div id="position_fields">'."\n");
            if( count($positions) > 0){
                foreach ($positions as $position){
                    $pos++;
                    $ye=htmlentities($position['year']);
                    $des=htmlentities($position['description']);
                    echo('<div id="position'.$pos.'">'."\n");
                    echo('<p>Year: <input type="text" name="year'.$pos.'"');
                    echo(' value="'.$ye.'" />'."\n");
                    echo('<input type="button" value="-" ');
                    //Se usa jquery para eliminar el div
                    echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
                    echo("</p>\n");
                    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
                    echo($des."\n");
                    echo("\n</textarea>\n</div>\n");
                }
            }
            echo("</div></p>\n");
        ?>

        <p>
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>"/>
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </p>
    </form>
    <script>
        //Contadores para armar los divs con las nuevas entradas
        countPos=<?= $pos ?>;
        countEdu=<?= $countEdu?>;
        // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
        // Se usa jquery para cargar dinámicamente las nuevas entradas
        $(document).ready(function(){
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event){
                // http://api.jquery.com/event.preventdefault/
                event.preventDefault();
                //Solo se permite agregar un maximo de 9 entradas
                if ( countPos >= 9 ) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position "+countPos);
               //Se usa jquery para aumentar y eliminar nuevos divs dinámicamente.
                //Se activa cuando se presiona los botones + o -
                $('#position_fields').append(
                    '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
            });
            //Similar al procedimiento anterior pero se usa un template
            $('#addEdu').click(function(event){
                event.preventDefault();
                if ( countEdu >= 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                countEdu++;
                window.console && console.log("Adding education "+countEdu);

                // Grab some HTML with hot spots and insert into the DOM
                var source  = $("#edu-template").html();
                $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

                // Add the even handler to the new ones
                $('.school').autocomplete({
                    source: "school.php"
                });

            });

            $('.school').autocomplete({
                source: "school.php"
            });

        });


    </script>
    <!-- HTML with Substitution hot spots -->
    <script id="edu-template" type="text">
    <div id="edu@COUNT@">
        <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
        <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
    </p>
    </div>
    </script>
</div>
</body>
</html>
