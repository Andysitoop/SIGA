<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Carreras</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/carreras/nuevo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Carrera
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($carreras?:[]) as $carrera): ?>
                        <tr>
                            <td><?= ($carrera['id_carrera']) ?></td>
                            <td><?= ($carrera['nombre']) ?></td>
                            <td><?= ($carrera['fecha_registro'] ? date('d/m/Y H:i', strtotime($carrera['fecha_registro'])) : '') ?></td>
                            <td>
                                <?php if ($carrera['activo']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <else>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/carreras/<?= ($carrera['id_carrera']) ?>/editar" class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form class="d-inline" action="/carreras/<?= ($carrera['id_carrera']) ?>/eliminar" method="POST" onsubmit="return confirmEliminacion(this)">
                                        <input type="hidden" name="csrf_token" value="<?= ($csrf_token) ?>">
                                        <select class="form-select form-select-sm" name="delete_mode" aria-label="Qué hacer con los alumnos asociados">
                                            <option value="restrict" data-i18n="No borrar alumnos">No borrar alumnos</option>
                                            <option value="cascade" data-i18n="Borrar alumnos y notas">Borrar alumnos y notas</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar permanentemente">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($carreras) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay carreras registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
