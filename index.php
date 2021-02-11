<!doctype html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
       <?php 
       session_start();
       include "head.html" ;
       if(!isset($_SESSION["userName"])) {
       header('Location: login.php');
      }
       
       ?>
        <title>Archivo UDEMEX!</title>
            <link rel="stylesheet" href="./tagsStyle.css">
    </head>

    <body>
        <!-- Menu  -->
        <?php include "menu.php"; ?>

        <!-- Contenido Inicio -->
        <div class="container">
            <h1 class="titlulo">DIGITALIZA TU ARCHIVO UDEMEX</h1>
            <div class="row">
                
                <div class="card mb-5">
                  <div class="card-header">Selecciona una foto o imagen para indexar.</div>
                  <div class="row no-gutters">
                    <div class="col-md-12">
                      <div class="card-body">
                        <h5 class="card-title" id="Instruccion">1) Analiza tu imagen</h5>
                        <p class="card-text" id="botUpload">
                          <label for="fotoFile">Selecciona tu archivo</label><br/>
                          <input id="fotoFile" type="file" name="myImage" accept="image/x-png,image/jpeg" />
                          <br/>
                          <button class="btn btn-primary" id="upload">Analizar</button>
                        </p>
                        <p class="card-text"><small class="text-muted">Puedes subir imagenes en jpg y png.Sube una foto lo mas enfocada posible. <br><a href="#">Más información </a></small></p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            
            
            
            
            <div id="tags"></div>
            <div class="row" id="datosFinal">

            </div>
            
            
            
        </div>
        
        
        <!-- Fin del Contenido -->


        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script type="text/javascript" src="./script.js"></script>
        

    </body>

</html>
