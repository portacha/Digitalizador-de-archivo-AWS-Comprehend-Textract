<?php
session_start();
if(!isset($_SESSION["userName"])) {
header('Location: login.php');
} else {

 if (isset($_REQUEST['search'])){
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    include './config.php'; 
    $searchWords = $_REQUEST['search'];
    $indexFile = '$**';
    require 'vendor/autoload.php';
    $client = new MongoDB\Client($dbURL);
    $db = $client->$database;
    $index = $db->$collection->createIndex( [ $indexFile => "text"],[ 'default_language'=> "spanish"] );
    $search['$text'] = ['$search' => ($searchWords)];
    //busqueda segmentada por fecha
    $search['user'] = $_SESSION["userName"];
    $options["projection"] = ['score' => ['$meta' => "textScore"]];
    $options["sort"] = ["score" => ['$meta' => "textScore"]];
    $cursor = $db->$collection->find($search, $options);
    $respuesta = array();
    foreach ($cursor as $doc) {
        array_push($respuesta, $doc);
    }
    //echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
    
    //buscamos las posiciones de las busquedas


     /*
     Busqueda por filtro
     
    { author: "xyz", $text: { 
        $search: "coffee bake" } 
    },
    { score: { 
        $meta: "textScore" 
    } }
     */
     
     
     
 }else{
 }
}
?>

<!doctype html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
       <?php include "head.html" ?>
        <title>Buscador de archivo UDEMEX!</title>
    </head>

    <body>
                <!-- Menu  -->
        <?php include "menu.php"; ?>
        <!-- Contenido Inicio -->
        <div class="container">
            <h1 class="titlulo">Buscando por: "<?php echo $_REQUEST['search']; ?>"</h1>
            
            <!--aqui empieza el for-->
            
            <?php 
            
            for($i=0 ; count($respuesta)-1 >= $i ; $i++){
                $caracteres = 31;
                $oficioPos = strpos(strtolower($respuesta[$i]->textocompleto[0]), 'oficio no') ;
                if ($oficioPos <= 0){
                    $caracteres = 23;
                    $oficioPos = strpos(strtolower($respuesta[$i]->textocompleto[0]), 'circular no') ;
                    if ($oficioPos <= 0){
                        $oficioPos = 0;
                        $caracteres = 0;
                    }
                    
                }
                
                echo '<div class="card" style="width: 100%; margin: 16px 0px;">
                <div class="row">
                <div class="col-md-5">
              <div class="box imagenBox pull-left" style="
                background-image:url('. $respuesta[$i]->urls[0] .');
                "></div>
                </div>
                <div class="col-md-7">
              <div class="card-body">
              <p>Score: <b>'.$respuesta[$i]->score.'</b></p>
              <p>ID: <b>'.substr($respuesta[$i]->textocompleto[0],$oficioPos,$caracteres).'</b></p>
                <p class="card-text">'. 
                substr($respuesta[$i]->textocompleto[0],0,200) .
                '<span class="viewMore"> <a>ver más</a><more class="more">'.substr($respuesta[$i]->textocompleto[0],200,-1).'</more><br/><a class="more"> ver menos</a></span>
                </p>
              </div>
              <div class="card-body">
                <a href="'
                . $respuesta[$i]->liga.'" target="_blank">Abrir</a>
                
              </div>
              </div>
              </div>
            </div>';
                
            }
            
            ?>
            
            
            
            
            <!--aqui termina el for-->
        </div>



        <script>
        
          $(".viewMore").click(function(){
            $(this).find('more').toggleClass( "more" );
            $(this).find('a').toggleClass( "more" );
            $(this).find('less').toggleClass( "more" );
          });
        
        
            $('.box').backgroundMove({
                movementStrength:'5000'
            });
        </script>

    </body>
    
</html>


