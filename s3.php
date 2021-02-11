   <?php


session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');


require 'aws.phar';

require './config.php';

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
$webPathFoto = "./images/{$imageID}.{$extension}";
$imageData = base64_decode($imageData);
file_put_contents($fileFullName, $imageData);
    






use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;




$keyname = $webPathFoto;
// $filepath should be absolute path to a file on disk                      
$filepath = date('ymd').'/'.uniqid().'.'.$extension;



// Instantiate the client.
$s3 = S3Client::factory(array(
    'version' => 'latest',
    'region'  => 'us-east-1',
    'credentials' => array(
        'key' => $awsAccessKeyId,
        'secret'  => $awsSecretKey,
    )
    ));

try {
    // Upload data.
    $result = $s3->putObject(array(
        'Bucket' => $bucket,
        'Key'    => $keyname,
        'SourceFile'   => $webPathFoto,
        'ACL'    => 'public-read',
        'ContentType' => 'image/jpeg'
    ));

    // Print the URL to the object.
    echo $result['ObjectURL'];
    

    
    
} catch (S3Exception $e) {
    echo $e->getMessage() . "\n";
}






?>