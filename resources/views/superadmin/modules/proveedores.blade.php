{{-- MÓDULO: GESTIÓN DE PROVEEDORES – Super Admin --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="provSearchInput" placeholder="🔍  Buscar por nombre, contacto o correo...">
    <button class="btn btn-gen" id="btnNewProveedor">＋ Nuevo Proveedor</button>
</div>

{{-- Table --}}
<div class="data-table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="proveedoresBody"></tbody>
    </table>
    <div class="table-empty" id="provTableEmpty">
        No hay proveedores registrados. Hacé clic en "＋ Nuevo Proveedor" para comenzar.
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODALS
     ═══════════════════════════════════════════════════════════ --}}

{{-- Modal: Crear Proveedor --}}
<div class="modal-overlay" id="provCreateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nuevo Proveedor</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="provCreateForm">
            <div class="form-group">
                <label class="form-label">Nombre del proveedor <span style="color:var(--red)">*</span></label>
                <input class="form-input" name="nombre" required placeholder="Ej: Distribuidora ABC">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Persona de contacto</label>
                    <input class="form-input" name="contacto" placeholder="Ej: Juan Pérez">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" name="telefono" placeholder="Ej: 3434-000000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" name="correo" placeholder="proveedor@ejemplo.com">
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input class="form-input" name="direccion" placeholder="Ej: Av. Ramírez 123, Concordia">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Crear Proveedor</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Proveedor --}}
<div class="modal-overlay" id="provEditModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Editar Proveedor</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="provEditForm">
            <input type="hidden" name="id" id="prov-edit-id">
            <div class="form-group">
                <label class="form-label">Nombre del proveedor <span style="color:var(--red)">*</span></label>
                <input class="form-input" name="nombre" id="prov-edit-nombre" required>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Persona de contacto</label>
                    <input class="form-input" name="contacto" id="prov-edit-contacto">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" name="telefono" id="prov-edit-telefono">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" name="correo" id="prov-edit-correo">
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input class="form-input" name="direccion" id="prov-edit-direccion">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Confirmar Eliminación --}}
<div class="modal-overlay" id="provDeleteModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Eliminación</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="provDeleteForm">
            <input type="hidden" name="id" id="prov-del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">
                ¿Estás seguro de que querés eliminar al proveedor:
            </p>
            <p style="color:var(--red);font-weight:700;font-size:1rem;margin-bottom:16px" id="prov-del-name"></p>
            <p style="color:var(--text-muted);font-size:.8rem;margin-bottom:16px">
                Esta acción no se puede deshacer.
            </p>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>
