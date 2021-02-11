

<?php
//Respondemos en json

//Login script header
/*
if(!isset($_SESSION["userName"])) {
header('Location: login.php');
} else {
    
}
*/
session_start();
echo $_SESSION["userName"];

if(isset($_SESSION["userName"])) {
    header('Location: index.php');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
$errorMessage = "";

 if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])){
    //Configuramos
    include './config.php';
    
    $user = $_REQUEST['user'];
    $password = $_REQUEST['pass'];
    
    
    
    //Importamos librerias y creamos el objeto
    require 'vendor/autoload.php';
    $client = new MongoDB\Client($dbURL);
    $db = $client->$database;
    
    //buscamos una coincidencia con el usuario
    $cursor = $db->$collectionUser->findOne(['user' => $user]);
    
    if ($cursor == null){
    $errorMessage = "no se encontro al usuario";
    }else{
        if ($cursor->pass == $password){
        
        $_SESSION["userName"] = $cursor->user;
        $_SESSION["userLoginTime"] = date(DATE_RFC2822);
        $_SESSION["departamento"] = $cursor->dep;
        echo $_SESSION["userName"];
        header('Location: index.php');
        
        //echo json_encode($cursor, JSON_PRETTY_PRINT); 
        }else{
            $errorMessage = "Contraseña equivocada";
        }
    
    }

}


?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Login - Archivo UDEMEX</title>
  <link rel="stylesheet" href="./styleLogin.css">
  <meta name="viewport" content="width=device-width, user-scalable=no">


</head>
<body>
<!-- partial:index.partial.html -->
<div class="app">

		<div class="bg"></div>

		<form action="login.php" method="post">
			<header>
				<img src="https://www.udemex.edu.mx/images/UDEM_1_1_1.jpg">
			</header>

			<div class="inputs">
				<input type="text" name="user" placeholder="usuario" <?php if (isset($_REQUEST['user'])){ echo "value='". $user."'"; }  ?> required>
				<input type="password" name="pass" placeholder="contraseña" required>

				<p class="light"><a href="#">Ayuda</a><br/>
				<span style="color:red;"><?php echo $errorMessage;?></span></p>
			</div>

		

		<footer>
			<button type="submit">Ingresar</button>
			<p>¿No tienes cuenta? <a href="#">Registro</a></p>
		</footer>
		</form>


	</div>
<!-- partial -->
  
</body>
</html>


