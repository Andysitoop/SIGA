<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buscar Alumnos</h1>
    <a href="/alumnos" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="/alumnos/buscar" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o apellido..." value="<?= ($this->esc($query)) ?>" required autofocus>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($query): ?>
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
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/alumnos/<?= ($alumno['id_alumno']) ?>" class="btn btn-outline-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/notas/historial/<?= ($alumno['id_alumno']) ?>" class="btn btn-outline-primary" title="Notas">
                                            <i class="bi bi-journal-text"></i>
                                        </a>
                                        <a href="/notas/nuevo?id_alumno=<?= ($alumno['id_alumno']) ?>" class="btn btn-outline-success" title="Agregar Nota">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($alumnos) == 0): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No se encontraron resultados para "<?= ($query) ?>"</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
