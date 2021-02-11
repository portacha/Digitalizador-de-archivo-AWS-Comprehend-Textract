   <?php


session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');



$data = $_REQUEST['binaryData'];


//Subimos a el server


$imageData = $data;
$PatchFoto = "./images/";
//$PatchFoto = "images/{$usuarioCarpeta}/img.{$type}";
if (!file_exists($PatchFoto)) {
    mkdir($PatchFoto, 0777, true);
}
list($type, $imageData) = explode(';', $imageData);
list(,$extension) = explode('/',$type);
list(,$imageData)      = explode(',', $imageData);
$imageID = uniqid();
$fileName = "{$imageID}.{$extension}";
$fileFullName = "{$PatchFoto}{$imageID}.{$extension}";
//echo 'Guardando en:'.$fileFullName;
$webPathFoto = "images/{$imageID}.{$extension}";
$imageData = base64_decode($imageData);
file_put_contents($fileFullName, $imageData);


echo $webPathFoto;



?>