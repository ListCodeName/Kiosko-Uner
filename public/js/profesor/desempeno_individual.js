/**
 * KIOSKO-UNER | MÓDULO DESEMPEÑO INDIVIDUAL – Profesor
 * Cards premium con datos reales de todos los alumnos del profesor.
 * Modal asistencia: calendario mensual  |  Modal actividad: log de acciones
 */
(function () {
    'use strict';

    /* ── URLs de API ────────────────────────────────────────── */
    const API_PERF    = '/profesor/api/performance/individual';
    const API_ATT_DET = '/profesor/api/performance/attendance-detail';
    const API_ACT_DET = '/profesor/api/performance/activity-detail';

    /* ── Estado local ───────────────────────────────────────── */
    const state = {
        attStudentId:   null,
        attStudentName: '',
        attMonth:       null,   // Date (1er día del mes en curso)
    };

    /* ── Config de tipos de acción ──────────────────────────── */
    const ACTION_META = {
        login:  { icon: '🔐', label: 'Ingresos',  cls: 'act-login'  },
        insert: { icon: '➕', label: 'Altas',      cls: 'act-insert' },
        update: { icon: '✏️', label: 'Ediciones', cls: 'act-update' },
        delete: { icon: '🗑️', label: 'Bajas',     cls: 'act-delete' },
        sale:   { icon: '💳', label: 'Ventas',     cls: 'act-sale'   },
    };

    /* ── Helpers ────────────────────────────────────────────── */
    function getToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }
    function jsonHeaders() {
        return { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken() };
    }
    function getInitials(name) {
        return (name || '?').split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }
    function toISO(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }
    function relativeTime(isoStr) {
        const diff = (Date.now() - new Date(isoStr).getTime()) / 1000;
        if (diff < 60)    return 'hace un momento';
        if (diff < 3600)  return `hace ${Math.floor(diff / 60)} min`;
        if (diff < 86400) return `hace ${Math.floor(diff / 3600)} h`;
        return `hace ${Math.floor(diff / 86400)} días`;
    }

    const MONTHS_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                       'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    /* ══════════════════════════════════════════════════════════
     * CARGA Y RENDER DE CARDS
     * ══════════════════════════════════════════════════════════ */
    async function loadPerformance() {
        const grid = document.getElementById('perfIndGrid');
        if (!grid) return;

        grid.innerHTML = `<div class="perf-ind-empty"><div class="asist-spinner"></div></div>`;

        try {
            const res = await fetch(API_PERF, { headers: jsonHeaders() });
            if (!res.ok) throw new Error();
            const { students } = await res.json();

            // Actualizar badge del header
            const badge = document.getElementById('perfIndSummaryBadge');
            if (badge) {
                badge.style.display = 'inline-flex';
                badge.textContent   = `👤 ${students.length} alumno${students.length !== 1 ? 's' : ''}`;
            }

            if (students.length === 0) {
                grid.innerHTML = `<div class="perf-ind-empty">
                    <div style="font-size:2.5rem;margin-bottom:10px">👥</div>
                    <div style="font-size:.95rem;font-weight:700;color:var(--text-white)">Sin alumnos aún</div>
                    <div style="font-size:.8rem;color:var(--text-muted);margin-top:6px;max-width:260px">
                        Asigná alumnos a tus grupos en el módulo de Grupos para ver su desempeño.
                    </div>
                </div>`;
                return;
            }

            grid.innerHTML = students.map(s => renderCard(s)).join('');

        } catch {
            grid.innerHTML = `<div class="perf-ind-empty">
                <div style="font-size:2rem;margin-bottom:8px">⚠️</div>
                <div style="color:var(--red);font-weight:600">Error al cargar datos</div>
            </div>`;
            showToast('Error al cargar desempeño', 'error');
        }
    }

    /* ── Determina el color del acento superior según % asistencia ── */
    function accentColor(pct) {
        if (pct === null) return '';
        if (pct >= 80)    return 'green';
        if (pct >= 60)    return 'yellow';
        return 'red';
    }

    /* ── Determina la clase del anillo de score ── */
    function ringClass(pct) {
        if (pct === null) return 'none';
        if (pct >= 80)    return 'high';
        if (pct >= 60)    return 'mid';
        return 'low';
    }

    function renderCard(student) {
        const initials = getInitials(student.name);

        /* ── Asistencia ─────────────────────────────────── */
        const attPct  = student.attendance_pct;
        const attFill = attPct !== null ? attPct : 0;
        const attText = attPct !== null
            ? `${student.att_present} / ${student.att_total} días`
            : 'Sin datos';
        const attPctText = attPct !== null ? `${attPct}%` : '—';

        /* ── Actividad ──────────────────────────────────── */
        const actTotal = student.activity_total || 0;
        const actFill  = Math.min(actTotal * 2, 100);
        const actText  = actTotal > 0
            ? `${actTotal} acción${actTotal !== 1 ? 'es' : ''}`
            : 'Sin registros';

        /* ── Grupos del alumno (chips) ──────────────────── */
        const groupsHtml = (student.groups || [])
            .map(g => `<span class="perf-card-group-chip">📁 ${g}</span>`)
            .join('');

        /* ── Acento y ring ──────────────────────────────── */
        const accent = accentColor(attPct);
        const ring   = ringClass(attPct);
        const ringEmoji = ring === 'high' ? '🟢' : ring === 'mid' ? '🟡' : ring === 'low' ? '🔴' : '⬜';

        return `
        <div class="perf-card">

            <div class="perf-card-accent ${accent}"></div>

            <div class="perf-card-header">
                <div class="perf-card-avatar">${initials}</div>
                <div class="perf-card-info">
                    <div class="perf-card-name" title="${student.name}">${student.name}</div>
                    <div class="perf-card-groups">
                        ${groupsHtml || '<span class="perf-card-group-chip" style="opacity:.5">Sin grupo</span>'}
                    </div>
                </div>
            </div>

            <div class="perf-card-body">

                <div class="perf-metric-row">
                    <div class="perf-metric-header">
                        <div class="perf-metric-left">
                            <div class="perf-metric-icon blue">📅</div>
                            <span class="perf-metric-label">
                                Asistencia
                                <button class="perf-detail-btn"
                                    title="Ver detalle de asistencia"
                                    data-action="att-detail"
                                    data-student-id="${student.id}"
                                    data-student-name="${student.name}">🔍</button>
                            </span>
                        </div>
                        <span class="perf-metric-value">${attPctText}</span>
                    </div>
                    <div class="perf-metric-bar-wrap">
                        <div class="perf-metric-bar-track"
                             data-action="att-detail"
                             data-student-id="${student.id}"
                             data-student-name="${student.name}">
                            <div class="perf-metric-bar-fill blue" style="width:${attFill}%"></div>
                        </div>
                    </div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">${attText}</div>
                </div>

                <div class="perf-metric-row">
                    <div class="perf-metric-header">
                        <div class="perf-metric-left">
                            <div class="perf-metric-icon green">⚡</div>
                            <span class="perf-metric-label">
                                Participación activa
                                <button class="perf-detail-btn"
                                    title="Ver detalle de participación"
                                    data-action="act-detail"
                                    data-student-id="${student.id}"
                                    data-student-name="${student.name}">🔍</button>
                            </span>
                        </div>
                        <span class="perf-metric-value">${actTotal}</span>
                    </div>
                    <div class="perf-metric-bar-wrap">
                        <div class="perf-metric-bar-track"
                             data-action="act-detail"
                             data-student-id="${student.id}"
                             data-student-name="${student.name}">
                            <div class="perf-metric-bar-fill green" style="width:${actFill}%"></div>
                        </div>
                    </div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">${actText}</div>
                </div>

            </div>

            <div class="perf-card-score">
                <div class="perf-score-left">
                    <div class="perf-score-label">Asistencia general</div>
                    <div class="perf-score-value">${attPctText}</div>
                    <div class="perf-score-sub">${student.att_present || 0} pres. · ${(student.att_total || 0) - (student.att_present || 0)} aus.</div>
                </div>
                <div class="perf-score-ring ${ring}" title="${attPctText} de asistencia">
                    ${ringEmoji}
                </div>
            </div>

        </div>`;
    }

    /* ══════════════════════════════════════════════════════════
     * MODAL: DETALLE DE ASISTENCIA (calendario mensual)
     * ══════════════════════════════════════════════════════════ */
    function openAttendanceDetail(studentId, studentName) {
        state.attStudentId   = studentId;
        state.attStudentName = studentName;
        state.attMonth       = new Date();
        state.attMonth.setDate(1);
        state.attMonth.setHours(0, 0, 0, 0);

        document.getElementById('attDetailTitle').textContent = `Asistencia – ${studentName}`;
        document.getElementById('attDetailSub').textContent   = 'Navegá mes a mes para ver el registro de asistencia';
        openModal('modalAttendanceDetail');

        fetchMonthAttendance();
    }

    function navigateMonth(delta) {
        if (!state.attMonth) return;
        state.attMonth.setMonth(state.attMonth.getMonth() + delta);
        fetchMonthAttendance();
    }

    async function fetchMonthAttendance() {
        const monthStr = `${state.attMonth.getFullYear()}-${String(state.attMonth.getMonth() + 1).padStart(2, '0')}`;

        document.getElementById('attCalendarLoading').style.display = 'flex';
        document.getElementById('attCalendarWrap').style.display    = 'none';
        document.getElementById('attMonthStats').innerHTML          = '';
        document.getElementById('attMonthLabel').textContent        = 'Cargando...';

        try {
            const params = new URLSearchParams({ student_id: state.attStudentId, month: monthStr });
            const res = await fetch(`${API_ATT_DET}?${params}`, { headers: jsonHeaders() });
            if (!res.ok) throw new Error();
            renderMonthCalendar(await res.json());
        } catch {
            showToast('Error al cargar asistencia', 'error');
            document.getElementById('attCalendarLoading').style.display = 'none';
        }
    }

    function renderMonthCalendar(data) {
        const { month_label, day_map, stats } = data;

        document.getElementById('attMonthLabel').textContent = month_label
            || `${MONTHS_ES[state.attMonth.getMonth()]} ${state.attMonth.getFullYear()}`;

        const pct = stats.pct !== null ? `${stats.pct}%` : '—';
        document.getElementById('attMonthStats').innerHTML = `
            <span class="asist-stat-pill present"><span class="asist-stat-icon">✓</span>${stats.present} presente${stats.present !== 1 ? 's' : ''}</span>
            <span class="asist-stat-pill absent"><span class="asist-stat-icon">✗</span>${stats.absent} ausente${stats.absent !== 1 ? 's' : ''}</span>
            <span class="asist-stat-pill unmarked"><span class="asist-stat-icon">○</span>${pct} de asistencia</span>
        `;

        const year      = state.attMonth.getFullYear();
        const month_num = state.attMonth.getMonth();
        const firstDay  = new Date(year, month_num, 1);
        const lastDay   = new Date(year, month_num + 1, 0);

        let startDow = firstDay.getDay();
        startDow = startDow === 0 ? 6 : startDow - 1;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let html = '';
        for (let i = 0; i < startDow; i++) {
            html += `<div class="att-cal-day att-cal-offmonth"></div>`;
        }
        for (let d = 1; d <= lastDay.getDate(); d++) {
            const cur     = new Date(year, month_num, d);
            const dateStr = toISO(cur);
            const isToday = cur.getTime() === today.getTime();
            const val     = Object.prototype.hasOwnProperty.call(day_map, dateStr) ? day_map[dateStr] : undefined;

            let stateClass = 'att-cal-unmarked';
            if (val === 1) stateClass = 'att-cal-present';
            else if (val === 0) stateClass = 'att-cal-absent';

            html += `<div class="att-cal-day ${stateClass}${isToday ? ' att-cal-today' : ''}" title="${dateStr}">
                        <span class="att-cal-day-num">${d}</span>
                        <span class="att-cal-day-dot"></span>
                     </div>`;
        }

        const remainder = (startDow + lastDay.getDate()) % 7;
        if (remainder !== 0) {
            for (let i = 0; i < 7 - remainder; i++) {
                html += `<div class="att-cal-day att-cal-offmonth"></div>`;
            }
        }

        document.getElementById('attCalendarGrid').innerHTML        = html;
        document.getElementById('attCalendarLoading').style.display = 'none';
        document.getElementById('attCalendarWrap').style.display    = 'block';
    }

    /* ══════════════════════════════════════════════════════════
     * MODAL: DETALLE DE PARTICIPACIÓN ACTIVA
     * ══════════════════════════════════════════════════════════ */
    async function openActivityDetail(studentId, studentName) {
        document.getElementById('actDetailTitle').textContent = `Participación – ${studentName}`;
        document.getElementById('actDetailSub').textContent   = 'Registro de ingresos y acciones en el sistema';
        openModal('modalActivityDetail');

        document.getElementById('actDetailLoading').style.display = 'flex';
        document.getElementById('actDetailContent').style.display = 'none';
        document.getElementById('actDetailEmpty').style.display   = 'none';

        try {
            const res = await fetch(`${API_ACT_DET}?student_id=${studentId}`, { headers: jsonHeaders() });
            if (!res.ok) throw new Error();
            renderActivityDetail(await res.json());
        } catch {
            showToast('Error al cargar actividad', 'error');
            document.getElementById('actDetailLoading').style.display = 'none';
        }
    }

    function renderActivityDetail(data) {
        const { total, by_type, recent } = data;
        document.getElementById('actDetailLoading').style.display = 'none';

        if (total === 0) {
            document.getElementById('actDetailEmpty').style.display = 'flex';
            return;
        }

        document.getElementById('actTypeGrid').innerHTML =
            Object.entries(ACTION_META).map(([key, meta]) => {
                const count = by_type[key] || 0;
                return `<div class="act-type-card ${meta.cls}">
                            <div class="act-type-icon">${meta.icon}</div>
                            <div class="act-type-count">${count}</div>
                            <div class="act-type-label">${meta.label}</div>
                        </div>`;
            }).join('');

        const logList = document.getElementById('actLogList');
        logList.innerHTML = recent.length === 0
            ? `<div style="text-align:center;color:var(--text-muted);padding:16px;font-size:.82rem">Sin registros recientes</div>`
            : recent.map(entry => {
                const meta = ACTION_META[entry.action] || { icon: '·', cls: entry.action };
                const desc = entry.description || meta.label || entry.action;
                const moduleChip = entry.module ? `<span class="act-log-module-chip">${entry.module}</span>` : '';

                return `<div class="activity-log-item">
                            <span class="act-log-dot ${entry.action}"></span>
                            <span class="act-log-desc" title="${desc}">${meta.icon} ${moduleChip}${desc}</span>
                            <span class="act-log-time">${relativeTime(entry.created_at)}</span>
                        </div>`;
            }).join('');

        document.getElementById('actDetailContent').style.display = 'block';
    }

    /* ── showToast ──────────────────────────────────────────── */
    function showToast(msg, type = 'success') {
        if (typeof window.showToast === 'function') window.showToast(msg, type);
    }

    /* ══════════════════════════════════════════════════════════
     * INIT
     * ══════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => {

        // Carga automática al iniciar (sin esperar selección de grupo)
        loadPerformance();

        // Recargar cuando el módulo se activa desde sidebar
        document.querySelectorAll('.nav-item[data-module="desempeno-individual"]').forEach(btn => {
            btn.addEventListener('click', () => loadPerformance());
        });

        // Delegación de clicks en el grid
        const grid = document.getElementById('perfIndGrid');
        if (grid) {
            grid.addEventListener('click', e => {
                const trigger = e.target.closest('[data-action]');
                if (!trigger) return;
                const action      = trigger.dataset.action;
                const studentId   = parseInt(trigger.dataset.studentId, 10);
                const studentName = trigger.dataset.studentName || '';

                if (action === 'att-detail') openAttendanceDetail(studentId, studentName);
                if (action === 'act-detail') openActivityDetail(studentId, studentName);
            });
        }

        // Navegación de meses
        document.getElementById('attMonthPrev')?.addEventListener('click', () => navigateMonth(-1));
        document.getElementById('attMonthNext')?.addEventListener('click', () => navigateMonth(1));

    });

})();
