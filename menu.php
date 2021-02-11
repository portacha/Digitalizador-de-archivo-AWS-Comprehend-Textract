<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
    <a class="navbar-brand" href="index.php">Digitalizar</a>
    <a class="navbar-brand" href="archivo.php">Archivo</a>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
        <!--<ul class="navbar-nav mr-auto mt-2 mt-lg-0">-->
        <!--    <li class="nav-item">-->
        <!--        <a class="nav-link" href="#">Mi archivo</a>-->
        <!--    </li>-->
        <!--    <li class="nav-item">-->
        <!--        <a class="nav-link" href="#">Crear Oficio</a>-->
        <!--    </li>-->
        <!--    <li class="nav-item">-->
        <!--        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Graficas</a>-->
        <!--    </li>-->
        <!--</ul>-->
        <form class="form-inline my-2 my-lg-0" action="search.php" method="get">
            <input class="form-control mr-sm-2" name="search" type="search" 
            <?php 
            if (isset($_REQUEST['search'])){echo "value='".$_REQUEST['search']."'";}else{echo "placeholder='ej. Covid 19'";}; 
            ?>
            aria-label="Buscar">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
        </form>
    </div>
    <a class="navbar-brand" href="exit.php">Salir</a>
</nav>
