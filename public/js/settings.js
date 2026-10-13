(() => {
    const languageKey = 'siga.language';
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
        'Administra cuentas reales y preferencias de idioma.': 'Manage real accounts and language preferences.',
        'Crea perfiles y asigna un nivel de acceso.': 'Create profiles and assign an access level.',
        'Mínimo 12 caracteres.': 'At least 12 characters.',
        'Cuenta actual': 'Current account',
        'No hay cuentas todavía.': 'No accounts yet.',
        'Cuentas': 'Accounts',
        'Idioma': 'Language',
        'Cuentas de usuario': 'User accounts',
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
        'Almacenadas en el sistema': 'Stored in the system',
        'Cuenta': 'Account',
        'Estado': 'Status',
        'Acción': 'Action',
        'Idioma de la interfaz': 'Interface language',
        'Elige el idioma que quieres usar en el sistema.': 'Choose the language you want to use in the system.',
        'La preferencia se guarda en este navegador.': 'Your preference is saved in this browser.',
        'Activo': 'Active',
        'Desactivar': 'Deactivate',
        'Activar': 'Activate',
        'No hay cuentas todavía.': 'No accounts yet.',
        'Esta dirección de correo ya está registrada.': 'This email address is already registered.',
        '¿Desactivar esta cuenta?': 'Deactivate this account?',
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

    function initializeTabs() {
        const selectedTab = document.querySelector('[data-settings-tab][aria-selected="true"]');
        if (selectedTab) {
            document.querySelectorAll('[data-settings-panel]').forEach((panel) => {
                panel.hidden = panel.dataset.settingsPanel !== selectedTab.dataset.settingsTab;
            });
        }

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
        document.querySelectorAll('[data-account-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                const message = translations['¿Desactivar esta cuenta?'];
                if (!window.confirm(getLanguage() === 'en' ? message : '¿Desactivar esta cuenta?')) {
                    event.preventDefault();
                }
            });
        });
    }

    function initializeLanguage() {
        document.querySelectorAll('[data-language]').forEach((button) => {
            button.addEventListener('click', () => {
                localStorage.setItem(languageKey, button.dataset.language);
                translatePage();
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
