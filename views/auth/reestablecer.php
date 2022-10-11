<div class="contenedor reestablecer">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>


    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca Tu Nueva Contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        
        <?php if($mostrar) :?>
        <form method="POST" class="formulario">

            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Tu Contraseña" name="password">
            </div>

            <input type="submit" class="boton" value="Guardar Contraseña">
        </form>

        <?php endif;?>
        <div class="acciones">
            <a href="/">Ya Tienes una Cuenta? Inicia Sesión!</a>
            <a href="/crear">¿Aun no tienes una cuenta? Crea Una!</a>
        </div>
    </div>
    <!--.contenedor-sm -->
</div>