<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Notas</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/notas/buscar" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Registrar Nota
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Alumno</th>
                        <th>Curso</th>
                        <th>Semestre</th>
                        <th>Nota</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($notas?:[]) as $nota): ?>
                        <tr>
                            <td><?= ($nota['apellidos']) ?>, <?= ($nota['nombres']) ?></td>
                            <td><?= ($nota['nombre_curso']) ?></td>
                            <td><?= ($nota['nombre_semestre']) ?></td>
                            <td><?= ($nota['nota']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($nota['nota'] >= 60 ? 'success' : 'danger') ?>">
                                    <?= ($nota['nota'] >= 60 ? 'Aprobado' : 'Reprobado')."
" ?>
                                </span>
                            </td>
                            <td><?= ($nota['fecha_registro'] ? date('d/m/Y', strtotime($nota['fecha_registro'])) : '') ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/notas/<?= ($nota['id_nota']) ?>/editar" class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/notas/<?= ($nota['id_nota']) ?>/desactivar" class="btn btn-outline-danger" title="Desactivar" onclick="return confirmDesactivacion()">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($notas) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay notas registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
