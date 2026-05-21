{{-- MÓDULO: BITÁCORA DE ACTIVIDADES – Profesor --}}

<div class="module-card" style="margin-bottom: 1.5rem; padding: 1.5rem; background: var(--bg-card); border-radius: 16px; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); border: 1px solid var(--border-color);">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 style="margin:0; font-size:1.5rem; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                <span>📖</span> Bitácora de Auditoría
            </h2>
            <p style="margin:0.25rem 0 0 0; font-size:0.875rem; color:var(--text-secondary);">
                Historial en tiempo real de las acciones que modifican la base de datos realizadas por los alumnos a tu cargo.
            </p>
        </div>
        <button class="btn btn-gen" onclick="BitacoraModule.loadLogs()" style="display:flex; align-items:center; gap:0.5rem; background: linear-gradient(135deg, #16a34a, #22c55e); color: white; border: none; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 500; cursor: pointer; transition: all 0.2s;">
            <span>🔄</span> Actualizar Datos
        </button>
    </div>
</div>

{{-- Toolbar de Filtros --}}
<div class="table-toolbar" style="background: var(--bg-card); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid var(--border-color); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
    
    <div style="flex: 1; min-width: 250px; position: relative;">
        <input class="search-input" type="text" id="bitacoraSearch" placeholder="🔍 Buscar por alumno, descripción o acción..." style="width: 100%; padding: 0.625rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-body); color: var(--text-primary); font-size: 0.875rem; outline: none; transition: border 0.2s;">
    </div>
    
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <div style="display:flex; flex-direction:column; gap:0.25rem;">
            <label style="font-size:0.75rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em;">Filtrar por Módulo</label>
            <select class="filter-select" id="bitacoraFilterModule" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary); font-size: 0.875rem; min-width: 140px; cursor: pointer; outline:none;">
                <option value="">Todos los Módulos</option>
                <option value="Kiosco">Kiosco POS</option>
                <option value="Pedidos">Pedidos</option>
                <option value="Productos">Productos</option>
                <option value="Ingresos">Ingresos</option>
                <option value="Egresos">Egresos</option>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:0.25rem;">
            <label style="font-size:0.75rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em;">Filtrar por Operación</label>
            <select class="filter-select" id="bitacoraFilterAction" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary); font-size: 0.875rem; min-width: 140px; cursor: pointer; outline:none;">
                <option value="">Todas las Operaciones</option>
                <option value="INSERT">Altas (INSERT)</option>
                <option value="UPDATE">Ediciones (UPDATE)</option>
                <option value="DELETE">Bajas (DELETE)</option>
                <option value="sale">Ventas POS</option>
                <option value="sale_collect">Cobros POS</option>
                <option value="sale_return">Devoluciones POS</option>
                <option value="order_delivery">Entregas de Pedido</option>
            </select>
        </div>
    </div>
    
</div>

{{-- Tabla Premium de Actividades --}}
<div class="data-table-wrapper" style="background: var(--bg-card); border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 2rem;">
    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: var(--border-color); border-bottom: 2px solid var(--border-color);">
                <th style="padding: 1rem; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary); width: 220px;">Alumno</th>
                <th style="padding: 1rem; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary); width: 140px;">Módulo</th>
                <th style="padding: 1rem; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary); width: 140px;">Operación</th>
                <th style="padding: 1rem; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary);">Descripción de la Actividad</th>
                <th style="padding: 1rem; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary); width: 180px;">Fecha y Hora</th>
            </tr>
        </thead>
        <tbody id="bitacoraTableBody">
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary); font-size: 0.95rem;">
                    <div style="display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
                        <span style="font-size: 2rem; animation: spin 1.5s linear infinite;">⏳</span>
                        <span>Cargando bitácora de actividades...</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<style>
    /* Estilos específicos para la bitácora */
    .module-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .module-badge.kiosco {
        background: rgba(34, 197, 94, 0.1);
        color: #15803d;
    }
    .module-badge.pedidos {
        background: rgba(59, 130, 246, 0.1);
        color: #1d4ed8;
    }
    .module-badge.productos {
        background: rgba(249, 115, 22, 0.1);
        color: #c2410c;
    }
    .module-badge.ingresos {
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
    }
    .module-badge.egresos {
        background: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }

    .action-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .action-badge.insert {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .action-badge.update {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
    .action-badge.delete {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .action-badge.sale {
        background: rgba(6, 182, 212, 0.1);
        color: #0891b2;
        border: 1px solid rgba(6, 182, 212, 0.2);
    }
    .action-badge.other {
        background: rgba(107, 114, 128, 0.1);
        color: #4b5563;
        border: 1px solid rgba(107, 114, 128, 0.2);
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
