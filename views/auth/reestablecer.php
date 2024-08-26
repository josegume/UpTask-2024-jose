<div class="contenedor reestablecer">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>
    
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca Tu Nuevo Password</p>
        
        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <?php if($mostrar) { ?>

        <form class="formulario" method="POST">
            <div class="campo">
                <label for="password">PassWord</label>
                <input 
                    type="password"
                    id="password"
                    placeholder="Tu Password"
                    name="password"
                >
            </div>

            <input type="submit" class="boton" value="Guardar Password">
        </form>

        <?php } ?>

        <div class="acciones">
            <a href="/crear">¿ Aun no tienes una cuenta ? Obtener una</a>
            <a href="/olvide">¿Olvidaste Tu Password?</a>
        </div>
    </div> <!-- contenedor-sm -->

</div>