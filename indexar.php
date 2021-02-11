   <?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


require './config.php';


if(!isset($_SESSION["userName"])) {
header('Location: login.php');
} else {


require 'aws.phar';

$images = $_REQUEST['images'];
$printImage = "";
foreach($images as $image){
    $printImage .= "<img src='".$image."'><br/>";
}

if(!isset($_REQUEST['frasesClave']) ){
$frases = array("SinFracesClave");
}else{
$frases = $_REQUEST['frasesClave'];
}

if(!isset($_REQUEST['tags']) ){
$tags = array("SinTags");
}else{
$tags = $_REQUEST['tags'];
}

$entidades = $_REQUEST['entidades'];
$textoCompleto = $_REQUEST['textoCompleto'];
$liga;
$data1 = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>'.
    $printImage .
    '</body></html>';

$data = $data1;
//Subimos a S3 como html

$keyname = 'produccion/'.date('y-m-d').'/'.date('h-i-s-A').'-'.uniqid().'.html';
                        

$s3 = new S3Client([
    'region' => 'us-east-1',
	'version' => 'latest',
	'credentials' => [
        'key'    => $awsAccessKeyId,
        'secret' => $awsSecretKey
	]
]);


try {
    // Upload data.
    $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $keyname,
        'Body'   => $data,
        'ContentType' => 'text/html',
        'ACL'    => 'public-read'
    ]);

    // Print the URL to the object.
    echo $result['ObjectURL'] . PHP_EOL;
    $liga = $result['ObjectURL'];
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}



//Subimos a la base de datos

require 'vendor/autoload.php';
include './config.php';
$client = new MongoDB\Client($dbURL);
//Configuramos

$db = $client->$database;
//echo 'conectado a '. $database .' '.$collection.'<br/>' ;




$insertManyResult = $db->$collection->insertMany([
         ['liga'=> $liga, 'urls'=> $images,
         'textocompleto'=> $textoCompleto, 
         'texto'=> $frases, 'entidades'=> $entidades, 
         'user' => $_SESSION["userName"],
         'dep' => $_SESSION["departamento"],
         'tags' => $tags,
         'fecha' => date('y-m-d||h-i-s-A')
]]);






//Subimos a S3

    
}


?>