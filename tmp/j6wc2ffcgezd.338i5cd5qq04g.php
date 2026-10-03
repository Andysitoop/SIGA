<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Alumnos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/alumnos/nuevo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Alumno
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="/alumnos/buscar" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o apellido..." value="<?= ($this->esc($query)) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nombre Completo</th>
                        <th>Carrera</th>
                        <th>Fecha Nacimiento</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($alumnos?:[]) as $alumno): ?>
                        <tr>
                            <td>
                                <?php if ($alumno['fotografia']): ?>
                                    <img src="<?= ($alumno['fotografia']) ?>" alt="Foto" class="foto-alumno">
                                <else>
                                    <div class="foto-alumno bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="bi bi-person"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= ($alumno['apellidos']) ?>, <?= ($alumno['nombres']) ?></td>
                            <td><?= ($alumno['nombre_carrera']) ?></td>
                            <td><?= ($alumno['fecha_nacimiento'] ? date('d/m/Y', strtotime($alumno['fecha_nacimiento'])) : '') ?></td>
                            <td><?= ($alumno['fecha_registro'] ? date('d/m/Y', strtotime($alumno['fecha_registro'])) : '') ?></td>
                            <td>
                                <?php if ($alumno['activo']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <else>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/alumnos/<?= ($alumno['id_alumno']) ?>" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/alumnos/<?= ($alumno['id_alumno']) ?>/editar" class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($alumno['activo']): ?>
                                        <a href="/alumnos/<?= ($alumno['id_alumno']) ?>/desactivar" class="btn btn-outline-danger" title="Desactivar" onclick="return confirmDesactivacion()">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <else>
                                        <a href="/alumnos/<?= ($alumno['id_alumno']) ?>/activar" class="btn btn-outline-success" title="Activar">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="/notas/buscar?q=<?= ($alumno['nombres']) ?> <?= ($alumno['apellidos']) ?>" class="btn btn-outline-secondary" title="Notas">
                                        <i class="bi bi-journal-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($alumnos) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay alumnos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="/alumnos?page=<?= ($page - 1) ?>">Anterior</a>
                        </li>
                    <?php endif; ?>
                    <?php foreach ((range(1, $totalPages)?:[]) as $p): ?>
                        <li class="page-item <?php if ($p == $page): ?>active<?php endif; ?>">
                            <a class="page-link" href="/alumnos?page=<?= ($p) ?>"><?= ($p) ?></a>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="/alumnos?page=<?= ($page + 1) ?>">Siguiente</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
