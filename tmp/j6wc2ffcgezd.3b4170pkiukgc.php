<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Cursos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/cursos/nuevo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Curso
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($cursos?:[]) as $curso): ?>
                        <tr>
                            <td><?= ($curso['codigo']) ?></td>
                            <td><?= ($curso['nombre']) ?></td>
                            <td><?= ($curso['fecha_registro'] ? date('d/m/Y H:i', strtotime($curso['fecha_registro'])) : '') ?></td>
                            <td>
                                <?php if ($curso['activo']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <else>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/cursos/<?= ($curso['id_curso']) ?>/editar" class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($curso['activo']): ?>
                                        <a href="/cursos/<?= ($curso['id_curso']) ?>/desactivar" class="btn btn-outline-danger" title="Desactivar" onclick="return confirmDesactivacion()">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <else>
                                        <a href="/cursos/<?= ($curso['id_curso']) ?>/activar" class="btn btn-outline-success" title="Activar">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($cursos) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay cursos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
