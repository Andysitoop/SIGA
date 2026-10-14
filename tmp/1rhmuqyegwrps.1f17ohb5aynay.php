<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php if ($nota): ?>Editar<else>Registrar<?php endif; ?> Nota</h1>
    <a href="/notas/historial/<?= ($alumno['id_alumno']) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="foto-alumno bg-secondary d-flex align-items-center justify-content-center text-white me-3 js-avatar"
                         data-src="<?= ($alumno['foto_url']) ?>"
                         aria-label="Foto de <?= ($alumno['nombres']) ?> <?= ($alumno['apellidos']) ?>">
                        <i class="bi bi-person" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?= ($alumno['nombres']) ?> <?= ($alumno['apellidos']) ?></h5>
                        <small class="text-muted"><?= ($alumno['nombre_carrera']) ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= ($form_action) ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= ($csrf_token) ?>">
            <input type="hidden" name="id_alumno" value="<?= ($alumno['id_alumno']) ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_semestre" class="form-label">Semestre *</label>
                        <select class="form-select" id="id_semestre" name="id_semestre" required>
                            <option value="">Seleccione un semestre</option>
                            <?php foreach (($semestres?:[]) as $semestre): ?>
                                <option value="<?= ($semestre['id_semestre']) ?>" 
                                        <?php if ($nota): ?>
                                            <?php if ($nota['id_semestre'] == $semestre['id_semestre']): ?>selected<?php endif; ?>
                                        <else>
                                            <?php if ($data['id_semestre'] == $semestre['id_semestre']): ?>selected<?php endif; ?>
                                        <?php endif; ?>>
                                    <?= ($semestre['nombre'])."
" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_seccion" class="form-label">Sección *</label>
                        <select class="form-select" id="id_seccion" name="id_seccion" required>
                            <option value="">Seleccione una sección</option>
                            <?php foreach (($secciones?:[]) as $seccion): ?>
                                <option value="<?= ($seccion['id_seccion']) ?>" 
                                        <?php if ($nota): ?>
                                            <?php if ($nota['id_seccion'] == $seccion['id_seccion']): ?>selected<?php endif; ?>
                                        <else>
                                            <?php if ($data['id_seccion'] == $seccion['id_seccion']): ?>selected<?php endif; ?>
                                        <?php endif; ?>>
                                    <?= ($seccion['nombre'])."
" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_curso" class="form-label">Curso *</label>
                        <select class="form-select" id="id_curso" name="id_curso" required>
                            <option value="">Seleccione un curso</option>
                            <?php foreach (($cursos?:[]) as $curso): ?>
                                <option value="<?= ($curso['id_curso']) ?>" 
                                        <?php if ($nota): ?>
                                            <?php if ($nota['id_curso'] == $curso['id_curso']): ?>selected<?php endif; ?>
                                        <else>
                                            <?php if ($data['id_curso'] == $curso['id_curso']): ?>selected<?php endif; ?>
                                        <?php endif; ?>>
                                    <?= ($curso['codigo']) ?> - <?= ($curso['nombre'])."
" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nota" class="form-label">Nota * (<?= ($nota_minima) ?> - <?= ($nota_maxima) ?>)</label>
                        <input type="number" class="form-control" id="nota" name="nota" 
                               <?php if ($nota): ?>
                                   value="<?= ($nota['nota']) ?>"
                               <else>
                                   value="<?= ($data['nota']) ?>"
                               <?php endif; ?>
                               min="<?= ($nota_minima) ?>" max="<?= ($nota_maxima) ?>" step="0.01" required>
                        <div class="form-text">Nota mínima para aprobar: <?= ($nota_minima) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="/notas/historial/<?= ($alumno['id_alumno']) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
