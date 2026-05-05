{{-- MÓDULO: GRUPOS – Profesor --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="groupSearch" placeholder="🔍  Buscar grupo...">
    <button class="btn btn-gen" onclick="document.getElementById('groupCreateModal').classList.add('visible')">＋ Nuevo Grupo</button>
</div>

{{-- Groups grid --}}
<div class="groups-grid">

    {{-- Grupo 1 --}}
    <div class="group-card">
        <div class="group-card-header">
            <div class="group-card-title">
                <span class="group-icon">🟢</span>
                <h3>Grupo A - Mañana</h3>
            </div>
            <div class="group-card-actions">
                <button class="action-btn" title="Editar grupo" onclick="document.getElementById('groupEditModal').classList.add('visible')">✏️</button>
                <button class="action-btn danger" title="Eliminar grupo">🗑️</button>
            </div>
        </div>
        <div class="group-card-meta">
            <span class="group-meta-item">👥 6 alumnos</span>
            <span class="group-meta-item">📊 92% participación</span>
        </div>
        <div class="group-members">
            <div class="member-chip">
                <span class="member-avatar">MG</span>
                <span>García, Martín</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">CL</span>
                <span>López, Camila</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">AR</span>
                <span>Rodríguez, Ana</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">LF</span>
                <span>Fernández, Lucas</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">JT</span>
                <span>Torres, Joaquín</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">VP</span>
                <span>Peralta, Valentina</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
        </div>
        <button class="group-add-btn" onclick="document.getElementById('groupAddMemberModal').classList.add('visible')">＋ Agregar alumno</button>
    </div>

    {{-- Grupo 2 --}}
    <div class="group-card">
        <div class="group-card-header">
            <div class="group-card-title">
                <span class="group-icon">🔵</span>
                <h3>Grupo B - Tarde</h3>
            </div>
            <div class="group-card-actions">
                <button class="action-btn" title="Editar grupo">✏️</button>
                <button class="action-btn danger" title="Eliminar grupo">🗑️</button>
            </div>
        </div>
        <div class="group-card-meta">
            <span class="group-meta-item">👥 5 alumnos</span>
            <span class="group-meta-item">📊 78% participación</span>
        </div>
        <div class="group-members">
            <div class="member-chip">
                <span class="member-avatar">DM</span>
                <span>Martínez, Diego</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">LS</span>
                <span>Sánchez, Lucía</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">NC</span>
                <span>Castro, Nicolás</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">FH</span>
                <span>Herrera, Florencia</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">EA</span>
                <span>Acosta, Emiliano</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
        </div>
        <button class="group-add-btn" onclick="document.getElementById('groupAddMemberModal').classList.add('visible')">＋ Agregar alumno</button>
    </div>

    {{-- Grupo 3 --}}
    <div class="group-card">
        <div class="group-card-header">
            <div class="group-card-title">
                <span class="group-icon">🟡</span>
                <h3>Grupo C - Noche</h3>
            </div>
            <div class="group-card-actions">
                <button class="action-btn" title="Editar grupo">✏️</button>
                <button class="action-btn danger" title="Eliminar grupo">🗑️</button>
            </div>
        </div>
        <div class="group-card-meta">
            <span class="group-meta-item">👥 4 alumnos</span>
            <span class="group-meta-item">📊 85% participación</span>
        </div>
        <div class="group-members">
            <div class="member-chip">
                <span class="member-avatar">MR</span>
                <span>Ruiz, Matías</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">JV</span>
                <span>Vega, Julieta</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">TO</span>
                <span>Ortiz, Tomás</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
            <div class="member-chip">
                <span class="member-avatar">IG</span>
                <span>Gómez, Isabella</span>
                <button class="member-remove" title="Quitar del grupo">✕</button>
            </div>
        </div>
        <button class="group-add-btn" onclick="document.getElementById('groupAddMemberModal').classList.add('visible')">＋ Agregar alumno</button>
    </div>

</div>

{{-- Modal: Crear Grupo --}}
<div class="modal-overlay" id="groupCreateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nuevo Grupo</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <form class="modal-body" onsubmit="event.preventDefault(); this.closest('.modal-overlay').classList.remove('visible')">
            <div class="form-group">
                <label class="form-label">Nombre del grupo</label>
                <input class="form-input" required placeholder="Ej: Grupo D - Intensivo">
            </div>
            <div class="form-group">
                <label class="form-label">Descripción (opcional)</label>
                <input class="form-input" placeholder="Breve descripción del grupo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit">Crear Grupo</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Grupo --}}
<div class="modal-overlay" id="groupEditModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Editar Grupo</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <form class="modal-body" onsubmit="event.preventDefault(); this.closest('.modal-overlay').classList.remove('visible')">
            <div class="form-group">
                <label class="form-label">Nombre del grupo</label>
                <input class="form-input" value="Grupo A - Mañana" required>
            </div>
            <div class="form-group">
                <label class="form-label">Descripción</label>
                <input class="form-input" value="Turno mañana, aula 3">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Agregar Alumno al Grupo --}}
<div class="modal-overlay" id="groupAddMemberModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Agregar Alumno al Grupo</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Buscar alumno</label>
                <input class="form-input" placeholder="🔍  Nombre o DNI...">
            </div>
            <div class="member-select-list">
                <label class="member-select-item">
                    <input type="checkbox"> <span class="member-avatar" style="width:24px;height:24px;font-size:.6rem">DM</span> Martínez, Diego
                </label>
                <label class="member-select-item">
                    <input type="checkbox"> <span class="member-avatar" style="width:24px;height:24px;font-size:.6rem">LS</span> Sánchez, Lucía
                </label>
                <label class="member-select-item">
                    <input type="checkbox"> <span class="member-avatar" style="width:24px;height:24px;font-size:.6rem">NC</span> Castro, Nicolás
                </label>
                <label class="member-select-item">
                    <input type="checkbox" checked> <span class="member-avatar" style="width:24px;height:24px;font-size:.6rem">FH</span> Herrera, Florencia
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="button" class="btn-submit" onclick="this.closest('.modal-overlay').classList.remove('visible')">Confirmar</button>
            </div>
        </div>
    </div>
</div>
