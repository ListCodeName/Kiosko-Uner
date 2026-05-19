/**
 * KIOSKO-UNER | MÓDULO ASISTENCIA – Profesor
 * Gestión de asistencia semanal por grupo
 */
(function () {
    'use strict';

    /* ── Estado del módulo ──────────────────────────────────── */
    const state = {
        groups: [],
        groupId: null,
        weekStart: null,       // Lunes de la semana activa (Date)
        data: null,            // Respuesta de la API
        savingKeys: new Set(), // Claves en proceso de guardado
    };

    const GROUPS_API   = '/profesor/api/groups';
    const ATTEND_API   = '/profesor/api/attendance';

    /* ── Helpers de fecha ───────────────────────────────────── */
    const DAYS_ES   = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const MONTHS_ES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function getMondayOf(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = (day === 0) ? -6 : 1 - day; // ISO week: lunes = 1
        d.setDate(d.getDate() + diff);
        d.setHours(0, 0, 0, 0);
        return d;
    }

    function addDays(date, n) {
        const d = new Date(date);
        d.setDate(d.getDate() + n);
        return d;
    }

    function toISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function isSameDay(a, b) {
        return a.getFullYear() === b.getFullYear() &&
               a.getMonth()    === b.getMonth()    &&
               a.getDate()     === b.getDate();
    }

    function formatShortDate(date) {
        return `${date.getDate()} ${MONTHS_ES[date.getMonth()].substring(0, 3)}`;
    }

    /* ── CSRF helpers ───────────────────────────────────────── */
    function getToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function jsonHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getToken(),
        };
    }

    /* ── Carga de grupos ────────────────────────────────────── */
    async function loadGroups() {
        try {
            const res = await fetch(GROUPS_API, { headers: jsonHeaders() });
            if (!res.ok) throw new Error();
            state.groups = await res.json();
            populateGroupSelector();
        } catch {
            showToast('Error al cargar grupos', 'error');
        }
    }

    function populateGroupSelector() {
        const sel = document.getElementById('asistGroupSelect');
        if (!sel) return;

        const currentVal = sel.value;

        // Conservar opción vacía
        sel.innerHTML = '<option value="">— Seleccioná un grupo —</option>';
        state.groups.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = `${g.name}${g.students_count ? ` (${g.students_count} alumnos)` : ''}`;
            sel.appendChild(opt);
        });

        if (currentVal && state.groups.some(g => g.id == currentVal)) {
            sel.value = currentVal;
        }
    }

    /* ── Semana activa ──────────────────────────────────────── */
    function goToCurrentWeek() {
        state.weekStart = getMondayOf(new Date());
        updateWeekLabel();
        if (state.groupId) fetchAttendance();
    }

    function prevWeek() {
        if (!state.weekStart) return;
        state.weekStart = addDays(state.weekStart, -7);
        updateWeekLabel();
        if (state.groupId) fetchAttendance();
    }

    function nextWeek() {
        if (!state.weekStart) return;
        state.weekStart = addDays(state.weekStart, 7);
        updateWeekLabel();
        if (state.groupId) fetchAttendance();
    }

    function updateWeekLabel() {
        const monday = state.weekStart;
        if (!monday) return;

        const sunday = addDays(monday, 6);
        const today  = getMondayOf(new Date());

        const rangeEl = document.getElementById('weekRangeText');
        const yearEl  = document.getElementById('weekYearText');
        const todayBtn = document.getElementById('btnCurrentWeek');

        if (rangeEl) {
            // Si lunes y viernes son del mismo mes: "19 – 23 May"
            // Si diferente mes: "28 Abr – 2 May"
            if (monday.getMonth() === sunday.getMonth()) {
                rangeEl.textContent = `${monday.getDate()} – ${sunday.getDate()} ${MONTHS_ES[sunday.getMonth()].substring(0, 3)}`;
            } else {
                rangeEl.textContent = `${formatShortDate(monday)} – ${formatShortDate(sunday)}`;
            }
        }
        if (yearEl) yearEl.textContent = monday.getFullYear();

        // Marcar botón Hoy si estamos en la semana actual
        if (todayBtn) {
            const isCurrentWeek = isSameDay(monday, today);
            todayBtn.classList.toggle('is-current', isCurrentWeek);
        }
    }

    /* ── Fetch de asistencias ───────────────────────────────── */
    async function fetchAttendance() {
        if (!state.groupId || !state.weekStart) return;

        showTableState('loading');

        try {
            const params = new URLSearchParams({
                group_id:   state.groupId,
                week_start: toISO(state.weekStart),
            });
            const res = await fetch(`${ATTEND_API}?${params}`, { headers: jsonHeaders() });
            if (!res.ok) throw new Error();
            state.data = await res.json();
            renderTable();
        } catch {
            showToast('Error al cargar asistencias', 'error');
            showTableState('empty');
        }
    }

    /* ── Render de la tabla ─────────────────────────────────── */
    function renderTable() {
        if (!state.data) return;

        const { students, attendance_map, week_start } = state.data;
        const monday = new Date(week_start + 'T00:00:00'); // evitar timezone offset
        const today  = new Date();
        today.setHours(0, 0, 0, 0);

        // Calcular los 7 días (Lun–Dom)
        const days = Array.from({ length: 7 }, (_, i) => addDays(monday, i));

        /* ─── THEAD ─── */
        const thead = document.getElementById('attendanceThead');
        // Fila 1: encabezados de columna
        let tr1 = '<tr>';
        tr1 += '<th class="att-th-student">Alumno</th>';
        days.forEach(day => {
            const isToday = isSameDay(day, today);
            const dayName = DAYS_ES[day.getDay()];
            const dayDate = formatShortDate(day);
            tr1 += `<th class="att-th-day${isToday ? ' is-today' : ''}">
                        <span class="att-th-day-name">${dayName}</span>
                        <span class="att-th-day-date">${dayDate}</span>
                    </th>`;
        });
        tr1 += '</tr>';

        // Fila 2: resumen por día
        let tr2 = '<tr>';
        tr2 += '<th class="att-th-summary-student">Total del día</th>';
        days.forEach(day => {
            const dateStr = toISO(day);
            let present = 0, absent = 0;
            students.forEach(s => {
                const val = (attendance_map[s.id] || {})[dateStr];
                if (val === 1) present++;
                else if (val === 0) absent++;
            });
            tr2 += `<th>
                        <span class="day-summary-badge" id="summary-${dateStr}">
                            <span class="s-present">✓ ${present}</span>
                            &nbsp;
                            <span class="s-absent">✗ ${absent}</span>
                        </span>
                    </th>`;
        });
        tr2 += '</tr>';
        thead.innerHTML = tr1 + tr2;

        /* ─── TBODY ─── */
        const tbody = document.getElementById('attendanceTbody');
        if (students.length === 0) {
            tbody.innerHTML = `<tr class="att-empty-row"><td colspan="${days.length + 1}">Este grupo no tiene alumnos asignados.</td></tr>`;
            showTableState('table');
            updateWeekStats();
            return;
        }

        let html = '';
        students.forEach(student => {
            const initials = getInitials(student.name);
            html += `<tr class="att-student-row">
                        <td class="att-td-student">
                            <div class="att-student-info">
                                <div class="att-student-avatar">${initials}</div>
                                <div>
                                    <div class="att-student-name">${student.name}</div>
                                    ${student.username ? `<div class="att-student-user">@${student.username}</div>` : ''}
                                </div>
                            </div>
                        </td>`;

            days.forEach(day => {
                const dateStr = toISO(day);
                const val = (attendance_map[student.id] || {})[dateStr];
                // val: null/undefined = sin marcar, 1 = presente, 0 = ausente
                const btnClass = (val === 1) ? 'present' : (val === 0) ? 'absent' : 'unmarked';
                const btnLabel = (val === 1) ? '✓ Pres.' : (val === 0) ? '✗ Aus.' : '— —';
                const btnId = `att-${student.id}-${dateStr}`;

                html += `<td class="att-td-day">
                            <button
                                class="att-btn ${btnClass}"
                                id="${btnId}"
                                data-student="${student.id}"
                                data-date="${dateStr}"
                                data-state="${val === 1 ? 1 : val === 0 ? 0 : 'null'}"
                                title="${student.name} – ${DAYS_ES[day.getDay()]} ${formatShortDate(day)}"
                            >
                                <span>${btnLabel}</span>
                            </button>
                        </td>`;
            });

            html += '</tr>';
        });

        tbody.innerHTML = html;
        showTableState('table');
        updateWeekStats();

        // Delegar clicks en la tabla
        tbody.addEventListener('click', handleToggleClick, { once: false });
    }

    /* ── Toggle handler ─────────────────────────────────────── */
    async function handleToggleClick(e) {
        const btn = e.target.closest('.att-btn');
        if (!btn) return;

        const studentId = parseInt(btn.dataset.student, 10);
        const date      = btn.dataset.date;
        const current   = btn.dataset.state; // '1', '0', 'null'

        // Ciclo: sin marcar → presente → ausente → sin marcar
        let nextVal;
        if (current === 'null') nextVal = 1;
        else if (current === '1') nextVal = 0;
        else nextVal = null;

        // Evitar doble-click
        const key = `${studentId}_${date}`;
        if (state.savingKeys.has(key)) return;
        state.savingKeys.add(key);

        // UI loading
        btn.classList.add('att-loading');

        try {
            const res = await fetch(ATTEND_API, {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify({
                    group_id:   state.groupId,
                    student_id: studentId,
                    date,
                    present:    nextVal,
                }),
            });
            if (!res.ok) throw new Error();

            // Actualizar mapa local
            if (!state.data.attendance_map[studentId]) {
                state.data.attendance_map[studentId] = {};
            }
            state.data.attendance_map[studentId][date] = nextVal;

            // Actualizar botón
            const newClass = (nextVal === 1) ? 'present' : (nextVal === 0) ? 'absent' : 'unmarked';
            const newLabel = (nextVal === 1) ? '✓ Pres.' : (nextVal === 0) ? '✗ Aus.' : '— —';
            btn.className = `att-btn ${newClass}`;
            btn.dataset.state = (nextVal === null) ? 'null' : String(nextVal);
            btn.querySelector('span').textContent = newLabel;

            // Actualizar resumen del día
            updateDaySummary(date);
            updateWeekStats();

        } catch {
            showToast('Error al guardar asistencia', 'error');
        } finally {
            btn.classList.remove('att-loading');
            state.savingKeys.delete(key);
        }
    }

    /* ── Actualizar resumen de un día ───────────────────────── */
    function updateDaySummary(dateStr) {
        const badge = document.getElementById(`summary-${dateStr}`);
        if (!badge || !state.data) return;

        let present = 0, absent = 0;
        state.data.students.forEach(s => {
            const val = (state.data.attendance_map[s.id] || {})[dateStr];
            if (val === 1) present++;
            else if (val === 0) absent++;
        });

        badge.innerHTML = `<span class="s-present">✓ ${present}</span>&nbsp;<span class="s-absent">✗ ${absent}</span>`;
    }

    /* ── Actualizar stats globales de la semana ─────────────── */
    function updateWeekStats() {
        if (!state.data || !state.data.students) {
            document.getElementById('asistWeekStats').style.display = 'none';
            return;
        }

        const { students, attendance_map, week_start } = state.data;
        const monday = new Date(week_start + 'T00:00:00');
        const days   = Array.from({ length: 7 }, (_, i) => toISO(addDays(monday, i)));
        const total  = students.length * days.length;

        let present = 0, absent = 0;
        students.forEach(s => {
            days.forEach(d => {
                const val = (attendance_map[s.id] || {})[d];
                if (val === 1) present++;
                else if (val === 0) absent++;
            });
        });
        const unmarked = total - present - absent;

        document.getElementById('asistWeekStats').style.display = 'flex';
        document.getElementById('statDaysLabel').textContent    = `${students.length} alumnos · 7 días`;
        document.getElementById('statPresentPct').textContent   = `${present} presente${present !== 1 ? 's' : ''}`;
        document.getElementById('statAbsentPct').textContent    = `${absent} ausente${absent !== 1 ? 's' : ''}`;
        document.getElementById('statUnmarkedPct').textContent  = `${unmarked} sin marcar`;
    }

    /* ── Helpers de UI ──────────────────────────────────────── */
    function showTableState(state) {
        document.getElementById('asistEmpty').style.display          = state === 'empty'   ? 'flex' : 'none';
        document.getElementById('asistLoading').style.display        = state === 'loading' ? 'flex' : 'none';
        document.getElementById('asistTableContainer').style.display = state === 'table'   ? 'block' : 'none';
    }

    function getInitials(name) {
        if (!name) return '?';
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    /* ── showToast (compatible con profesor.js) ─────────────── */
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = msg;
        el.className = `toast ${type} visible`;
        clearTimeout(el._t);
        el._t = setTimeout(() => el.classList.remove('visible'), 3500);
    }

    /* ── Init ───────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {

        // Inicializar semana actual
        state.weekStart = getMondayOf(new Date());
        updateWeekLabel();

        // Cargar grupos en el selector
        loadGroups();

        // Selector de grupo
        const sel = document.getElementById('asistGroupSelect');
        if (sel) {
            sel.addEventListener('change', () => {
                state.groupId = sel.value ? parseInt(sel.value, 10) : null;
                state.data    = null;

                if (!state.groupId) {
                    showTableState('empty');
                    document.getElementById('asistWeekStats').style.display = 'none';
                    return;
                }
                fetchAttendance();
            });
        }

        // Botones de navegación de semana
        document.getElementById('btnPrevWeek')?.addEventListener('click', prevWeek);
        document.getElementById('btnNextWeek')?.addEventListener('click', nextWeek);
        document.getElementById('btnCurrentWeek')?.addEventListener('click', goToCurrentWeek);

        // Refrescar cuando el módulo se activa desde el sidebar
        document.querySelectorAll('.nav-item[data-module="asistencia"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const sel = document.getElementById('asistGroupSelect');
                if (sel && state.groupId && sel.value) {
                    fetchAttendance();
                }
            });
        });
    });

    // Exponer públicamente para refrescar dinámicamente desde otros módulos
    window.asistenciaModule = {
        loadGroups
    };

})();
