<?php 
    //Base de datos

    require '../../includes/config/database.php';
    $db = conectarDB();


    //Consultar a los vendedores
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta); 

    // Arreglo con mensajes de errores
    $errores = [];

    // Variables inicializadas de forma GLOBAL
    $titulo = '';
    $precio = '';
    $descripcion = '';
    $habitaciones = '';
    $wc = ' ';
    $estacionamiento = '';
    $vendedorId = '';

    //Ejecutar el codigo despuesd e que el usuario envie el formulario
    if($_SERVER['REQUEST_METHOD'] === 'POST'){


        // Sanitizar
        // $resultado = filter_var($numero, FILTER_SANITIZE_NUMBER_INT);

        // $resultado = filter_var($numero2, FILTER_VALIDATE_INT);


        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";
        // echo "<pre>";
        // var_dump($_FILES);
        // echo "</pre>";


        //Les asignamos un valor a las variables con $_POST
        // Con mysqli_real_escape_string sanitizamos la entrada de datos
        $titulo = mysqli_real_escape_string( $db, $_POST['titulo']);
        $precio = mysqli_real_escape_string( $db, $_POST['precio']);
        $descripcion = mysqli_real_escape_string( $db, $_POST['descripcion']);
        $habitaciones = mysqli_real_escape_string( $db, $_POST['habitaciones']);
        $wc = mysqli_real_escape_string( $db, $_POST['wc']);
        $estacionamiento = mysqli_real_escape_string( $db, $_POST['estacionamiento']);
        $vendedorId = mysqli_real_escape_string( $db, $_POST['vendedor']);
        $creado = date('Y/m/d');

        // $_FILES hacia una variable
        $imagen = $_FILES['imagen'];

        // var_dump($imagen ['name']);
        // exit;

      

        if(!$titulo){
            $errores [] = 'Debes anadir un titulo';
        }
        
        if(!$precio){
            $errores [] = 'Debes anadir un precio';
        }
        if( strlen($descripcion) < 50 ){
            $errores [] = 'Debes anadir una descripcion de minimo 50 caracteres';
        }
        if(!$habitaciones){
            $errores [] = 'Debes anadir habitaciones';
        }
        if(!$estacionamiento){
            $errores [] = 'Debes anadir estacionamientos';
        }
        if(!$wc){
            $errores [] = 'Debes anadir banos';
        }
        if(!$vendedorId){
            $errores [] = 'Debes seleccionar vendedor';
        }

        if(!$imagen ['name'] || $imagen['error']){
            $errores [] = 'Debes agregar una Imagen de la propiedad';
        }


        // Validamos imagenes por tamano (1MB maximo)
        $medida = 1000*1000;

        if($imagen['size'] > $medida ){
            $errores [] = 'La imagen es muy pesada';
        }

        // echo "<pre>";
        // var_dump($errores);
        // echo "</pre>";
        // exit;
        
        if(empty($errores)){
                
        /** SUBIDA DE ARCHIVOS **/

            // Crear una carpeta
            $carpetaImagenes = '../../imagenes/';

            if(!is_dir($carpetaImagenes)){
            
                mkdir($carpetaImagenes);
            }

            // Generar nombre unico
            $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";


            // Subir imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
       
            
            

            // Insertar en bases de datos
            $query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado,
            vendedores_id ) VALUES ( '$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId' ) ";

            // echo $query; //Aqui revisamos que se inyecten bien los datos en el comando cuando llenamos el formulario

            $resultado = mysqli_query($db, $query);

            if($resultado){

                // Redireccionar al usuario cuando se crea la propiedad
                header('Location: /admin?resultado=1');
            }
        }
    }




    require '../../includes/funciones.php';
    $inicio = true;

    incluirTemplate('header'); //En esta funcion recibimos el nombre del .php que queremos que ejecute en templates
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        <a href="/admin" class="boton boton-verde">Volver</a>

     <!--Aqui mostramos los errores que hay, el div juega un papel importante para el diseno-->
    <?php foreach($errores as $error): ?>

        <div class="alerta error">
    <?php echo $error; ?>

        </div>
    <?php endforeach; ?>

   
        <!--Tomar en cuenta los atributos method y action a la hora de enviar formularios-->
                                                                                    <!--Ojo a el enctype, sirve para enviar imagenes-->
        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data"> <!--USAR GET Y POST DEPENDIENDO DE LA CIRCUNSTANCIA-->

        <!--Fieldset crea el espacio dentro del formulario, recordemos-->
        <fieldset>
        <legend>Informacion General</legend>
        
        <label for="titulo">Titulo:</label>                       <!--Value toma la variable con un valor, en el caso deque haya un error en el formulario cuando se llena, el usuario no pierde lo que ya tenia escrito-->
        <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">  <!--Recordar que el id tiene que ser igual que el for---->

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>"> 

        <label for="imagen">Imagen:</label>
        <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen"> <!--accept solo permite que se suban imagenes-->


        <label for="descripcion">Descripcion:</label>
        <textarea id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>"></textarea>

        </fieldset>

        <fieldset> 
        <legend>Informacion Propiedad</legend>

        <label for="habitaciones">Habitaciones:</label>
        <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej; 4" min="1" max="9" value="<?php echo $habitaciones; ?>"> <!---el min indica la cantidad minima del numero a poner, el max hace lo opuesto---> 

        <label for="wc">Baños:</label>
        <input type="number" id="wc" name="wc" placeholder="Ej; 2" min="1" max="5" value="<?php echo $wc; ?>">

        <label for="estacionamiento">Estacionamiento:</label>
        <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej; 3" min="1" max="5" value="<?php echo $estacionamiento; ?>">


        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>

            <select name="vendedor">

            <option value="">---Seleccione---</option>
            <?php
            while($vendedor = mysqli_fetch_assoc($resultado) ) : ?>
            <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : '';?>   
            value="<?php echo $vendedor['id']; ?>"><?php echo $vendedor['nombre']. " " . $vendedor['apellido']; ?> </option>
            <?php endwhile ?>

            
            </select>
        </fieldset>

        <input type="submit" value="Crear Propiedad" class="boton boton-verde">

        </form>

    </main>

    <?php 
       incluirTemplate('footer');
    ?>

}