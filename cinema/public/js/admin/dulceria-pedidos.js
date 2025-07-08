/**
 * Gestión de Pedidos de Dulcería - Admin Dashboard
 * Archivo: public/js/admin/dulceria-pedidos.js
 * 
 * Este archivo maneja toda la funcionalidad JavaScript para la gestión
 * de pedidos en el panel de administración de dulcería
 */

$(document).ready(function() {
    'use strict';
    
    // Variables globales
    let autoRefresh = false;
    let refreshInterval = null;
    let pendingStateChange = null;
    const REFRESH_INTERVAL = 30000; // 30 segundos

    // Inicializar cuando el DOM esté listo
    initializePage();

    /**
     * Inicializar la página
     */
    function initializePage() {
        console.log('Inicializando gestión de pedidos...');
        
        // Configurar eventos
        initializeEventHandlers();
        
        // Actualizar tiempo inicial
        updateLastRefreshTime();
        
        // Verificar autenticación CSRF
        setupCSRF();
        
        // Inicializar indicadores de tiempo
        updateTimeIndicators();
        
        console.log('Gestión de pedidos inicializada correctamente');
    }

    /**
     * Configurar token CSRF para peticiones AJAX
     */
    function setupCSRF() {
        const token = $('meta[name="csrf-token"]').attr('content');
        if (token) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
        } else {
            console.warn('Token CSRF no encontrado');
        }
    }

    /**
     * Configurar todos los event handlers
     */
    function initializeEventHandlers() {
        // Limpiar eventos anteriores para evitar duplicados
        $(document).off('.dulceria-pedidos');
        
        // Auto-refresh toggle
        $('#auto-refresh-toggle').off('click.dulceria-pedidos').on('click.dulceria-pedidos', toggleAutoRefresh);
        
        // Filtros rápidos
        $('.filtro-rapido').off('click.dulceria-pedidos').on('click.dulceria-pedidos', function() {
            const estado = $(this).data('estado');
            if (estado) {
                $('select[name="estado"]').val(estado);
                $('#filtros-form').submit();
            }
        });

        // Cambio de estado via select
        $('.estado-select').off('change.dulceria-pedidos').on('change.dulceria-pedidos', handleEstadoChange);
        
        // Cambio rápido de estado
        $('.cambio-rapido').off('click.dulceria-pedidos').on('click.dulceria-pedidos', handleCambioRapido);
        
        // Confirmación de cambio
        $('#confirmar-cambio-estado').off('click.dulceria-pedidos').on('click.dulceria-pedidos', confirmarCambioEstado);
        
        // Búsqueda en tiempo real con debounce
        $('input[name="buscar"]').off('input.dulceria-pedidos').on('input.dulceria-pedidos', 
            debounce(function() {
                $('#filtros-form').submit();
            }, 500)
        );

        // Cerrar modal con ESC
        $(document).off('keydown.dulceria-pedidos').on('keydown.dulceria-pedidos', function(e) {
            if (e.keyCode === 27) { // ESC key
                $('.modal').modal('hide');
            }
        });

        // Prevenir envío accidental del formulario
        $('#filtros-form').off('submit.dulceria-pedidos').on('submit.dulceria-pedidos', function(e) {
            // Permitir envío normal pero mostrar loading
            showLoadingState();
        });
    }

    /**
     * Auto-refresh functionality
     */
    function toggleAutoRefresh() {
        autoRefresh = !autoRefresh;
        const btn = $('#auto-refresh-toggle');
        
        if (autoRefresh) {
            btn.removeClass('btn-outline-success').addClass('btn-success')
               .html('<i class="fas fa-pause me-1"></i>Pausar Auto-refresh');
            startAutoRefresh();
            showToast('Auto-actualización activada cada 30 segundos', 'success');
        } else {
            btn.removeClass('btn-success').addClass('btn-outline-success')
               .html('<i class="fas fa-play me-1"></i>Auto-actualizar');
            stopAutoRefresh();
            showToast('Auto-actualización pausada', 'info');
        }
    }

    function startAutoRefresh() {
        // Limpiar intervalo anterior si existe
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        
        refreshInterval = setInterval(function() {
            // Solo actualizar si no hay modales abiertos
            if (!$('.modal').hasClass('show')) {
                refreshData();
            }
        }, REFRESH_INTERVAL);
        
        console.log('Auto-refresh iniciado');
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
        console.log('Auto-refresh detenido');
    }

    function refreshData() {
        console.log('Actualizando datos...');
        
        $.get(window.location.href)
        .done(function(data) {
            try {
                // Actualizar solo el contenido de la tabla
                const newTableBody = $(data).find('tbody').html();
                const newStats = $(data).find('.row .card').html();
                
                if (newTableBody) {
                    $('tbody').html(newTableBody);
                    
                    // Re-inicializar eventos en el nuevo contenido
                    initializeEventHandlers();
                    
                    // Actualizar tiempo
                    updateLastRefreshTime();
                    
                    // Verificar nuevos pedidos
                    checkForNewOrders();
                    
                    // Actualizar indicadores de tiempo
                    updateTimeIndicators();
                    
                    console.log('Datos actualizados correctamente');
                }
            } catch (error) {
                console.error('Error al procesar datos actualizados:', error);
                showToast('Error al procesar datos actualizados', 'error');
            }
        })
        .fail(function(xhr) {
            console.error('Error al actualizar datos:', xhr);
            showToast('Error al actualizar datos', 'error');
        });
    }

    function updateLastRefreshTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-PE', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        $('#ultima-actualizacion').text(timeString);
    }

    function checkForNewOrders() {
        try {
            // Contar pedidos confirmados actuales
            const currentOrders = $('.estado-select[data-estado-actual="confirmado"]').length;
            const lastOrderCount = parseInt(localStorage.getItem('lastOrderCount') || '0');
            
            if (currentOrders > lastOrderCount) {
                const newOrdersCount = currentOrders - lastOrderCount;
                showToast(`${newOrdersCount} nuevo(s) pedido(s) confirmado(s)`, 'info');
                playNotificationSound();
            }
            
            localStorage.setItem('lastOrderCount', currentOrders.toString());
        } catch (error) {
            console.error('Error al verificar nuevos pedidos:', error);
        }
    }

    /**
     * Manejo de cambios de estado
     */
    function handleEstadoChange() {
        const pedidoId = $(this).data('pedido-id');
        const estadoActual = $(this).data('estado-actual');
        const estadoNuevo = $(this).val();
        
        console.log('Cambio de estado solicitado:', {
            pedidoId,
            estadoActual,
            estadoNuevo
        });
        
        if (estadoActual !== estadoNuevo) {
            prepareModal(pedidoId, estadoActual, estadoNuevo);
        }
    }

    function handleCambioRapido() {
        const pedidoId = $(this).data('pedido-id');
        const estadoNuevo = $(this).data('nuevo-estado');
        const estadoActual = $(this).closest('tr').find('.estado-select').data('estado-actual');
        
        console.log('Cambio rápido solicitado:', {
            pedidoId,
            estadoActual,
            estadoNuevo
        });
        
        prepareModal(pedidoId, estadoActual, estadoNuevo);
    }

    function prepareModal(pedidoId, estadoActual, estadoNuevo) {
        const row = $(`tr[data-pedido-id="${pedidoId}"]`);
        
        if (row.length === 0) {
            console.error('No se encontró la fila del pedido:', pedidoId);
            showToast('Error: No se encontró el pedido', 'error');
            return;
        }
        
        const codigoPedido = row.find('td:first strong').text().trim();
        const cliente = row.find('td:nth-child(2) strong').text().trim();

        // Llenar modal con información
        $('#modal-codigo-pedido').text(codigoPedido);
        $('#modal-cliente').text(cliente);
        $('#modal-estado-actual').text(getEstadoTexto(estadoActual));
        $('#modal-estado-nuevo').text(getEstadoTexto(estadoNuevo));

        // Guardar información del cambio pendiente
        pendingStateChange = {
            pedidoId: pedidoId,
            estadoNuevo: estadoNuevo,
            estadoActual: estadoActual
        };

        // Mostrar modal
        $('#confirmarCambioModal').modal('show');
    }

    function confirmarCambioEstado() {
        if (!pendingStateChange) {
            console.error('No hay cambio de estado pendiente');
            return;
        }

        const { pedidoId, estadoNuevo, estadoActual } = pendingStateChange;
        
        console.log('Confirmando cambio de estado:', pendingStateChange);
        
        // Mostrar loading en el botón
        const btn = $('#confirmar-cambio-estado');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...').prop('disabled', true);

        // Enviar cambio via AJAX
        $.ajax({
            url: `/admin/dulceria-pedidos/${pedidoId}/estado`,
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                estado: estadoNuevo
            }),
            success: function(response) {
                console.log('Estado cambiado exitosamente:', response);
                
                // Cerrar modal
                $('#confirmarCambioModal').modal('hide');
                
                // Mostrar mensaje de éxito
                const message = response.message || `Estado cambiado de '${estadoActual}' a '${estadoNuevo}'`;
                showToast(message, 'success');
                
                // Actualizar la fila en la tabla
                updateRowState(pedidoId, estadoNuevo);
                
                // Actualizar estadísticas si es necesario
                if (autoRefresh) {
                    setTimeout(refreshData, 1000);
                }
            },
            error: function(xhr) {
                console.error('Error al cambiar estado:', xhr);
                
                let message = 'Error al cambiar el estado del pedido';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    message = 'Pedido no encontrado';
                } else if (xhr.status === 403) {
                    message = 'No tienes permisos para realizar esta acción';
                } else if (xhr.status === 422) {
                    message = 'Estado no válido';
                }
                
                showToast(message, 'error');
                
                // Revertir el select al estado anterior
                const row = $(`tr[data-pedido-id="${pedidoId}"]`);
                row.find('.estado-select').val(estadoActual);
            },
            complete: function() {
                // Restaurar botón
                btn.html(originalText).prop('disabled', false);
                pendingStateChange = null;
            }
        });
    }

    function updateRowState(pedidoId, nuevoEstado) {
        const row = $(`tr[data-pedido-id="${pedidoId}"]`);
        
        if (row.length === 0) {
            console.error('No se encontró la fila para actualizar:', pedidoId);
            return;
        }
        
        // Actualizar atributos de la fila
        row.attr('data-estado', nuevoEstado);
        
        // Actualizar el select
        const select = row.find('.estado-select');
        select.val(nuevoEstado).data('estado-actual', nuevoEstado);
        
        // Actualizar botones de acción rápida
        const actionsCell = row.find('td:last-child .d-flex');
        actionsCell.find('.cambio-rapido').remove();
        
        if (nuevoEstado === 'confirmado') {
            actionsCell.append(`
                <button class="btn btn-sm btn-success cambio-rapido ms-1" 
                        data-pedido-id="${pedidoId}" 
                        data-nuevo-estado="listo">
                    <i class="fas fa-bell me-1"></i>Listo
                </button>
            `);
        } else if (nuevoEstado === 'listo') {
            actionsCell.append(`
                <button class="btn btn-sm btn-info cambio-rapido ms-1" 
                        data-pedido-id="${pedidoId}" 
                        data-nuevo-estado="entregado">
                    <i class="fas fa-check me-1"></i>Entregar
                </button>
            `);
        }
        
        // Re-attach events para los nuevos botones
        actionsCell.find('.cambio-rapido').off('click.dulceria-pedidos').on('click.dulceria-pedidos', handleCambioRapido);
        
        // Animación visual
        row.addClass('estado-cambiado');
        setTimeout(() => row.removeClass('estado-cambiado'), 2000);
        
        console.log('Fila actualizada:', pedidoId, nuevoEstado);
    }

    /**
     * Funciones de utilidad
     */
    function getEstadoTexto(estado) {
        const estados = {
            'pendiente': 'Pendiente',
            'confirmado': 'Confirmado',
            'listo': 'Listo para recoger',
            'entregado': 'Entregado',
            'cancelado': 'Cancelado'
        };
        return estados[estado] || estado;
    }

    function getEstadoBadgeClass(estado) {
        const clases = {
            'pendiente': 'bg-secondary',
            'confirmado': 'bg-warning',
            'listo': 'bg-info',
            'entregado': 'bg-success',
            'cancelado': 'bg-danger'
        };
        return clases[estado] || 'bg-secondary';
    }

    function showToast(message, type = 'info') {
        // Verificar si ya existe un contenedor de toasts
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            $('body').append(toastContainer);
        }
        
        const toastId = 'toast-' + Date.now();
        const iconClass = {
            'success': 'fas fa-check-circle text-success',
            'error': 'fas fa-exclamation-circle text-danger',
            'warning': 'fas fa-exclamation-triangle text-warning',
            'info': 'fas fa-info-circle text-info'
        }[type] || 'fas fa-info-circle text-info';

        const toast = $(`
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="toast-header">
                    <i class="${iconClass} me-2"></i>
                    <strong class="me-auto">Gestión de Pedidos</strong>
                    <small class="text-muted">${new Date().toLocaleTimeString()}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);

        toastContainer.append(toast);
        
        // Mostrar toast usando Bootstrap
        const toastElement = new bootstrap.Toast(toast[0]);
        toastElement.show();
        
        // Auto-remove después de que se oculte
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
        
        console.log('Toast mostrado:', type, message);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function playNotificationSound() {
        try {
            // Audio simple para notificaciones
            if ('AudioContext' in window || 'webkitAudioContext' in window) {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                gainNode.gain.value = 0.1;
                
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.2);
                
                console.log('Sonido de notificación reproducido');
            }
        } catch (e) {
            console.log('Audio no disponible:', e.message);
        }
    }

    function showLoadingState() {
        const btn = $('#filtros-form button[type="submit"]');
        if (btn.length) {
            const originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Filtrando...').prop('disabled', true);
            
            // Restaurar después de un tiempo
            setTimeout(() => {
                btn.html(originalText).prop('disabled', false);
            }, 2000);
        }
    }

    /**
     * Indicadores visuales de tiempo
     */
    function updateTimeIndicators() {
        $('.tiempo-transcurrido').each(function() {
            const createdAt = $(this).data('created-at');
            if (createdAt) {
                try {
                    const createdTime = new Date(createdAt);
                    const now = new Date();
                    const diffMinutes = Math.floor((now - createdTime) / (1000 * 60));
                    
                    const row = $(this).closest('tr');
                    const estado = row.data('estado');
                    
                    // Cambiar color según el tiempo transcurrido y estado
                    row.removeClass('table-warning table-danger');
                    
                    if (estado === 'confirmado' && diffMinutes > 15) {
                        row.addClass('table-warning');
                    } else if (estado === 'listo' && diffMinutes > 30) {
                        row.addClass('table-danger');
                    }
                    
                    // Actualizar texto del tiempo
                    $(this).text(formatTimeElapsed(diffMinutes));
                } catch (error) {
                    console.error('Error al actualizar indicador de tiempo:', error);
                }
            }
        });
    }

    function formatTimeElapsed(minutes) {
        if (minutes < 60) {
            return `${minutes}m`;
        } else {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            return `${hours}h ${remainingMinutes}m`;
        }
    }

    // Actualizar indicadores cada minuto
    setInterval(updateTimeIndicators, 60000);

    /**
     * Funciones globales expuestas
     */
    window.dulceriaPedidos = {
        // Función para marcar todos como listos
        marcarTodosListos: function() {
            const pendientes = $('.estado-select').filter(function() {
                return $(this).val() === 'confirmado';
            });
            
            if (pendientes.length === 0) {
                showToast('No hay pedidos pendientes para marcar como listos', 'info');
                return;
            }
            
            if (confirm(`¿Estás seguro de marcar ${pendientes.length} pedidos como listos?`)) {
                let completed = 0;
                const total = pendientes.length;
                
                showToast(`Procesando ${total} pedidos...`, 'info');
                
                pendientes.each(function() {
                    const pedidoId = $(this).data('pedido-id');
                    
                    $.ajax({
                        url: `/admin/dulceria-pedidos/${pedidoId}/estado`,
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify({
                            estado: 'listo'
                        }),
                        success: function() {
                            completed++;
                            updateRowState(pedidoId, 'listo');
                            
                            if (completed === total) {
                                showToast(`${total} pedidos marcados como listos exitosamente`, 'success');
                                if (autoRefresh) {
                                    setTimeout(refreshData, 1000);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Error al procesar pedido:', pedidoId, xhr);
                            showToast(`Error al procesar pedido #${pedidoId}`, 'error');
                        }
                    });
                });
            }
        },

        // Función para actualización manual
        actualizarPedidos: function() {
            refreshData();
            showToast('Actualizando datos...', 'info');
        },

        // Función para exportar datos
        exportarPedidos: function() {
            const params = new URLSearchParams(window.location.search);
            params.append('export', 'excel');
            window.location.href = `/admin/dulceria-pedidos?${params.toString()}`;
        },

        // Función para imprimir
        imprimirReporte: function() {
            window.print();
        }
    };

    // Funciones globales para compatibilidad
    window.marcarTodosListos = window.dulceriaPedidos.marcarTodosListos;
    window.actualizarPedidos = window.dulceriaPedidos.actualizarPedidos;
    window.exportarPedidos = window.dulceriaPedidos.exportarPedidos;
    window.imprimirReporte = window.dulceriaPedidos.imprimirReporte;

    console.log('Sistema de gestión de pedidos cargado completamente');
});