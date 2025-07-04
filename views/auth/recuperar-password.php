<h1 class="nombre-paigina">Recuperar Password</h1>
<p class="descripcion-pagina">Colca tu nuevo password a continuación</p>

<?php include_once __DIR__."/../templates/alertas.php"; ?>

<?php if($error) return null; ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu nuevo password">
    </div>
    <input type="submit" class="boton" value="Guardar nuevo password">

</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Obten una</a>
</div>