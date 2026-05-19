{{-- MÓDULO: GRUPOS – Profesor --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="groupSearch" placeholder="🔍  Buscar grupo...">
    <button class="btn btn-gen" onclick="window.gruposModule.openCreateModal()">＋ Nuevo Grupo</button>
</div>

{{-- Groups grid --}}
<div class="groups-grid" id="groupsContainer">
    {{-- Dinámico desde JS --}}
    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 2rem;">
        Cargando grupos...
    </div>
</div>

{{-- Modal: Crear Grupo --}}
<div class="modal-overlay" id="groupCreateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nuevo Grupo</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <form class="modal-body" id="groupCreateForm">
            <div class="form-group">
                <label class="form-label">Nombre del grupo</label>
                <input class="form-input" id="createGroupName" required placeholder="Ej: Grupo D - Intensivo">
            </div>
            <div class="form-group">
                <label class="form-label">Descripción (opcional)</label>
                <input class="form-input" id="createGroupDesc" placeholder="Breve descripción del grupo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit" id="btnCreateGroupSubmit">Crear Grupo</button>
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
        <form class="modal-body" id="groupEditForm">
            <input type="hidden" id="editGroupId">
            <div class="form-group">
                <label class="form-label">Nombre del grupo</label>
                <input class="form-input" id="editGroupName" required>
            </div>
            <div class="form-group">
                <label class="form-label">Descripción</label>
                <input class="form-input" id="editGroupDesc">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="submit" class="btn-submit" id="btnEditGroupSubmit">Guardar Cambios</button>
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
            <input type="hidden" id="addMemberGroupId">
            <div class="form-group">
                <label class="form-label">Buscar alumno</label>
                <input class="form-input" id="studentSearchInput" placeholder="🔍  Nombre o DNI...">
            </div>
            <div class="member-select-list" id="studentSelectList">
                {{-- Dinámico desde JS --}}
                <div style="text-align: center; color: var(--text-secondary); padding: 1rem;">
                    Escribe para buscar alumnos...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="this.closest('.modal-overlay').classList.remove('visible')">Cancelar</button>
                <button type="button" class="btn-submit" id="btnAddMemberSubmit">Confirmar</button>
            </div>
        </div>
    </div>
</div>
