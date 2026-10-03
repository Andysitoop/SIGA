<form action="/login/authenticate" method="POST">
    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="	username" name="username" placeholder="Usuario" required>
        <label for="username">Usuario</label>
    </div>

    <div class="form-floating mb-4">
        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
        <label for="password">Contraseña</label>
    </div>

    <button type="submit" class="btn btn-primary btn-login w-100">
        <i class="bi bi-box-arrow-in-right me-2"></i>
        Iniciar Sesión
    </button>

    <div class="text-center mt-4">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Modo demo: cualquier usuario/contraseña funciona
        </small>
    </div>
</form>
