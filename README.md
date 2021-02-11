# Digitalizador-de-archivo
Es un digitalizador o escaner de archivo que usa aws (comprehend, textract y s3). Desarrollado en: php, js, mongodb, bootstrap, jquery.

Para probarlo entra a http://transformacion.udemex.edu.mx/Textract/
user: prueba
pass: 1234

Para instalar necesitas instalar npm y composer.

1.- Instala bootstrap y jquery con npm.

2.- Instala mongodb con composer.

3.- copia y pega en raiz de la aplicacion el archivo de SDK de aws.phar.

https://docs.aws.amazon.com/aws-sdk-php/v3/download/aws.phar .

4.- Ingresa los datos necesarios en el archivo de configuracion /config.php.

5.- Recuerda darle permisos a tu bucket para mostrar los archivos. Puedes hacerlo dandoles acceso publico pero recuerda que cualquiera con la liga podra entrar.

6.- crea tu usuario en mongodb para acceder usando esta estructura.
{"_id":{"$oid":"***"},"user":"USER","correo":"mail@ejemplo.com","pass":"1234","dep":"DEPARTAMENT"}

Por ahora el sistema esta configurado para correr en us-east-1 para cambiarlo solo cambia en los archivos . En la siguiente actualizacion estará en el archivo config.php 
//Sorry.

¿Como funciona?
Selecciona una imagen PNG o JPG.
El sistema lo va a comprimir con image-compressor.js y lo subirá al servidor.
Seguido la imagen sera subida a S3 y analizada por textract, para extraer todo el texto que contenga la imagen.
Despues estos datos los pasaremos por comprehend para Sacar datos como: palabras clave, entidades, sentimientos e idioma.
Despues se muestra todo esto en el front end y se aprueba y etiqueta por el usuario.
Al final si es aprovado por el usuario se pasa a indexar el archivo lo cual creará un archivo html con todos los legajos de este documento. Se agregará a mongodb la informacion en texto plano y se guardara junto con la url del archivo (ObjectURL) de S3.
