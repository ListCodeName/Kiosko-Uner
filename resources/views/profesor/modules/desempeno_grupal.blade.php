{{-- MÓDULO: DESEMPEÑO GRUPAL – Profesor --}}

{{-- Header con filtros --}}
<div class="perf-header">
    <h2 class="perf-title">Desempeño por Grupo</h2>
    <div class="perf-filters">
        <select class="filter-select" id="perfGrpPeriod">
            <option value="day">Hoy</option>
            <option value="month" selected>Este mes</option>
            <option value="year">Este año</option>
        </select>
    </div>
</div>

{{-- Groups performance grid --}}
<div class="perf-grid">

    {{-- Grupo A --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <span class="group-icon" style="font-size:1.4rem">🟢</span>
            <div>
                <div class="perf-card-name">Grupo A - Mañana</div>
                <div class="perf-card-sub">6 alumnos</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia promedio</span><span>89%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:89%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>82%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:82%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>86%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:86%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>84%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:84%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">85%</div>
            <div class="perf-score-label">Puntaje grupal</div>
        </div>
    </div>

    {{-- Grupo B --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <span class="group-icon" style="font-size:1.4rem">🔵</span>
            <div>
                <div class="perf-card-name">Grupo B - Tarde</div>
                <div class="perf-card-sub">5 alumnos</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia promedio</span><span>78%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:78%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>71%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:71%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>74%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:74%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>69%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:69%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">73%</div>
            <div class="perf-score-label">Puntaje grupal</div>
        </div>
    </div>

    {{-- Grupo C --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <span class="group-icon" style="font-size:1.4rem">🟡</span>
            <div>
                <div class="perf-card-name">Grupo C - Noche</div>
                <div class="perf-card-sub">4 alumnos</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia promedio</span><span>92%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:92%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>88%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:88%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>90%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:90%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>86%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:86%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">89%</div>
            <div class="perf-score-label">Puntaje grupal</div>
        </div>
    </div>

</div>
