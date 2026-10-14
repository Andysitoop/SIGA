<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php if ($alumno): ?>Editar<else>Nuevo<?php endif; ?> Alumno</h1>
    <a href="/alumnos" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= ($form_action) ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= ($csrf_token) ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" 
                               <?php if ($alumno): ?>
                                   value="<?= ($this->esc($alumno['nombres'])) ?>"
                               <else>
                                   value="<?= ($this->esc($data['nombres'])) ?>"
                               <?php endif; ?>
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                               <?php if ($alumno): ?>
                                   value="<?= ($this->esc($alumno['apellidos'])) ?>"
                               <else>
                                   value="<?= ($this->esc($data['apellidos'])) ?>"
                               <?php endif; ?>
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                               <?php if ($alumno): ?>
                                   value="<?= ($alumno['fecha_nacimiento']) ?>"
                               <else>
                                   value="<?= ($data['fecha_nacimiento']) ?>"
                               <?php endif; ?>
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_carrera" class="form-label">Carrera *</label>
                        <select class="form-select" id="id_carrera" name="id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach (($carreras?:[]) as $carrera): ?>
                                <option value="<?= ($carrera['id_carrera']) ?>" 
                                        <?php if ($alumno): ?>
                                            <?php if ($alumno['id_carrera'] == $carrera['id_carrera']): ?>selected<?php endif; ?>
                                        <else>
                                            <?php if ($data['id_carrera'] == $carrera['id_carrera']): ?>selected<?php endif; ?>
                                        <?php endif; ?>>
                                    <?= ($carrera['nombre'])."
" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fotografia" class="form-label">Fotografía</label>
                        <input type="file" class="form-control" id="fotografia" name="fotografia" 
                               accept="image/jpeg,image/jpg,image/png">
                        <div class="form-text">Formatos: JPG, JPEG, PNG. Máximo 2MB</div>
                    </div>
                    
                    <div class="mb-3 text-center">
                        <div data-src="<?= ($foto_url) ?>" id="preview-foto"
                             class="photo-preview foto-alumno-lg bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-2"
                             role="img" aria-label="Foto del alumno">
                            <i class="bi bi-person fs-1" aria-hidden="true"></i>
                        </div>
                    </div>
                    <?php if ($foto_url): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remove-photo" name="remove_photo" value="1"
                                   <?php if ($data['remove_photo']): ?>checked<?php endif; ?>>
                            <label class="form-check-label" for="remove-photo">Quitar foto actual</label>
                        </div>
                    <?php endif; ?>
                    <script>
                        (() => {
                            const preview = document.getElementById('preview-foto');
                            const fallback = '<i class="bi bi-person fs-1" aria-hidden="true"></i>';
                            const showImage = source => {
                                const image = document.createElement('img');
                                image.className = 'foto-alumno-lg';
                                image.alt = 'Foto del alumno';
                                image.onerror = () => preview.innerHTML = fallback;
                                image.src = source;
                                preview.replaceChildren(image);
                            };
                            const photoInput = document.getElementById('fotografia');
                            const removePhoto = document.getElementById('remove-photo');

                            if (preview.dataset.src && !(removePhoto && removePhoto.checked)) {
                                showImage(preview.dataset.src);
                            }

                            photoInput.addEventListener('change', () => {
                                if (photoInput.files && photoInput.files[0]) {
                                    if (removePhoto) removePhoto.checked = false;
                                    const reader = new FileReader();
                                    reader.onload = event => showImage(event.target.result);
                                    reader.readAsDataURL(photoInput.files[0]);
                                }
                            });

                            if (removePhoto) {
                                removePhoto.addEventListener('change', () => {
                                    if (removePhoto.checked) {
                                        photoInput.value = '';
                                        preview.innerHTML = fallback;
                                    } else if (preview.dataset.src) {
                                        showImage(preview.dataset.src);
                                    }
                                });
                            }
                        })();
                    </script>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="/alumnos" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
