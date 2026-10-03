<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reporte Individual del Alumno</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/reportes/alumnos" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <a href="/reportes/alumno/<?= ($alumno['id_alumno']) ?>/exportar-csv" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV
        </a>
    </div>
</div>

<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Historial individual de demostración con datos de ejemplo.</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <?php if ($alumno['foto_url']): ?>
                    <img src="<?= ($alumno['foto_url']) ?>" alt="Foto" class="foto-alumno-lg mb-3">
                <else>
                    <div class="foto-alumno-lg bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-3">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                <?php endif; ?>
                <h5 class="card-title"><?= ($alumno['nombres']) ?> <?= ($alumno['apellidos']) ?></h5>
                <p class="card-text text-muted"><?= ($alumno['nombre_carrera']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">Información Personal</h6>
                <table class="table table-sm">
                    <tr>
                        <th class="w-50">Fecha de Nacimiento:</th>
                        <td><?= ($alumno['fecha_nacimiento'] ? date('d/m/Y', strtotime($alumno['fecha_nacimiento'])) : '') ?></td>
                    </tr>
                    <tr>
                        <th>Fecha de Registro:</th>
                        <td><?= ($alumno['fecha_registro'] ? date('d/m/Y H:i', strtotime($alumno['fecha_registro'])) : '') ?></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="badge bg-<?= ($alumno['activo'] ? 'success' : 'secondary') ?>"><?= ($alumno['activo'] ? 'Activo' : 'Inactivo') ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card mb-0">
            <div class="card-body text-center">
                <p class="stats-number"><?= ($stats['total_cursos']) ?></p>
                <p class="text-muted mb-0">Total de Cursos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card mb-0" style="border-left-color: #198754;">
            <div class="card-body text-center">
                <p class="stats-number text-success"><?= ($stats['cursos_aprobados']) ?></p>
                <p class="text-muted mb-0">Cursos Aprobados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card mb-0" style="border-left-color: #dc3545;">
            <div class="card-body text-center">
                <p class="stats-number text-danger"><?= ($stats['cursos_reprobados']) ?></p>
                <p class="text-muted mb-0">Cursos Reprobados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card mb-0" style="border-left-color: #0dcaf0;">
            <div class="card-body text-center">
                <p class="stats-number text-info"><?= ($stats['promedio_general']) ?></p>
                <p class="text-muted mb-0">Promedio General</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Historial Académico Completo</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Semestre</th>
                        <th>Sección</th>
                        <th>Código</th>
                        <th>Curso</th>
                        <th>Nota</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($notas?:[]) as $nota): ?>
                        <tr>
                            <td><?= ($nota['nombre_semestre']) ?></td>
                            <td><?= ($nota['nombre_seccion']) ?></td>
                            <td><?= ($nota['codigo_curso']) ?></td>
                            <td><?= ($nota['nombre_curso']) ?></td>
                            <td><?= ($nota['nota']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($nota['aprobado'] ? 'success' : 'danger') ?>">
                                    <?= ($nota['aprobado'] ? 'Aprobado' : 'Reprobado')."
" ?>
                                </span>
                            </td>
                            <td><?= ($nota['fecha_registro'] ? date('d/m/Y', strtotime($nota['fecha_registro'])) : '') ?></td>
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
