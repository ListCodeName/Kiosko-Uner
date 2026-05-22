{{-- MÓDULO: USUARIOS – Panel del Alumno --}}
{{-- Solo permite cambiar la contraseña de otros alumnos. --}}
{{-- Gestión completa (crear, editar, eliminar) es responsabilidad del Profesor. --}}

<style>
/* ── Aviso informativo ── */
.usr-notice {
    display: flex; align-items: flex-start; gap: 12px;
    background: rgba(45,140,255,.08);
    border: 1px solid rgba(45,140,255,.22);
    border-radius: var(--radius-md);
    padding: 14px 18px;
    margin-bottom: 20px;
}
.usr-notice-icon { font-size: 1.3rem; flex-shrink: 0; margin-top: 1px; }
.usr-notice-text { font-size: .82rem; color: var(--text-secondary); line-height: 1.55; }
.usr-notice-text strong { color: var(--text-primary); }

/* ── Toolbar ── */
.usr-toolbar {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 16px; flex-wrap: wrap;
}
.usr-search {
    flex: 1; min-width: 200px;
    padding: 10px 16px;
    background: var(--bg-input);
    border: 1px solid var(--border-dim);
    border-radius: var(--radius-sm);
    color: var(--text-white);
    font-family: inherit; font-size: .85rem;
    outline: none;
    transition: border-color var(--transition);
}
.usr-search::placeholder { color: var(--text-muted); }
.usr-search:focus { border-color: var(--primary); }

/* ── Tabla ── */
.usr-table-wrap {
    overflow-x: auto;
    border: 1px solid var(--border-dim);
    border-radius: var(--radius-md);
    background: var(--bg-card);
}
.usr-table {
    width: 100%; border-collapse: collapse; white-space: nowrap;
}
.usr-table th {
    padding: 11px 16px;
    text-align: left;
    font-size: .72rem; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 1px;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border-dim);
}
.usr-table td {
    padding: 11px 16px;
    font-size: .84rem; color: var(--text-primary);
    border-bottom: 1px solid rgba(255,255,255,.04);
}
.usr-table tbody tr:last-child td { border-bottom: none; }
.usr-table tbody tr { transition: background var(--transition); }
.usr-table tbody tr:hover { background: var(--bg-hover); }
.usr-empty {
    padding: 48px 16px;
    text-align: center;
    color: var(--text-muted); font-size: .88rem;
}

/* ── Badge alumno ── */
.usr-badge {
    display: inline-flex; padding: 3px 10px;
    border-radius: 12px; font-size: .72rem; font-weight: 600;
    background: rgba(45,140,255,.1);
    color: var(--primary, #2d8cff);
    border: 1px solid rgba(45,140,255,.2);
}

/* ── Botón acción ── */
.usr-action-btn {
    background: none;
    border: 1px solid var(--border-dim);
    color: var(--text-secondary);
    padding: 5px 9px;
    border-radius: var(--radius-sm);
    cursor: pointer; font-size: .85rem;
    transition: all var(--transition);
}
.usr-action-btn:hover {
    border-color: var(--primary, #2d8cff);
    color: var(--primary, #2d8cff);
    background: rgba(45,140,255,.08);
}
</style>

{{-- Aviso informativo --}}
<div class="usr-notice">
    <span class="usr-notice-icon">ℹ️</span>
    <div class="usr-notice-text">
        <strong>Módulo de compañeros</strong> — Aquí podés ver los alumnos del sistema y
        <strong>cambiarles la contraseña</strong> en caso de que la hayan olvidado.
        Para crear, editar o eliminar cuentas, contactá al <strong>Profesor</strong>.
    </div>
</div>

{{-- Buscador --}}
<div class="usr-toolbar">
    <input
        class="usr-search"
        type="text"
        id="usrSearch"
        placeholder="🔍  Buscar por nombre, apellido o DNI…"
        autocomplete="off"
    >
</div>

{{-- Tabla de alumnos --}}
<div class="usr-table-wrap">
    <table class="usr-table">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre Completo</th>
                <th>Usuario (login)</th>
                <th>Rol</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="usrTableBody">
            <tr>
                <td colspan="5" class="usr-empty">Cargando compañeros…</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ══════════════════ MODAL: Cambiar Contraseña ══════════════════ --}}
<div class="modal-overlay" id="usrPasswordModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cambiar Contraseña</h3>
            <button type="button" class="modal-close" id="usrPwdClose">✕</button>
        </div>
        <form class="modal-body" id="usrPasswordForm">
            <input type="hidden" id="usrPwdTargetId">
            <p style="color:var(--text-secondary);font-size:.85rem;margin-bottom:4px">
                Nueva contraseña para:
            </p>
            <p style="color:var(--text-white);font-weight:700;font-size:1rem;margin-bottom:16px" id="usrPwdTargetName"></p>
            <div class="form-group">
                <label class="form-label">Nueva contraseña</label>
                <input
                    class="form-input"
                    type="password"
                    id="usrPwdInput"
                    required
                    minlength="4"
                    placeholder="Mínimo 4 caracteres"
                >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="usrPwdCancel">Cancelar</button>
                <button type="submit" class="btn-submit">Actualizar contraseña</button>
            </div>
        </form>
    </div>
</div>
