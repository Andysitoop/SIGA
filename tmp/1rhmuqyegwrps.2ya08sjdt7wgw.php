<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php if ($curso): ?>Editar<else>Nuevo<?php endif; ?> Curso</h1>
    <a href="/cursos" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= ($form_action) ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= ($csrf_token) ?>">
            
            <div class="mb-3">
                <label for="codigo" class="form-label">Código *</label>
                <input type="text" class="form-control" id="codigo" name="codigo" 
                       <?php if ($curso): ?>
                           value="<?= ($this->esc($curso['codigo'])) ?>"
                       <else>
                           value="<?= ($this->esc($data['codigo'])) ?>"
                       <?php endif; ?>
                       required>
            </div>
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       <?php if ($curso): ?>
                           value="<?= ($this->esc($curso['nombre'])) ?>"
                       <else>
                           value="<?= ($this->esc($data['nombre'])) ?>"
                       <?php endif; ?>
                       required>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" 
                       <?php if ($curso): ?>
                           <?php if ($curso['activo']): ?>checked<?php endif; ?>
                       <else>
                           <?php if ($data['activo']): ?>checked<?php endif; ?>
                       <?php endif; ?>
                       <else>
                           checked
                       >
                <label class="form-check-label" for="activo">Activo</label>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="/cursos" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
