<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
if(!isset($_SESSION["userName"])) {
header('Location: login.php');

} else {

    include './config.php'; 
     if (isset($_REQUEST['tags'])){
        //FILTRAMOS POR TAG
        //$searchWords = $_REQUEST['search'];
        $indexFile = '$**';
        require 'vendor/autoload.php';
        $client = new MongoDB\Client($dbURL);
        $db = $client->$database;
        //$index = $db->$collection->createIndex( [ $indexFile => "text"],[ 'default_language'=> "spanish"] );
        //$search['$text'] = ['$search' => ($searchWords)];
        //busqueda segmentada por fecha
        $search['user'] = $_SESSION["userName"];
        $tagsArray = explode(",",$_REQUEST["tags"]);
        $search['tags'] = ['$all' => $tagsArray];
        //$options["projection"] = ['score' => ['$meta' => "textScore"]];
        //$options["sort"] = ["score" => ['$meta' => "textScore"]];
        //$cursor = $db->$collection->find($search, $options);
        $cursor = $db->$collection->find($search);
    
        $respuesta = array();
        foreach ($cursor as $doc) {
            array_push($respuesta, $doc);
        }
        
        $titlulo = "Filtrando por:".$_REQUEST['tags'].'"';
        
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
         $titlulo = "";
         //MOSTRAMOS TODO
        //$searchWords = $_REQUEST['search'];
        $indexFile = '$**';
        require 'vendor/autoload.php';
        $client = new MongoDB\Client($dbURL);
        $db = $client->$database;
        //$index = $db->$collection->createIndex( [ $indexFile => "text"],[ 'default_language'=> "spanish"] );
        //$search['$text'] = ['$search' => ($searchWords)];
        //busqueda segmentada por fecha
        $search['user'] = $_SESSION["userName"];
        //$options["projection"] = ['score' => ['$meta' => "textScore"]];
        //$options["sort"] = ["score" => ['$meta' => "textScore"]];
        //$cursor = $db->$collection->find($search, $options);
        $cursor = $db->$collection->find($search);
    
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
            <h1 class="titlulo"><?php echo $titlulo ?></h1>
            <div>
                <link rel="stylesheet" href="./tagsStyle.css">
                <!-- Etiquetas Start-->
                  <label for="exist-values">Filtrar por:
                    <input type="text" id="exist-values" class="tagged form-control" data-removeBtn="true" name="tag-2" value="" placeholder="Etiquetas">
                  </label>
                  <div id="fitrar" class="btn btn-success">Filtrar</div>
                  <div id="tagsFilter" style="display: flex;"></div>
                <!--Etiquetas end -->
                  <script  src="./tags.js"></script>
            </div>
            
            <!--aqui empieza el for-->
            
            <?php 
            $allTags = array();
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
                $tags = "";
                
                for($l=0 ; count($respuesta[$i]->tags)-1 >= $l ; $l++){
                    $tags .= $respuesta[$i]->tags[$l].",";
                    array_push($allTags,$respuesta[$i]->tags[$l]);
                }
                
                
                echo '<div class="documentos" data-tags="'.$tags.'" data-mongoid="'.$respuesta[$i]->_id.'" >
                <div class="row">
                    <div class="col-md-12">
                      <p>ID: <b>'.substr($respuesta[$i]->textocompleto[0],$oficioPos,$caracteres).'</b><br/>
                        Contenido: '. 
                        substr($respuesta[$i]->textocompleto[0],0,50) .
                        '<span class="viewMore"> <a>ver más</a><more class="more">'
                        .substr($respuesta[$i]->textocompleto[0],50,-1)
                        .'</more><a class="more"> ver menos</a></span><small><br/>Etiquetas: ['.$tags.']</small>
                        </p>
                        <a class="open" href="'. $respuesta[$i]->liga.'" target="_blank">Abrir</a>
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
            
            document.getElementById('fitrar').addEventListener('click', function(e) {

              var tag = tags.getTags();
              if (tag.length > 0){
              window.location.href= ("archivo.php?tags="+tag);
                  
              }else{
                window.location.href= ("archivo.php");
                  
              }
            });
            
            <?php 
            $tagsString = "";
            if(isset($tagsArray)){
                for ($i = 0; $i < count($tagsArray); $i++) {
                    $tagsString .= '"'.$tagsArray[$i].'",';
                }
                
            echo '(function() {
            tags.addTags(['.$tagsString.']);
            })();
            console.log(['.$tagsString.']);
            ';
            }
            
            $unicTags = array_unique($allTags);
            echo 'var tagsFilter =`'; 
            foreach ($unicTags as $clave => $valor) {
                echo '<div class="tag" onclick="tags.addTags([\''.$valor.'\'])"><span class="tag__name">'.$valor.'</span></div>';
            }
            echo '`;';
            ?>
            $('#tagsFilter').html(tagsFilter);
        </script>

    </body>
    
</html>


