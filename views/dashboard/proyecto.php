<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <div class="contenedor-acciones-proyecto">
        <div class="contenedor-nueva-tarea">
            <button type="button" class="agregar-tarea" id="agregar-tarea">&#43; Nueva Tarea</button>
        </div>

        <div class="contenedor-eliminar-proyecto">
            <button type="button" class="eliminar-proyecto" id="eliminar-proyecto">&#45; Eliminar Proyecto</button>
        </div>
    </div>


    <div id="filtros" class="filtros">
        <div class="filtros-inputs">
            <h2>Filtros:</h2>
            <div class="campo">
                <label for="todas">Todas</label>
                <input type="radio" id="todas" name="filtro" value="" checked>
            </div>
            <div class="campo">
                <label for="completadas">Completadas</label>
                <input type="radio" id="completada" name="filtro" value="1">
            </div>
            <div class="campo">
                <label for="pendientes">Pendientes</label>
                <input type="radio" id="pendiente" name="filtro" value="0">
            </div>
        </div>
    </div>
    <ul id="listado-tareas" class="listado-tareas">

    </ul>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php $script .=
    '<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="build/js/tareas.js"></script>
<script src="build/js/proyecto.js"></script>';
?>