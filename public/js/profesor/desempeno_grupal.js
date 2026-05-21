/**
 * KIOSKO-UNER | MÓDULO DESEMPEÑO GRUPAL – Profesor
 * Carga datos reales desde /profesor/api/performance/grupal
 * y renderiza:
 *   - Tarjetas expandidas por grupo con desglose por miembro
 *   - Estadísticas globales (media de asistencia y actividad)
 *   - Pódio de más destacados (top 3 asistencia + top 3 actividad)
 *   - Alertas: alumnos por debajo de la media global
 */
(function () {
    'use strict';

    const API_GRUPAL = '/profesor/api/performance/grupal';

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
    function pctText(v) { return v !== null && v !== undefined ? `${v}%` : '—'; }
    function actBar(total) { return Math.min(Math.round(total * 2), 100); }

    /* ── Colores según porcentaje ───────────────────────────── */
    function attColor(pct) {
        if (pct === null || pct === undefined) return 'muted';
        if (pct >= 80) return 'green';
        if (pct >= 60) return 'yellow';
        return 'red';
    }
    function attRing(pct) {
        if (pct === null || pct === undefined) return 'none';
        if (pct >= 80) return 'high';
        if (pct >= 60) return 'mid';
        return 'low';
    }
    function ringEmoji(ring) {
        return ring === 'high' ? '🟢' : ring === 'mid' ? '🟡' : ring === 'low' ? '🔴' : '⬜';
    }

    /* ── Posición del pódio ─────────────────────────────────── */
    function medalEmoji(i) {
        return i === 0 ? '🥇' : i === 1 ? '🥈' : '🥉';
    }

    /* ══════════════════════════════════════════════════════════
     * CARGA PRINCIPAL
     * ══════════════════════════════════════════════════════════ */
    async function loadGrupal() {
        const grid          = document.getElementById('grpCardsGrid');
        const globalSection = document.getElementById('grpGlobalSection');
        const badge         = document.getElementById('grpSummaryBadge');
        const emptyEl       = document.getElementById('grpEmpty');

        if (!grid) return;

        // Estado de carga
        if (globalSection) globalSection.style.display = 'none';
        if (badge)         badge.style.display = 'none';
        emptyEl.innerHTML = '<div class="asist-spinner"></div>';
        emptyEl.style.display = 'flex';

        // Limpiar tarjetas previas (excepto el empty placeholder)
        grid.querySelectorAll('.grp-group-card').forEach(el => el.remove());

        try {
            const res = await fetch(API_GRUPAL, { headers: jsonHeaders() });
            if (!res.ok) throw new Error();
            const data = await res.json();

            if (!data.groups || data.groups.length === 0) {
                emptyEl.innerHTML = `
                    <div style="font-size:2.5rem;margin-bottom:10px">👥</div>
                    <div style="font-size:.95rem;font-weight:700;color:var(--text-white)">Sin grupos aún</div>
                    <div style="font-size:.8rem;color:var(--text-muted);margin-top:6px;max-width:280px">
                        Creá grupos en el módulo de Grupos y asigná alumnos para ver el desempeño.
                    </div>`;
                emptyEl.style.display = 'flex';
                return;
            }

            emptyEl.style.display = 'none';

            // Badge
            const totalStudents = data.global_stats?.total_students ?? 0;
            const totalGroups   = data.groups.length;
            if (badge) {
                badge.style.display = 'inline-flex';
                badge.textContent   = `👥 ${totalGroups} grupo${totalGroups !== 1 ? 's' : ''} · ${totalStudents} alumno${totalStudents !== 1 ? 's' : ''}`;
            }

            // Renderizar sección global + pódio
            renderGlobalSection(data);

            // Renderizar tarjetas por grupo
            data.groups.forEach(group => {
                const card = buildGroupCard(group, data.global_stats);
                grid.insertBefore(card, emptyEl);
            });

        } catch (err) {
            emptyEl.innerHTML = `
                <div style="font-size:2rem;margin-bottom:8px">⚠️</div>
                <div style="color:var(--red);font-weight:600">Error al cargar datos grupales</div>`;
            emptyEl.style.display = 'flex';
            if (typeof window.showToast === 'function') window.showToast('Error al cargar desempeño grupal', 'error');
        }
    }

    /* ══════════════════════════════════════════════════════════
     * SECCIÓN GLOBAL: ESTADÍSTICAS + PÓDIO + ALERTAS
     * ══════════════════════════════════════════════════════════ */
    function renderGlobalSection(data) {
        const section = document.getElementById('grpGlobalSection');
        if (!section) return;

        const gs = data.global_stats;

        // ── Barra de estadísticas globales ──────────────────
        const globalBar = document.getElementById('grpGlobalBar');
        if (globalBar && gs) {
            globalBar.innerHTML = `
                <div class="grp-global-stat">
                    <div class="grp-global-stat-icon blue">📅</div>
                    <div class="grp-global-stat-body">
                        <div class="grp-global-stat-label">Media de asistencia global</div>
                        <div class="grp-global-stat-value">${pctText(gs.avg_attendance_pct)}</div>
                    </div>
                    <div class="grp-global-bar-track">
                        <div class="grp-global-bar-fill blue" style="width:${gs.avg_attendance_pct ?? 0}%"></div>
                    </div>
                </div>
                <div class="grp-global-stat">
                    <div class="grp-global-stat-icon green">⚡</div>
                    <div class="grp-global-stat-body">
                        <div class="grp-global-stat-label">Media de contribución global</div>
                        <div class="grp-global-stat-value">${gs.avg_activity ?? 0} acciones</div>
                    </div>
                    <div class="grp-global-bar-track">
                        <div class="grp-global-bar-fill green" style="width:${actBar(gs.avg_activity ?? 0)}%"></div>
                    </div>
                </div>
                <div class="grp-global-stat">
                    <div class="grp-global-stat-icon purple">👤</div>
                    <div class="grp-global-stat-body">
                        <div class="grp-global-stat-label">Total de alumnos únicos</div>
                        <div class="grp-global-stat-value">${gs.total_students} alumno${gs.total_students !== 1 ? 's' : ''}</div>
                    </div>
                    <div class="grp-global-bar-track" style="background:transparent"></div>
                </div>`;
        }

        // ── Pódio ──────────────────────────────────────────
        const podium = data.podium;
        if (podium) {
            renderPodiumList('podiumAtt', podium.top_attendance, 'attendance_pct', v => pctText(v));
            renderPodiumList('podiumAct', podium.top_activity,   'activity_total', v => `${v} acciones`);
        }

        // ── Alertas ─────────────────────────────────────────
        const alertsSection = document.getElementById('grpAlertsSection');
        const alertsList    = document.getElementById('grpAlertsList');

        if (data.alerts && data.alerts.length > 0 && alertsSection && alertsList) {
            alertsSection.style.display = 'block';
            alertsList.innerHTML = data.alerts.map(m => {
                const reasons = (m.alert_reasons || []).map(r => {
                    if (r === 'asistencia') return `<span class="grp-alert-reason att">📅 Asistencia baja</span>`;
                    if (r === 'actividad')  return `<span class="grp-alert-reason act">⚡ Sin contribuciones</span>`;
                    return `<span class="grp-alert-reason">${r}</span>`;
                }).join('');
                const groups = (m.groups || []).map(g => `<span class="perf-card-group-chip">📁 ${g}</span>`).join('');
                const initials = getInitials(m.name);
                return `
                <div class="grp-alert-item">
                    <div class="grp-alert-avatar">${initials}</div>
                    <div class="grp-alert-info">
                        <div class="grp-alert-name">${m.name}</div>
                        <div class="grp-alert-groups">${groups || '<span style="color:var(--text-muted);font-size:.72rem">Sin grupo</span>'}</div>
                    </div>
                    <div class="grp-alert-reasons">${reasons}</div>
                    <div class="grp-alert-metrics">
                        <div class="grp-alert-metric">
                            <span class="grp-alert-metric-icon">📅</span>
                            <span class="grp-alert-metric-val ${attColor(m.attendance_pct)}">${pctText(m.attendance_pct)}</span>
                        </div>
                        <div class="grp-alert-metric">
                            <span class="grp-alert-metric-icon">⚡</span>
                            <span class="grp-alert-metric-val">${m.activity_total ?? 0}</span>
                        </div>
                    </div>
                </div>`;
            }).join('');
        } else if (alertsSection) {
            alertsSection.style.display = 'none';
        }

        section.style.display = 'block';
    }

    function renderPodiumList(containerId, members, valueKey, formatFn) {
        const el = document.getElementById(containerId);
        if (!el) return;
        if (!members || members.length === 0) {
            el.innerHTML = `<div style="text-align:center;padding:20px;color:var(--text-muted);font-size:.82rem">Sin datos suficientes</div>`;
            return;
        }
        el.innerHTML = members.map((m, i) => {
            const initials = getInitials(m.name);
            const value    = m[valueKey];
            const groups   = (m.groups || []).join(', ');
            const ring     = valueKey === 'attendance_pct' ? attRing(value) : 'none';
            return `
            <div class="grp-podium-item rank-${i + 1}">
                <div class="grp-podium-medal">${medalEmoji(i)}</div>
                <div class="grp-podium-avatar ${ring}">${initials}</div>
                <div class="grp-podium-info">
                    <div class="grp-podium-name">${m.name}</div>
                    <div class="grp-podium-group">${groups || 'Sin grupo'}</div>
                </div>
                <div class="grp-podium-value">${formatFn(value)}</div>
            </div>`;
        }).join('');
    }

    /* ══════════════════════════════════════════════════════════
     * TARJETA POR GRUPO
     * ══════════════════════════════════════════════════════════ */
    function buildGroupCard(group, globalStats) {
        const card = document.createElement('div');
        card.className = 'grp-group-card';

        const avgAtt = group.avg_attendance_pct;
        const avgAct = group.avg_activity;
        const accent = attColor(avgAtt);
        const ring   = attRing(avgAtt);

        // ── Header de la tarjeta ──────────────────────────────
        const headerHtml = `
        <div class="grp-card-accent ${accent}"></div>
        <div class="grp-card-header">
            <div class="grp-card-icon">👥</div>
            <div class="grp-card-title-block">
                <div class="grp-card-name">${group.name}</div>
                <div class="grp-card-sub">${group.member_count} miembro${group.member_count !== 1 ? 's' : ''}</div>
            </div>
            <div class="grp-card-avg-block">
                <div class="perf-score-ring ${ring}" title="Asistencia promedio grupal">${ringEmoji(ring)}</div>
            </div>
        </div>`;

        // ── Promedios grupales (barras) ───────────────────────
        const avgHtml = `
        <div class="grp-card-averages">
            <div class="grp-avg-row">
                <div class="grp-avg-label">
                    <span class="grp-avg-icon blue">📅</span>
                    <span>Asistencia grupal promedio</span>
                    <span class="grp-avg-value">${pctText(avgAtt)}</span>
                </div>
                <div class="perf-metric-bar-track">
                    <div class="perf-metric-bar-fill blue" style="width:${avgAtt ?? 0}%"></div>
                </div>
            </div>
            <div class="grp-avg-row">
                <div class="grp-avg-label">
                    <span class="grp-avg-icon green">⚡</span>
                    <span>Contribución grupal promedio</span>
                    <span class="grp-avg-value">${avgAct ?? 0} acc.</span>
                </div>
                <div class="perf-metric-bar-track">
                    <div class="perf-metric-bar-fill green" style="width:${actBar(avgAct ?? 0)}%"></div>
                </div>
            </div>
        </div>`;

        // ── Lista de miembros ─────────────────────────────────
        const membersHtml = buildMembersTable(group.members, globalStats);

        // ── Toggle expand/collapse ────────────────────────────
        const toggleId = `grp-toggle-${group.id}`;
        const bodyId   = `grp-body-${group.id}`;

        card.innerHTML = `
            ${headerHtml}
            ${avgHtml}
            <button class="grp-card-toggle" id="${toggleId}" aria-expanded="false" aria-controls="${bodyId}">
                <span class="grp-toggle-label">Ver miembros</span>
                <svg class="grp-toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div class="grp-card-body" id="${bodyId}" style="display:none">
                ${membersHtml}
            </div>`;

        // Bind toggle
        card.querySelector(`#${toggleId}`).addEventListener('click', function () {
            const body = document.getElementById(bodyId);
            const open = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !open);
            this.querySelector('.grp-toggle-label').textContent = open ? 'Ver miembros' : 'Ocultar miembros';
            this.querySelector('.grp-toggle-arrow').style.transform = open ? '' : 'rotate(180deg)';
            body.style.display = open ? 'none' : 'block';
        });

        return card;
    }

    /* ── Tabla de miembros ─────────────────────────────────── */
    function buildMembersTable(members, globalStats) {
        if (!members || members.length === 0) {
            return `<div class="grp-members-empty">Sin alumnos en este grupo</div>`;
        }

        const globalAvgAtt = globalStats?.avg_attendance_pct ?? null;
        const globalAvgAct = globalStats?.avg_activity ?? null;

        const rows = members.map(m => {
            const initials  = getInitials(m.name);
            const ring      = attRing(m.attendance_pct);
            const color     = attColor(m.attendance_pct);

            const attBelowGlobal = globalAvgAtt !== null && m.attendance_pct !== null && m.attendance_pct < globalAvgAtt;
            const actBelowGlobal = globalAvgAct !== null && m.activity_total < globalAvgAct;
            const alertClass     = (attBelowGlobal || actBelowGlobal) ? 'is-alert' : '';

            // Desglose de actividad por tipo
            const byType = m.activity_by_type || {};
            const keys = [
                { key: 'sale', icon: '💳', label: 'Ventas' },
                { key: 'sale_collect', icon: '💰', label: 'Cobros' },
                { key: 'sale_return', icon: '↩️', label: 'Devoluciones' },
                { key: 'order_delivery', icon: '🚚', label: 'Entregas' },
                { key: 'insert', icon: '➕', label: 'Nuevos' },
                { key: 'update', icon: '✏️', label: 'Ediciones' },
                { key: 'delete', icon: '🗑️', label: 'Bajas' },
                { key: 'confirm', icon: '✅', label: 'Aceptados' },
                { key: 'reject', icon: '❌', label: 'Rechazados' },
                { key: 'reactivate', icon: '🔄', label: 'Reactivados' },
                { key: 'login', icon: '🔐', label: 'Sesión' }
            ];

            const typeBreakdown = keys
                .map(item => {
                    const count = (byType[item.key] || 0) + (byType[item.key.toUpperCase()] || 0);
                    if (count > 0) {
                        return `<span class="grp-member-act-chip" title="${item.label}">${item.icon} ${count}</span>`;
                    }
                    return null;
                })
                .filter(Boolean)
                .join('');

            // Si el alumno colaboró fuera de su turno (total > turno)
            let helpBadge = '';
            if (m.activity_total > m.group_activity_total) {
                const diff = m.activity_total - m.group_activity_total;
                helpBadge = `<span class="grp-member-act-chip help-chip" style="background:rgba(147,51,234,0.15);color:#d8b4fe;border:1px solid rgba(168,85,247,0.3)" title="Ayudó como colaborador en otros turnos (+${diff} acciones)">🤝 +${diff}</span>`;
            }

            const finalBreakdown = (helpBadge ? helpBadge : '') + (typeBreakdown || `<span style="color:var(--text-muted);font-size:.7rem">Sin registros</span>`);

            return `
            <div class="grp-member-row ${alertClass}">
                <div class="grp-member-left">
                    <div class="grp-member-avatar">${initials}</div>
                    <div class="grp-member-info">
                        <div class="grp-member-name">${m.name}</div>
                        <div class="grp-member-user">@${m.username ?? '—'}</div>
                    </div>
                </div>
                <div class="grp-member-metrics">
                    <div class="grp-member-metric">
                        <div class="grp-member-metric-label">Asistencia</div>
                        <div class="grp-member-metric-bar">
                            <div class="perf-metric-bar-track" style="height:5px">
                                <div class="perf-metric-bar-fill blue" style="width:${m.attendance_pct ?? 0}%"></div>
                            </div>
                        </div>
                        <div class="grp-member-metric-val ${color}">${pctText(m.attendance_pct)}</div>
                    </div>
                    <div class="grp-member-metric">
                        <div class="grp-member-metric-label">Contribución: <strong style="color:var(--text-white)">${m.group_activity_total}</strong> <span style="color:var(--text-muted);font-size:.7rem">(Total: ${m.activity_total})</span></div>
                        <div class="grp-member-act-chips">${finalBreakdown}</div>
                    </div>
                </div>
                <div class="grp-member-ring-wrap">
                    <div class="perf-score-ring ${ring}" title="${pctText(m.attendance_pct)} de asistencia" style="width:36px;height:36px;font-size:.9rem">
                        ${ringEmoji(ring)}
                    </div>
                    ${attBelowGlobal || actBelowGlobal ? '<div class="grp-member-alert-dot" title="Por debajo de la media global"></div>' : ''}
                </div>
            </div>`;
        }).join('');

        return `
        <div class="grp-members-header">
            <span>Alumno</span><span>Asistencia · Contribución</span>
        </div>
        <div class="grp-members-list">${rows}</div>`;
    }

    /* ══════════════════════════════════════════════════════════
     * INIT
     * ══════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => {
        // Cargar al activar el módulo desde el sidebar
        document.querySelectorAll('.nav-item[data-module="desempeno-grupal"]').forEach(btn => {
            btn.addEventListener('click', () => loadGrupal());
        });

        // Si el módulo ya está activo al cargar (restaurado desde localStorage)
        const section = document.querySelector('[data-module-content="desempeno-grupal"]');
        if (section && section.classList.contains('active')) {
            loadGrupal();
        } else {
            // Esperar a que el módulo se active por primera vez (observador de clases)
            const observer = new MutationObserver(() => {
                if (section && section.classList.contains('active')) {
                    observer.disconnect();
                    loadGrupal();
                }
            });
            if (section) observer.observe(section, { attributes: true, attributeFilter: ['class'] });
        }
    });

    // Exponer para llamada externa si se necesita
    window.loadGrupal = loadGrupal;

})();
