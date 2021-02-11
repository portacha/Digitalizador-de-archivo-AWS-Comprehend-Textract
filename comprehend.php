<?php
header('Content-type: application/json; charset=utf-8');


session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
//Aqui ponemos el texto a analizar


#Ocupamos el autoloader de aws.phar para cargar las clases
require './aws.phar';
//Declaramos las claves
require './config.php';
$credentials    = new \Aws\Credentials\Credentials($awsAccessKeyId, $awsSecretKey);

//Creamos un cliente de comprehend en la region y vercion correspondiente
$client_comprehend  = new \Aws\Comprehend\ComprehendClient([
  'version'     => 'latest',
  'credentials' => $credentials,
  'region'      => 'us-east-1'
]);


function lenguage(){

    $lenguage = $GLOBALS["client_comprehend"]->detectDominantLanguage([
        'Text' => $_SESSION['Texto'], // REQUIRED
    ]);
    $result = $lenguage ->get('Languages');
    echo json_encode ($result, JSON_PRETTY_PRINT);
}



function frasesClave(){
// Agregamos los campos a analizar
    $resFrases = $GLOBALS["client_comprehend"]->detectKeyPhrases([
        'LanguageCode' => $_SESSION['idioma'], // Lenguaje
        'Text' => $_SESSION['Texto'], // REQUIRED
    ]);
    //Damos el formato json a regresar en ajax
    $result = $resFrases->get('KeyPhrases');
     echo json_encode ($result, JSON_PRETTY_PRINT);
    //echo $resFrases; //Regresamos todo
}

function sentimientos(){
    $sentimientos = $GLOBALS["client_comprehend"]->batchDetectSentiment([
        'LanguageCode' => $_SESSION['idioma'], // REQUIRED
        'TextList' => [$_SESSION['Texto']], // REQUIRED
    ]);
    $result = $sentimientos->get('ResultList');
    echo json_encode ($result, JSON_PRETTY_PRINT);
    //echo $sentimientos;
    
}


function entidades(){
    $entidades = $GLOBALS["client_comprehend"]->batchDetectEntities([
    'LanguageCode' => $_SESSION['idioma'], // REQUIRED
    'TextList' => [$_SESSION['Texto']], // REQUIRED
    ]);
    $result = $entidades->get('ResultList');
    echo json_encode ($result, JSON_PRETTY_PRINT);
    //echo $result; ResultList
}




if($_REQUEST['step'] == 1){
$_SESSION['Texto'] = $_REQUEST['texto'];
lenguage();
}elseif($_REQUEST['step'] == 2){
$_SESSION['idioma'] = $_REQUEST['idioma'];
sentimientos();
}elseif($_REQUEST['step'] == 3){
frasesClave();
}elseif($_REQUEST['step'] == 4){
entidades();
}


?>
