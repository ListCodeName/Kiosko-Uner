{{-- MÓDULO: GESTIÓN DE PERSONAL – Super Admin --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="searchInput" placeholder="🔍  Buscar por nombre, DNI o correo...">
    <select class="filter-select" id="filterRole">
        <option value="">Todos los roles</option>
        <option value="alumno">Alumno</option>
        <option value="profesor">Profesor</option>
        <option value="directivo">Directivo</option>
    </select>
    <button class="btn btn-gen" id="btnNewPersonnel">＋ Nuevo Personal</button>
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
        <tbody id="personnelBody"></tbody>
    </table>
    <div class="table-empty" id="tableEmpty">
        No hay personal registrado. Hacé clic en "＋ Nuevo Personal" para comenzar.
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODALS
     ═══════════════════════════════════════════════════════════ --}}

{{-- Modal: Crear Personal --}}
<div class="modal-overlay" id="createModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="createFormTitle">Nuevo Personal</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="createForm">
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input class="form-input" name="nombre" required placeholder="Juan">
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input class="form-input" name="apellido" required placeholder="Pérez">
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">DNI</label>
                    <input class="form-input" name="dni" required placeholder="12345678">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" name="telefono" placeholder="3434-000000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" name="correo" required placeholder="usuario@uner.edu.ar">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Usuario (login)</label>
                    <input class="form-input" name="username" required placeholder="jperez">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input class="form-input" type="password" name="password" required minlength="4" placeholder="••••••••">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Rol</label>
                <select class="form-input form-select" name="role" required>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                    <option value="directivo">Directivo</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Crear Personal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Personal --}}
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Editar Personal</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="editForm">
            <input type="hidden" name="id" id="edit-id">
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input class="form-input" name="nombre" id="edit-nombre" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input class="form-input" name="apellido" id="edit-apellido" required>
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">DNI</label>
                    <input class="form-input" name="dni" id="edit-dni" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" name="telefono" id="edit-telefono">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" name="correo" id="edit-correo" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Cambiar Rol --}}
<div class="modal-overlay" id="roleModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cambiar Rol</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="roleForm">
            <input type="hidden" name="id" id="role-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:16px">
                Cambiar rol de: <strong style="color:var(--text-white)" id="role-name"></strong>
            </p>
            <div class="form-group">
                <label class="form-label">Nuevo Rol</label>
                <select class="form-input form-select" name="role" id="role-select" required>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                    <option value="directivo">Directivo</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Cambiar Rol</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Cambiar Contraseña --}}
<div class="modal-overlay" id="passwordModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cambiar Contraseña</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="passwordForm">
            <input type="hidden" name="id" id="pw-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:16px">
                Nueva contraseña para: <strong style="color:var(--text-white)" id="pw-name"></strong>
            </p>
            <div class="form-group">
                <label class="form-label">Nueva contraseña</label>
                <input class="form-input" type="password" name="password" id="pw-input" required minlength="4" placeholder="••••••••">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Actualizar Contraseña</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Confirmar Eliminación --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Eliminación</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="deleteForm">
            <input type="hidden" name="id" id="del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">
                ¿Estás seguro de que querés eliminar a:
            </p>
            <p style="color:var(--red);font-weight:700;font-size:1rem;margin-bottom:16px" id="del-name"></p>
            <p style="color:var(--text-muted);font-size:.8rem;margin-bottom:16px">
                Se eliminará el registro de personal y su cuenta de usuario asociada. Esta acción no se puede deshacer.
            </p>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>
