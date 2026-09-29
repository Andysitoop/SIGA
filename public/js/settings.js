(() => {
    const languageKey = 'siga.language';
    const accountKey = 'siga.demoAccounts';
    const originalTitle = document.title;
    const sourceTextNodes = new WeakMap();
    const translations = {
        'Ajustes': 'Settings',
        'Dashboard': 'Dashboard',
        'Alumnos': 'Students',
        'Notas': 'Grades',
        'Reportes': 'Reports',
        'Catálogos': 'Catalogs',
        'Carreras': 'Programs',
        'Carrera': 'Program',
        'Cursos': 'Courses',
        'Semestres': 'Terms',
        'Secciones': 'Sections',
        'Cerrar Sesión': 'Sign out',
        'PREFERENCIAS DEL SISTEMA': 'SYSTEM PREFERENCES',
        'Administra cuentas y preferencias de idioma.': 'Manage accounts and language preferences.',
        'Vista de demostración': 'Demo interface',
        'Cuentas': 'Accounts',
        'Idioma': 'Language',
        'Cuentas de usuario': 'User accounts',
        'Crea perfiles y asigna un nivel de acceso.': 'Create profiles and assign an access level.',
        'cuentas': 'accounts',
        'Nueva cuenta': 'New account',
        'Nombre': 'Name',
        'Correo electrónico': 'Email address',
        'Rol': 'Role',
        'Usuario': 'User',
        'Contraseña': 'Password',
        'Sistema de Gestión Académica': 'Academic Management System',
        'Administrador': 'Administrator',
        'Crear cuenta': 'Create account',
        'Escribe el nombre de la cuenta.': 'Enter an account name.',
        'Ingresa un correo válido.': 'Enter a valid email address.',
        'Cuentas registradas': 'Registered accounts',
        'Guardado en este navegador': 'Saved in this browser',
        'Cuenta': 'Account',
        'Estado': 'Status',
        'Acción': 'Action',
        'Estas cuentas son solo una maqueta local; no habilitan acceso real.': 'These accounts are a local demo only; they do not grant real access.',
        'Idioma de la interfaz': 'Interface language',
        'Elige el idioma que quieres usar en el sistema.': 'Choose the language you want to use in the system.',
        'La preferencia se guarda en este navegador.': 'Your preference is saved in this browser.',
        'Activo': 'Active',
        'Desactivar': 'Deactivate',
        'Activar': 'Activate',
        'Eliminar': 'Remove',
        'No hay cuentas todavía.': 'No accounts yet.',
        'Esta dirección de correo ya está registrada.': 'This email address is already registered.',
        'Cuenta creada. Es una demostración local, no un usuario de acceso real.': 'Account created. This is a local demo, not a real login account.',
        'Cuenta eliminada.': 'Account removed.',
        '¿Eliminar esta cuenta de demostración?': 'Remove this demo account?',
        'Total Alumnos': 'Total students',
        'Total Carreras': 'Total programs',
        'Total Cursos': 'Total courses',
        'Total Notas': 'Total grades',
        'Últimos Alumnos Registrados': 'Recently registered students',
        'Últimas Notas Ingresadas': 'Latest grades',
        'Ver todos': 'View all',
        'Ver todas': 'View all',
        'Fecha': 'Date',
        'Alumno': 'Student',
        'Curso': 'Course',
        'Nota': 'Grade',
        'No hay alumnos registrados': 'No students registered',
        'No hay notas registradas': 'No grades recorded',
        'No hay carreras registradas': 'No programs registered',
        'No hay cursos registrados': 'No courses registered',
        'No hay semestres registrados': 'No terms registered',
        'No hay secciones registradas': 'No sections registered',
        'Inactivo': 'Inactive',
        'Nuevo Alumno': 'New student',
        'Editar Alumno': 'Edit student',
        'Buscar Alumnos': 'Search students',
        'Detalle del Alumno': 'Student details',
        'Registrar Nota': 'Add grade',
        'Buscar Alumno para Registrar Notas': 'Find a student to add grades',
        'Reporte de Alumnos por Carrera': 'Student report by program',
        'Reporte de Notas': 'Grade report',
        'Volver': 'Back',
        'Guardar': 'Save',
        'Cancelar': 'Cancel',
        'Buscar': 'Search',
        'Filtrar': 'Filter',
        'Acciones': 'Actions',
        'Fotografía': 'Photo',
        'Foto': 'Photo',
        'Nombre Completo': 'Full name',
        'Fecha Nacimiento': 'Date of birth',
        'Fecha Registro': 'Date added',
        'Todas las carreras': 'All programs',
        'No hay alumnos para mostrar': 'No students to display',
        'No hay notas para mostrar': 'No grades to display',
        'Modo demo: cualquier usuario/contraseña funciona': 'Demo mode: any username/password works',
        'Iniciar Sesión': 'Sign in',
        'Nuevo': 'New',
        'Nueva': 'New',
        'Editar': 'Edit',
        'Registrar': 'Add',
        'Exportar CSV': 'Export CSV',
        'Código': 'Code',
        'Nombres': 'First name',
        'Apellidos': 'Last name',
        'Información Personal': 'Personal information',
        'Fecha de Nacimiento:': 'Date of birth:',
        'Fecha de Registro:': 'Date added:',
        'Estado:': 'Status:',
        'Total de Cursos': 'Total courses',
        'Cursos Aprobados': 'Courses passed',
        'Cursos Reprobados': 'Courses failed',
        'Promedio General': 'Overall average',
        'Historial Académico Completo': 'Full academic history',
        'Semestre': 'Term',
        'Sección': 'Section',
        'Aprobado': 'Passed',
        'Reprobado': 'Failed',
        'Todas las secciones': 'All sections',
        'Todos los cursos': 'All courses',
        'Todos los semestres': 'All terms',
        'Anterior': 'Previous',
        'Siguiente': 'Next',
        'Página': 'Page',
        'Buscar por nombre o apellido...': 'Search by first or last name...',
        'Seleccione una carrera': 'Select a program',
        'Seleccione un curso': 'Select a course',
        'Seleccione un semestre': 'Select a term',
        'Seleccione una sección': 'Select a section',
        'Fecha de Nacimiento': 'Date of birth',
        'Fecha de Registro': 'Date added',
        'Ej. Alex Rivera': 'e.g. Alex Rivera',
        'nombre@ejemplo.com': 'name@example.com',
        'Usuario de prueba': 'Test user'
    };

    const getLanguage = () => localStorage.getItem(languageKey) === 'en' ? 'en' : 'es';

    function translatePage() {
        const language = getLanguage();
        document.documentElement.lang = language;

        const titleParts = originalTitle.split(' - ');
        if (language === 'en' && translations[titleParts[0]]) {
            document.title = [translations[titleParts[0]], ...titleParts.slice(1)].join(' - ');
        } else {
            document.title = originalTitle;
        }

        document.querySelectorAll('[data-i18n]').forEach((element) => {
            const spanish = element.dataset.i18n;
            if (language === 'en' && translations[spanish]) {
                element.textContent = translations[spanish];
            } else {
                element.textContent = spanish;
            }
        });

        document.querySelectorAll('[placeholder]').forEach((element) => {
            const spanish = element.dataset.i18nPlaceholder || (element.dataset.originalPlaceholder ||= element.placeholder);
            element.placeholder = language === 'en' ? (translations[spanish] || spanish) : spanish;
        });

        document.querySelectorAll('[alt], [title]').forEach((element) => {
            ['alt', 'title'].forEach((attribute) => {
                const originalKey = `original${attribute[0].toUpperCase()}${attribute.slice(1)}`;
                const original = element.dataset[originalKey] || element.getAttribute(attribute);
                if (!original) {
                    return;
                }
                element.dataset[originalKey] = original;
                element.setAttribute(attribute, language === 'en' ? (translations[original] || original) : original);
            });
        });

        document.querySelectorAll('[data-i18n-aria]').forEach((element) => {
            const spanish = element.dataset.i18nAria;
            element.setAttribute('aria-label', language === 'en' ? (translations[spanish] || spanish) : spanish);
        });

        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        while (walker.nextNode()) {
            const node = walker.currentNode;
            const current = node.nodeValue;
            const original = sourceTextNodes.get(node) || current.trim();
            if (!sourceTextNodes.has(node)) {
                sourceTextNodes.set(node, original);
            }
            if (!original || node.parentElement.closest('script, style, textarea, [data-i18n]')) {
                continue;
            }
            const leadingWhitespace = current.match(/^\s*/)[0];
            const trailingWhitespace = current.match(/\s*$/)[0];
            const translated = language === 'en' ? (translations[original] || original) : original;
            node.nodeValue = `${leadingWhitespace}${translated}${trailingWhitespace}`;
        }

        document.querySelectorAll('[data-language]').forEach((button) => {
            const selected = button.dataset.language === language;
            button.classList.toggle('selected', selected);
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
    }

    function getAccounts() {
        const fallback = [
            { id: 'demo-admin', name: 'Admin SIGA', email: 'admin@siga.demo', role: 'admin', active: true },
            { id: 'demo-user', name: 'Usuario de prueba', email: 'usuario@siga.demo', role: 'user', active: true }
        ];

        try {
            const saved = JSON.parse(localStorage.getItem(accountKey));
            if (Array.isArray(saved)) {
                return saved;
            }
        } catch (error) {
            localStorage.removeItem(accountKey);
        }

        localStorage.setItem(accountKey, JSON.stringify(fallback));
        return fallback;
    }

    function renderAccounts() {
        const rows = document.getElementById('accountRows');
        if (!rows) {
            return;
        }

        const accounts = getAccounts();
        document.getElementById('accountCount').textContent = accounts.length;
        rows.replaceChildren();

        if (accounts.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.className = 'text-center text-muted py-4';
            cell.dataset.i18n = 'No hay cuentas todavía.';
            cell.textContent = getLanguage() === 'en' ? translations[cell.dataset.i18n] : cell.dataset.i18n;
            row.append(cell);
            rows.append(row);
            return;
        }

        accounts.forEach((account) => {
            const row = document.createElement('tr');
            const identity = document.createElement('td');
            identity.className = 'account-identity';
            const avatar = document.createElement('span');
            avatar.className = 'account-avatar';
            avatar.textContent = account.name.trim().slice(0, 1).toUpperCase();
            const identityText = document.createElement('span');
            const name = document.createElement('strong');
            name.textContent = account.id === 'demo-user' && getLanguage() === 'en'
                ? translations['Usuario de prueba']
                : account.name;
            const email = document.createElement('small');
            email.textContent = account.email;
            identityText.append(name, email);
            identity.append(avatar, identityText);

            const roleCell = document.createElement('td');
            const role = document.createElement('span');
            role.className = `role-badge ${account.role === 'admin' ? 'role-admin' : 'role-user'}`;
            role.dataset.i18n = account.role === 'admin' ? 'Administrador' : 'Usuario';
            role.textContent = getLanguage() === 'en' ? translations[role.dataset.i18n] : role.dataset.i18n;
            roleCell.append(role);

            const statusCell = document.createElement('td');
            const status = document.createElement('span');
            status.className = `account-status ${account.active ? 'is-active' : 'is-inactive'}`;
            status.dataset.i18n = account.active ? 'Activo' : 'Inactivo';
            status.textContent = getLanguage() === 'en' ? translations[status.dataset.i18n] : status.dataset.i18n;
            statusCell.append(status);

            const actionCell = document.createElement('td');
            actionCell.className = 'text-end';
            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'btn btn-sm btn-outline-secondary account-action';
            toggle.dataset.accountAction = 'toggle';
            toggle.dataset.accountId = account.id;
            toggle.dataset.i18n = account.active ? 'Desactivar' : 'Activar';
            toggle.textContent = getLanguage() === 'en' ? translations[toggle.dataset.i18n] : toggle.dataset.i18n;
            actionCell.append(toggle);

            if (!account.id.startsWith('demo-')) {
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'btn btn-sm btn-outline-danger account-action ms-2';
                remove.dataset.accountAction = 'remove';
                remove.dataset.accountId = account.id;
                remove.dataset.i18n = 'Eliminar';
                remove.textContent = getLanguage() === 'en' ? translations.Eliminar : 'Eliminar';
                actionCell.append(remove);
            }

            row.append(identity, roleCell, statusCell, actionCell);
            rows.append(row);
        });
    }

    function initializeTabs() {
        document.querySelectorAll('[data-settings-tab]').forEach((tab) => {
            tab.addEventListener('click', () => {
                const selected = tab.dataset.settingsTab;
                document.querySelectorAll('[data-settings-tab]').forEach((item) => {
                    const active = item === tab;
                    item.classList.toggle('active', active);
                    item.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                document.querySelectorAll('[data-settings-panel]').forEach((panel) => {
                    panel.hidden = panel.dataset.settingsPanel !== selected;
                });
            });
        });
    }

    function initializeAccounts() {
        const form = document.getElementById('accountForm');
        if (!form) {
            return;
        }

        renderAccounts();
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            form.classList.add('was-validated');
            if (!form.reportValidity()) {
                return;
            }

            const formData = new FormData(form);
            const email = String(formData.get('email')).trim().toLowerCase();
            const accounts = getAccounts();
            const feedback = document.getElementById('accountFeedback');
            if (accounts.some((account) => account.email.toLowerCase() === email)) {
                feedback.dataset.i18n = 'Esta dirección de correo ya está registrada.';
                feedback.textContent = getLanguage() === 'en' ? translations[feedback.dataset.i18n] : feedback.dataset.i18n;
                feedback.classList.add('is-error');
                return;
            }

            accounts.unshift({
                id: globalThis.crypto?.randomUUID?.() || `account-${Date.now()}`,
                name: String(formData.get('name')).trim(),
                email,
                role: String(formData.get('role')),
                active: true
            });
            localStorage.setItem(accountKey, JSON.stringify(accounts));
            form.reset();
            form.classList.remove('was-validated');
            feedback.dataset.i18n = 'Cuenta creada. Es una demostración local, no un usuario de acceso real.';
            feedback.textContent = getLanguage() === 'en' ? translations[feedback.dataset.i18n] : feedback.dataset.i18n;
            feedback.classList.remove('is-error');
            renderAccounts();
        });

        document.getElementById('accountRows').addEventListener('click', (event) => {
            const button = event.target.closest('[data-account-action]');
            if (!button) {
                return;
            }
            const accounts = getAccounts();
            const account = accounts.find((item) => item.id === button.dataset.accountId);
            if (!account) {
                return;
            }
            if (button.dataset.accountAction === 'remove') {
                const confirmation = translations['¿Eliminar esta cuenta de demostración?'];
                if (!window.confirm(getLanguage() === 'en' ? confirmation : '¿Eliminar esta cuenta de demostración?')) {
                    return;
                }
                localStorage.setItem(accountKey, JSON.stringify(accounts.filter((item) => item.id !== account.id)));
                document.getElementById('accountFeedback').dataset.i18n = 'Cuenta eliminada.';
            } else {
                account.active = !account.active;
                localStorage.setItem(accountKey, JSON.stringify(accounts));
            }
            renderAccounts();
            const feedback = document.getElementById('accountFeedback');
            if (feedback.dataset.i18n) {
                feedback.textContent = getLanguage() === 'en' ? translations[feedback.dataset.i18n] : feedback.dataset.i18n;
            }
        });
    }

    function initializeLanguage() {
        document.querySelectorAll('[data-language]').forEach((button) => {
            button.addEventListener('click', () => {
                localStorage.setItem(languageKey, button.dataset.language);
                translatePage();
                renderAccounts();
                const feedback = document.getElementById('languageFeedback');
                if (feedback) {
                    feedback.dataset.i18n = 'La preferencia se guarda en este navegador.';
                    feedback.textContent = getLanguage() === 'en' ? translations[feedback.dataset.i18n] : feedback.dataset.i18n;
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        translatePage();
        initializeTabs();
        initializeAccounts();
        initializeLanguage();
    });
})();
