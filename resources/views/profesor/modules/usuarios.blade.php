{{-- MÓDULO: USUARIOS – Profesor --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="userSearch" placeholder="🔍  Buscar por nombre, DNI o correo...">
    <select class="filter-select" id="userFilterRole">
        <option value="">Todos</option>
        <option value="alumno">Alumnos</option>
        <option value="profesor">Profesores</option>
    </select>
    <button class="btn btn-gen" id="btnNewUser" onclick="document.getElementById('userCreateModal').classList.add('visible')">＋ Nuevo Usuario</button>
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
        <tbody>
            <tr>
                <td>40123456</td>
                <td>García, Martín</td>
                <td>3434-601234</td>
                <td>mgarcia@uner.edu.ar</td>
                <td><span class="role-badge alumno">alumno</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña">🔒</button>
                    <button class="action-btn danger" title="Eliminar">🗑️</button>
                </td>
            </tr>
            <tr>
                <td>41234567</td>
                <td>López, Camila</td>
                <td>3434-605678</td>
                <td>clopez@uner.edu.ar</td>
                <td><span class="role-badge alumno">alumno</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña">🔒</button>
                    <button class="action-btn danger" title="Eliminar">🗑️</button>
                </td>
            </tr>
            <tr>
                <td>39876543</td>
                <td>Rodríguez, Ana</td>
                <td>3434-609012</td>
                <td>arodriguez@uner.edu.ar</td>
                <td><span class="role-badge alumno">alumno</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña">🔒</button>
                    <button class="action-btn danger" title="Eliminar">🗑️</button>
                </td>
            </tr>
            <tr>
                <td>42345678</td>
                <td>Fernández, Lucas</td>
                <td>3434-613456</td>
                <td>lfernandez@uner.edu.ar</td>
                <td><span class="role-badge alumno">alumno</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña">🔒</button>
                    <button class="action-btn danger" title="Eliminar">🗑️</button>
                </td>
            </tr>
            <tr>
                <td>38765432</td>
                <td>Méndez, Sofía</td>
                <td>3434-617890</td>
                <td>smendez@uner.edu.ar</td>
                <td><span class="role-badge profesor">profesor</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña">🔒</button>
                    <button class="action-btn danger" title="Eliminar">🗑️</button>
                </td>
            </tr>
            <tr>
                <td>43456789</td>
                <td>Torres, Joaquín</td>
                <td>3434-621234</td>
                <td>jtorres@uner.edu.ar</td>
                <td><span class="role-badge alumno">alumno</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña">🔒</button>
                    <button class="action-btn danger" title="Eliminar">🗑️</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Modal: Crear Usuario --}}
<div class="modal-overlay" id="userCreateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nuevo Usuario</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <form class="modal-body" onsubmit="event.preventDefault(); this.closest('.modal-overlay').classList.remove('visible')">
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input class="form-input" required placeholder="Juan">
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input class="form-input" required placeholder="Pérez">
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">DNI</label>
                    <input class="form-input" required placeholder="12345678">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" placeholder="3434-000000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" required placeholder="usuario@uner.edu.ar">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Usuario (login)</label>
                    <input class="form-input" required placeholder="jperez">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input class="form-input" type="password" required placeholder="••••••••">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Rol</label>
                <select class="form-input form-select" required>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>
