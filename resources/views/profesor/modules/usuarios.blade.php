{{-- MÓDULO: USUARIOS – Profesor --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="userSearch" placeholder="🔍  Buscar por nombre, DNI o correo...">
    <select class="filter-select" id="userFilterRole">
        <option value="">Todos</option>
        <option value="alumno">Alumnos</option>
        <option value="profesor">Profesores</option>
    </select>
    <button class="btn btn-gen" id="btnNewUser" onclick="UserModule.openCreateModal()">＋ Nuevo Usuario</button>
</div>

{{-- Table --}}
<div class="data-table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre Completo</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            <!-- Cargado por JS -->
        </tbody>
    </table>
</div>

{{-- Modal: Crear/Editar Usuario --}}
<div class="modal-overlay" id="userCreateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="userModalTitle">Nuevo Usuario</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <form class="modal-body" id="userForm" onsubmit="event.preventDefault(); UserModule.saveUser();">
            <input type="hidden" id="userId">
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input class="form-input" id="userNombre" required placeholder="Juan">
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input class="form-input" id="userApellido" required placeholder="Pérez">
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">DNI</label>
                    <input class="form-input" id="userDni" required placeholder="12345678">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" id="userTelefono" placeholder="3434-000000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" id="userCorreo" required placeholder="usuario@uner.edu.ar">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Usuario (login)</label>
                    <input class="form-input" id="userUsername" required placeholder="jperez">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input class="form-input" type="password" id="userPassword" placeholder="••••••••">
                    <small style="color:var(--text-secondary);font-size:0.75rem;display:none;" id="userPasswordHelp">Déjalo en blanco para no cambiarla.</small>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Rol</label>
                <select class="form-input form-select" id="userRole" required>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Cambiar Contraseña --}}
<div class="modal-overlay" id="userPasswordModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cambiar Contraseña</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <form class="modal-body" id="userPasswordForm" onsubmit="event.preventDefault(); UserModule.savePassword();">
            <input type="hidden" id="pwdUserId">
            <div class="form-group">
                <label class="form-label">Nueva Contraseña</label>
                <input class="form-input" type="password" id="newPassword" required placeholder="Mínimo 4 caracteres" minlength="4">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit">Actualizar Contraseña</button>
            </div>
        </form>
    </div>
</div>
