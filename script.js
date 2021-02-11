/*global $*/
/*global ImageCompressor*/
/*global atob*/

//var data;
//biary
//var binaryBlob;
//base64
var blob;
var file;

//var s3UrlImage;

var TextoDiv;
//var Lenguage;
var datosFinal = [];
var fracesClaveIndex = [];
var entidadesIndex = [];
var urlS3Index = [];
var etiquetas = [];


//Analizamos y comprimimos de ser necesario

$("#upload").click(function() {

    //loader
    $('#datosFinal').html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
    if ($('#tags').text().length <= 0) {
        $('#tags').html(`
<!-- Etiquetas Inicio -->
<h3>Etiquetas:</h3>
<p>Etiqueta tu archivo para agruparlo o encontrarlo mas facilmente</p>
  <label for="exist-values">Preciona enter entre cada etiqueta.
    <input type="text" id="exist-values" class="tagged form-control" data-removeBtn="true" name="tag-2" value="" placeholder="Add Platform">
  </label>
</form>
<!-- Etiquetas fin -->
<script  src="./tags.js"></script>

    `);
    }
    //  $("#botUpload").html( htmlTemp + '<button class="btn btn-primary" onclick="location.reload();">Nuevo</button>');


    //cargamos las imagenes
    file = document.getElementById("fotoFile").files[0];
    //Comprimimos con ImageCompressor y colvemos a binario.
    var imageCompress = new ImageCompressor(file, {
        quality: .7,
        maxWidth: 4000,
        maxHeight: 4000,
        convertSize: 3000000,
        checkOrientation: false,
        success(result) {
            const formData = new FormData();
            formData.append('file', result, result.name);
            file = result;
            //console.log(file);

            //Convertimos en Base64
            var reader = new FileReader();
            reader.onloadend = function() {
                //console.log('Encoded Base 64 File String:', reader.result);
                //Binary
                //data = (reader.result).split(',')[1];
                //binaryBlob = atob(data);
                //console.log('Encoded Binary File String:', binaryBlob);
                //Base64
                blob = reader.result;
                //console.log(blob);
                //console.log(blob);

                s3upload(blob);

            }
            reader.readAsDataURL(file);
            //console.log(file);

            //Comprimimos y mandamos a subir a s3


        },
        error(e) {
            console.log(e.message);
        },
    });
})


//Subimos al servidor y a s3

function s3upload(binaryBlobData) {
    $.ajax({
            method: "POST",
            url: "s3.php",
            data: {
                binaryData: binaryBlobData
            }
        })
        .done(function(data) {
            //s3UrlImage = data;
            console.log("URL de imagen: " + data);
            setTimeout(function() {
                textract(data);
            }, 2000);
        });
}

//Analizamos con textract para sacar texto

function textract(urlS3) {
    var urlSpace = urlS3.replace(/ /g, "");
    console.log('Url para S3 ' + urlSpace);
    urlS3Index.push(urlSpace);


    $.ajax({
            method: "POST",
            url: "textract.php",
            data: {
                url: urlSpace
            }
        })
        .done(function(data) {
            console.log("Foto analizada OK");
            //console.log(JSON.stringify(data));

            //Creamos el contenedor
            $('#datosFinal').html(`
                <div class="card col-mb-12">
                  <div class="card-header">Corrobora los datos extraidos. Corrigelos si es necesario</div>
                  <div class="row no-gutters">
                    <div class="col-md-5">
                      <img src="#" class="card-img" alt="..." id="imagenAnalizada">
                    </div>
                    <div class="col-md-7">
                      <div class="card-body">
                        <h5 class="card-title">2) Corrobora los Datos obtenidos:</h5>
                        
                        <h5>Texto</h5>
                        <p class="card-text" contenteditable="true" id="textoLineas">
                        </p>
                        
                        <h5>Idioma</h5>
                        <div id="idioma"></div>
                        <h5>Tono</h5>
                        <div id="sentimiento"></div>
                        <h5>Fraces Clave</h5>
                        <div id="fracesClave"></div>
                        <h5>Palabras clave</h5>
                        <div id="entidades"></div>

                          
                        
                        <p class="card-text"><small class="text-muted">
                          Una vez indexado no podras cambiar el contenido
                        </small></p>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="indexButton" class="indexButton"></div>
            `);




            $('#textoLineas').html(data.lineas);
            //$('#textoPalabras').html(data.palabras);
            $('#imagenAnalizada').attr("src", data.imagenUrl);
            analizarTexto(data.lineas);
        });
}



//***********Comprehend*********************//

//Analizamos con comprehend para sacar Palabras clave y entidades.



function analizarTexto(textoExtraido) {

    TextoDiv = $("#textoLineas").text();
    datosFinal.push(TextoDiv);
    //textoExtraido;
    //  .clone() //clone the element
    //    .children() //select all the children
    //      .remove() //remove all the children
    //        .end() //again go back to selected element
    //get the text of element
    //console.log(TextoDiv);

    var texto = {
        'texto': TextoDiv,
        'step': 1
    };
    $('#idioma').html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
    $('#sentimiento').html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
    $('#fracesClave').html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
    $('#entidades').html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
    $.ajax({
        url: "comprehend.php",
        type: "POST",
        dataType: "json",
        data: texto,
        success: function(result) {
            //console.log('stepOne:' + result);
            var idiomaResult = result[0].LanguageCode;
            console.log('idioma:' + result);
            //Lenguage = result;
            $('#idioma').html('');
            for (var i = 0; i <= (result.length - 1); i++) {
                $('#idioma').append('<span>' + result[i].LanguageCode + '</span>');
            }



            stepTwo(idiomaResult);
        }
    }).done(function(data, textStatus, jqXHR) {
        if (console && console.log) {
            console.log("StepOne OK");
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        if (console && console.log) {
            console.log("StepONE Fail: " + textStatus);
        }
    });
}

function stepTwo(idioma) {

    var idiomaVar = {
        'idioma': idioma,
        'step': 2
    };

    $.ajax({
        url: "comprehend.php",
        type: "POST",
        dataType: "json",
        data: idiomaVar,
        success: function(result) {
            var SentimientoTexto = result[0].Sentiment;


            var porcent = (result[0].SentimentScore.Positive.toFixed(3)) * 100;
            $('#sentimiento').html('<div class="container"><span>' + SentimientoTexto +
                ' </span><div class="row" id="Barras"><div class="col"><span style="text-align:right; width:100%;÷\
                    background: rgb(98,16,117); background: linear-gradient(90deg, rgba(16,200,16,1) 0%, rgba(16,200,16,1) ' + porcent +
                '%, rgba(150,150,150,1) ' + porcent + '%, rgba(170,170,200,1) 100%); color:white;">Positivo:' +
                porcent + '%</span></div></div></div>');

            porcent = (result[0].SentimentScore.Neutral.toFixed(3)) * 100;
            $('#Barras').append('<div class="col"><span style="text-align:right; width:100%;÷\
                    background: rgb(98,16,117); background: linear-gradient(90deg, rgba(111,111,111,1) 0%, rgba(111,111,111,1) ' + porcent +
                '%, rgba(150,150,150,1) ' + porcent + '%, rgba(170,170,200,1) 100%); color:white;">Neutro:' +
                porcent + '%</span></div>');
            porcent = (result[0].SentimentScore.Negative.toFixed(3)) * 100;
            $('#Barras').append('<div class="col"><span style="text-align:right; width:100%;÷\
                    background: rgb(98,16,117); background: linear-gradient(90deg, rgba(255,0,0,1) 0%, rgba(255,0,0,1) ' + porcent +
                '%, rgba(150,150,150,1) ' + porcent + '%, rgba(170,170,200,1) 100%); color:white;">Negativo:' +
                porcent + '%</span></div>');
            stepThree();
        }
    }).done(function(data, textStatus, jqXHR) {
        if (console && console.log) {
            console.log("StepTwo OK");
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        if (console && console.log) {
            console.log("StepTwo Fail: " + textStatus);
        }
    });
}


function stepThree() {

    var justStep = {
        'step': 3
    };

    $.ajax({
        url: "comprehend.php",
        type: "POST",
        dataType: "json",
        data: justStep,
        success: function(result) {
            $('#fracesClave').html('');
            var TextoAcumulado = TextoDiv;
            //var styleAcumulado = '';

            for (var i = 0; i <= (result.length - 1); i++) {
                //agregamos todo a un array
                fracesClaveIndex.push(result[i].Text);
                $('#fracesClave').append('<span>' + result[i].Text + '</span>  - ');
                //Damos Formato al texto
                $('#TextInput').html(TextoAcumulado);
            }
            $('#fracesClave span').click(function() {
                $(this).toggleClass("selected");
                var sui = $(this).data("id");
                console.log(sui);
                $(sui).toggleClass('selected');
            });

            stepFour();
        }
    }).done(function(data, textStatus, jqXHR) {
        if (console && console.log) {
            console.log("StepThree OK");

        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        if (console && console.log) {
            console.log("StepThree Fail: " + textStatus);
        }
    });
}



function stepFour() {

    var justStep = {
        'step': 4
    };

    $.ajax({
        url: "comprehend.php",
        type: "POST",
        dataType: "json",
        data: justStep,
        success: function(result) {
            $('#entidades').html('<div class="row" id="rowEntidades"></div>');
            $('#indexButton').html(`
            <button class="btn btn-primary" onclick="indexar();">Terminar</button>
            <button class="btn btn-primary" onclick="window.scrollTo(0, 0);">Siguiente página</button>
            <button class="btn btn-primary" onclick="location.reload();">Cancelar</button>
            `);
            $('#Instruccion').html('Analizar siguiente página');



            for (var i = 0; i <= (result[0].Entities.length - 1); i++) {
                entidadesIndex.push(result[0].Entities[i]);

                $('#rowEntidades').append(`
                     <span> ${result[0].Entities[i].Text}
                     <small>(${result[0].Entities[i].Type})</small></span> - 
                    `);
            }

        }
    }).done(function(data, textStatus, jqXHR) {
        if (console && console.log) {
            console.log("StepFour OK");
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        if (console && console.log) {
            console.log("StepFour Fail: " + textStatus);
        }
    });
}



function indexar() {
    //htmlContent
    var etiquetasArray = tags.getTags();

    etiquetas = etiquetas.concat(etiquetasArray);
    console.log(etiquetas);

    $.ajax({
            method: "POST",
            url: "indexar.php",
            data: {
                images: urlS3Index,
                frasesClave: fracesClaveIndex,
                entidades: entidadesIndex,
                textoCompleto: datosFinal,
                tags: etiquetas
            }
        })
        .done(function(data) {
            console.log("Datos Indexados y subidos a S3");
            $('#datosFinal').html('<h3>¡Terminaste! ¿Indexamos otro archivo?</h3> <br/> Link del archivo: <a href="' +
                data + '" target="_blank">' + data + "</a>");
            datosFinal = [];
            fracesClaveIndex = [];
            entidadesIndex = [];
            urlS3Index = [];
            etiquetas = [];
            $("#tags").html("");
            alert('Su archivo fue indexado');


            //console.log(JSON.stringify(data));


        });

}
