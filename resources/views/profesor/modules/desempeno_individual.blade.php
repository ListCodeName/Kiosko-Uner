{{-- MÓDULO: DESEMPEÑO INDIVIDUAL – Profesor --}}

{{-- Header con filtros --}}
<div class="perf-header">
    <h2 class="perf-title">Desempeño Individual de Alumnos</h2>
    <div class="perf-filters">
        <select class="filter-select" id="perfIndGroup">
            <option value="">Todos los grupos</option>
            <option value="a" selected>Grupo A - Mañana</option>
            <option value="b">Grupo B - Tarde</option>
            <option value="c">Grupo C - Noche</option>
        </select>
        <select class="filter-select" id="perfIndPeriod">
            <option value="day">Hoy</option>
            <option value="month" selected>Este mes</option>
            <option value="year">Este año</option>
        </select>
    </div>
</div>

{{-- Cards grid --}}
<div class="perf-grid">

    {{-- Alumno 1 --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <div class="member-avatar">MG</div>
            <div>
                <div class="perf-card-name">García, Martín</div>
                <div class="perf-card-sub">Grupo A - Mañana</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia</span><span>95%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:95%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>88%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:88%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>92%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:92%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>85%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:85%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">90%</div>
            <div class="perf-score-label">Puntaje general</div>
        </div>
    </div>

    {{-- Alumno 2 --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <div class="member-avatar">CL</div>
            <div>
                <div class="perf-card-name">López, Camila</div>
                <div class="perf-card-sub">Grupo A - Mañana</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia</span><span>100%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:100%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>94%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:94%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>97%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:97%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>91%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:91%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">96%</div>
            <div class="perf-score-label">Puntaje general</div>
        </div>
    </div>

    {{-- Alumno 3 --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <div class="member-avatar">AR</div>
            <div>
                <div class="perf-card-name">Rodríguez, Ana</div>
                <div class="perf-card-sub">Grupo A - Mañana</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia</span><span>80%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:80%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>65%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:65%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>72%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:72%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>78%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:78%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">74%</div>
            <div class="perf-score-label">Puntaje general</div>
        </div>
    </div>

    {{-- Alumno 4 --}}
    <div class="perf-card">
        <div class="perf-card-header">
            <div class="member-avatar">LF</div>
            <div>
                <div class="perf-card-name">Fernández, Lucas</div>
                <div class="perf-card-sub">Grupo A - Mañana</div>
            </div>
        </div>
        <div class="perf-bar-group">
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Asistencia</span><span>70%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill blue" style="width:70%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Participación activa</span><span>45%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill green" style="width:45%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Tareas completadas</span><span>55%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill purple" style="width:55%"></div></div>
            </div>
            <div class="perf-bar-row">
                <div class="perf-bar-label"><span>Trabajo en equipo</span><span>60%</span></div>
                <div class="perf-bar-track"><div class="perf-bar-fill yellow" style="width:60%"></div></div>
            </div>
        </div>
        <div class="perf-score">
            <div class="perf-score-value">58%</div>
            <div class="perf-score-label">Puntaje general</div>
        </div>
    </div>

</div>
