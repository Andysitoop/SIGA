<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Alumnos</p>
                        <p class="stats-number"><?= ($total_alumnos) ?></p>
                    </div>
                    <i class="bi bi-people-fill stats-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Carreras</p>
                        <p class="stats-number"><?= ($total_carreras) ?></p>
                    </div>
                    <i class="bi bi-book-fill stats-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Cursos</p>
                        <p class="stats-number"><?= ($total_cursos) ?></p>
                    </div>
                    <i class="bi bi-book-half stats-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Notas</p>
                        <p class="stats-number"><?= ($total_notas) ?></p>
                    </div>
                    <i class="bi bi-journal-text stats-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Últimos Alumnos Registrados</span>
                <a href="/alumnos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($ultimos_alumnos?:[]) as $alumno): ?>
                                <tr>
                                    <td><?= ($alumno['apellidos']) ?>, <?= ($alumno['nombres']) ?></td>
                                    <td><?= ($alumno['nombre_carrera']) ?></td>
                                    <td><?= ($alumno['fecha_registro'] ? date('d/m/Y', strtotime($alumno['fecha_registro'])) : '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($ultimos_alumnos) == 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay alumnos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Últimas Notas Ingresadas</span>
                <a href="/notas" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alumno</th>
                                <th>Curso</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($ultimas_notas?:[]) as $nota): ?>
                                <tr>
                                    <td><?= ($nota['apellidos']) ?>, <?= ($nota['nombres']) ?></td>
                                    <td><?= ($nota['nombre_curso']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($nota['nota'] >= 60 ? 'success' : 'danger') ?>">
                                            <?= ($nota['nota'])."
" ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($ultimas_notas) == 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay notas registradas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
