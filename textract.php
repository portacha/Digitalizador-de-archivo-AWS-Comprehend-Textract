<?php
//header('Content-type: text/html; charset=UTF-8');
header('Content-type: application/json; charset=utf-8');


require './aws.phar';
use Aws\Credentials\CredentialProvider;
use Aws\Textract\TextractClient;


require './config.php';


// If you use CredentialProvider, it will use credentials in your .aws/credentials file.
$client = new TextractClient([
    'region' => 'us-east-1',
	'version' => '2018-06-27',
	'credentials' => [
        'key'    => $awsAccessKeyId,
        'secret' => $awsSecretKey
	]
]);

//Errores _ On
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');


/*
//Si es Archivo Local
$filename = "oficio-3.jpg";
$file = fopen($filename, "rb");
$contents = fread($file, filesize($filename));
fclose($file);
$imgData = base64_encode(file_get_contents($filename));
$src = 'data: '. mime_content_type($filename).';base64,'.$imgData;
*/

//Si es URL Comentar este bloque y descomentar el de archivo
if(isset($_REQUEST['url'])){
  $urlFile = $_REQUEST['url'];
}else{
trigger_error("No se proporciono URL", E_USER_ERROR);
}


$contents = file_get_contents($urlFile);


$imgData = base64_encode(file_get_contents($urlFile));
$src = $urlFile;


//Configuracion de Textract
$options = [
    'Document' => [
		'Bytes' => $contents
    ],
    'FeatureTypes' => ['FORMS','TABLES'], // REQUIRED
];







$result = $client->analyzeDocument($options);
// If debugging:
// echo print_r($result, true);
$blocks = $result['Blocks'];
// Loop through all the blocks:
$count = 0;
$countW = 0;

//echo json_encode ($blocks, JSON_PRETTY_PRINT);



// Echo out a sample image
//echo '<div style="text-align: center;"><img src="'.$src.'"> <br/> ';


$lines = '';
$words = '';

//            'BlockType' => 'KEY_VALUE_SET|PAGE|LINE|WORD|TABLE|CELL|SELECTION_ELEMENT'
foreach ($blocks as $key => $value) {
    
	if (isset($value['BlockType']) && $value['BlockType']) {
		$blockType = $value['BlockType'];
		if (isset($value['Text']) && $value['Text']) {
			$text = $value['Text'];
			if ($blockType == 'WORD') {
				$words .=  print_r($text, true) . " - ";
			} else if ($blockType == 'LINE') {
				$lines .= print_r($text, true) . " <br/> ";
			}
		}
	}
}


//echo $lines;
//echo $words;

//echo "</div>";

$jsondata = array();


$jsondata['lineas'] = $lines;
$jsondata['palabras'] = $words;
$jsondata['imagenUrl'] = $src;



echo json_encode($jsondata, JSON_PRETTY_PRINT);

?>