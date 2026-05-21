/**
 * ============================================================================
 * KIOSKO-UNER | MANEJADOR UNIVERSAL DE MODALES (MODAL MANAGER)
 * ============================================================================
 * Este script proporciona una API global y robusta para la apertura y clausura
 * de modales en toda la plataforma (Alumno, Profesor, Superadmin).
 *
 * Resuelve problemas comunes de especificidad de CSS mediante la manipulación
 * prioritaria de estilos inline y utiliza delegación de eventos para el cierre
 * automático (clicks en el fondo, botones de cancelación y la tecla Escape).
 *
 * ----------------------------------------------------------------------------
 * 📖 GUÍA DE USO PARA NUEVAS IMPLEMENTACIONES:
 * ----------------------------------------------------------------------------
 *
 * 1. ESTRUCTURA HTML ESTÁNDAR RECOMENDADA:
 *    <!-- El contenedor principal (Overlay) actúa como fondo translúcido -->
 *    <div class="modal-overlay" id="miNuevoModal">
 *        <div class="modal">
 *            <!-- Cabecera del modal -->
 *            <div class="modal-header">
 *                <h3 class="modal-title">Título del Modal</h3>
 *                <button type="button" class="modal-close">✕</button>
 *            </div>
 *            <!-- Cuerpo del modal (Formularios, Tablas, etc.) -->
 *            <form class="modal-body" id="miNuevoForm">
 *                <div class="form-group">
 *                    <label class="form-label">Nombre</label>
 *                    <input class="form-input" name="nombre" required>
 *                </div>
 *                <!-- Pie del modal (Acciones) -->
 *                <div class="modal-footer">
 *                    <button type="button" class="btn-cancel">Cancelar</button>
 *                    <button type="submit" class="btn-submit">Guardar</button>
 *                </div>
 *            </form>
 *        </div>
 *    </div>
 *
 * 2. LLAMADO DESDE JAVASCRIPT:
 *    - Para abrir el modal:
 *      window.openModal('miNuevoModal');
 *
 *    - Para cerrar el modal de manera manual:
 *      window.closeModal('miNuevoModal');
 *
 * 3. COMPORTAMIENTO AUTOMÁTICO INTEGRADO:
 *    - Clic fuera del contenido (en el fondo oscuro) cerrará el modal automáticamente.
 *    - Clic en cualquier botón con la clase ".modal-close", ".btn-cancel" o el atributo "data-modal-close" lo cerrará automáticamente.
 *    - Presionar la tecla "Escape" (ESC) cerrará el modal que esté visible.
 * ============================================================================
 */

(function () {
    'use strict';

    /**
     * Abre un modal por ID o por paso directo de su elemento del DOM.
     * @param {string|HTMLElement} idOrElement - El ID del modal o el elemento del DOM.
     */
    window.openModal = function (idOrElement) {
        const modal = typeof idOrElement === 'string' ? document.getElementById(idOrElement) : idOrElement;
        if (!modal) {
            console.warn(`[ModalManager] No se encontró el modal solicitado:`, idOrElement);
            return;
        }

        // 1. Eliminar cualquier propiedad 'display' inline previa que pudiera interferir (por ejemplo: style="display: none !important;")
        modal.style.removeProperty('display');

        // 2. Forzar display flex con prioridad !important para contrarrestar colisiones de CSS global o local
        modal.style.setProperty('display', 'flex', 'important');

        // 3. Añadir clases de estado activo para asegurar compatibilidad con hojas de estilo existentes
        modal.classList.add('visible', 'open');

        // 4. Prevenir scroll en el body si se desea (opcional, mejora UX)
        // document.body.style.overflow = 'hidden';
    };

    /**
     * Cierra un modal por ID o por paso directo de su elemento del DOM.
     * @param {string|HTMLElement} idOrElement - El ID del modal o el elemento del DOM.
     */
    window.closeModal = function (idOrElement) {
        const modal = typeof idOrElement === 'string' ? document.getElementById(idOrElement) : idOrElement;
        if (!modal) return;

        // 1. Forzar display none con prioridad !important para asegurar el ocultamiento absoluto
        modal.style.setProperty('display', 'none', 'important');

        // 2. Remover clases de estado activo
        modal.classList.remove('visible', 'open');

        // 3. Restaurar scroll en el body si corresponde
        // Comprobamos si queda algún otro modal abierto antes de habilitar el scroll
        const anyOpen = document.querySelector('.modal-overlay.visible, .modal-overlay.open, .pmodal-overlay.open');
        if (!anyOpen) {
            document.body.style.overflow = '';
        }
    };

    /**
     * ── DELEGACIÓN DE EVENTOS GLOBAL (CLICS) ────────────────────────────────
     * Se escuchan los clics a nivel de documento. Esto garantiza que funcione
     * incluso con modales cargados dinámicamente o inyectados por AJAX/MutationObservers.
     */
    document.addEventListener('click', function (event) {
        const target = event.target;

        // 1. Caso A: Clic en el fondo oscuro del modal (Overlay)
        // Soporta la clase estándar ".modal-overlay" y la variante ".pmodal-overlay" de productos
        if (target.classList.contains('modal-overlay') || target.classList.contains('pmodal-overlay')) {
            window.closeModal(target);
            return;
        }

        // 2. Caso B: Clic en botones de cerrar, cancelar o con atributo de cierre
        const closeButton = target.closest('.modal-close, .pmodal-close, .btn-cancel, [data-modal-close]');
        if (closeButton) {
            // Encontrar el contenedor overlay ancestro más cercano
            const modal = closeButton.closest('.modal-overlay, .pmodal-overlay');
            if (modal) {
                window.closeModal(modal);
            }
        }
    });

    /**
     * ── CAPTURA DE TECLADO GLOBAL (TECLA ESCAPE) ───────────────────────────
     * Permite una experiencia de usuario natural cerrando el modal activo al presionar ESC.
     */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' || event.keyCode === 27) {
            // Buscar todos los modales que estén actualmente en estado visible u abierto
            // Ordenados de menor a mayor jerarquía en el DOM (el último visible será el de arriba)
            const openModals = Array.from(
                document.querySelectorAll('.modal-overlay.visible, .modal-overlay.open, .pmodal-overlay.open')
            ).filter(modal => {
                // Doble chequeo: verificar que realmente se muestre en pantalla
                return modal.style.display !== 'none' && getComputedStyle(modal).display !== 'none';
            });

            if (openModals.length > 0) {
                // Cerrar el último modal abierto (el de más arriba)
                const topmostModal = openModals[openModals.length - 1];
                window.closeModal(topmostModal);
                event.preventDefault(); // Evitar comportamientos por defecto del navegador
            }
        }
    });

    console.log('[ModalManager] Inicializado correctamente y cargado de forma universal.');
})();
