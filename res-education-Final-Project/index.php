<!--Pagina que se muestra al principio-->
<?php
//Se agrega las siguientes utilidades
require_once "pdo.php";//Contiene la conexión a la base de datos MySQL
require_once "util.php";//Especie de librería que contiene distintas funciones
session_start();//Se inicia sesión
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kevin Dario Simbaña Cusicagua</title>
    <?php require_once "head.php"?>

</head>
<body>
<div class="container">
    <h1>Kevin Simbana's Resume Registry</h1>
    <p>
        <?php
        //Se revisa si se inicio sesión
        if ( ! isset($_SESSION['name']) ) {
            echo ('<a href="login.php">Please Log In</a>');
        }else{
            //It shows success flash messages, messages que vienen de distintas operations
            flashMessages();
            echo('<a href="logout.php">Logout</a>');
            //Retrieves information from the database para armar la tabla de perfiles
            $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id  FROM profile");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //En caso de que no exista información
            if ($rows == false) {
                echo('<p>No rows found</p>');
            } else {
                //Se arma la tabla con los elementos necesarios
                echo('<table border="1">' . "\n");
                echo('<tr><th>Name</th><th>Headline</th><th>Action</th></tr>');
                foreach ($rows as $row) {
                    echo "<tr><td>";
                    echo(htmlentities($row['first_name']." ".$row['last_name']));
                    echo("</td><td>");
                    echo(htmlentities($row['headline']));
                    echo("</td><td>");
                    //Links que redirigen a otras páginas para editar y borrar los datos
                    echo('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                    echo('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                    echo("</td></tr>\n");
                }
                echo('</table>');
            }
            //Para agregar una nueva entrada
            echo('<a href="add.php">Add New Entry</a><br><br>');

            }
        ?>
    </p>


</div>
</body>
</html>
