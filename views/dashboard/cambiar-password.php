
<?php include_once __DIR__ . '/header-dashboard.php'; ?>


<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver A Perfil</a>

    <form action="/cambiar-password" class="formulario" method="POST">
        <div class="campo">
            <label for="password">Contraseña Actual</label>
            <input type="password" id="password" name="password_actual" placeholder="Tu Constraseña">
        </div>

        <div class="campo">
            <label for="email">Contraseña Nueva</label>
            <input type="password" id="password" name="password_nuevo" placeholder="Contraseña Nueva">
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>
</div>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
