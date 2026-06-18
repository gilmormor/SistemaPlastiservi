/* ============================================================
   admin/menu-rol/index.js
   Árbol accordion de menús con checkboxes independientes por rol.
   ============================================================ */

var rolesSeleccionados = []; // [{id, nombre}, ...]
var tokenCsrf = '';

$(document).ready(function () {
    tokenCsrf = $('input[name=_token]').val();

    $('#rol_id').selectpicker('refresh');

    $('#btnconsultar').on('click', function () {
        consultarMenuRol();
    });

    $('#btn-expandir-todo').on('click', function () {
        $('.menu-children').show();
        $('.toggle-icono').removeClass('fa-chevron-right').addClass('fa-chevron-down');
    });

    $('#btn-colapsar-todo').on('click', function () {
        $('.menu-children').hide();
        $('.toggle-icono').removeClass('fa-chevron-down').addClass('fa-chevron-right');
    });
});

/* ----------------------------------------------------------
   1. CONSULTA
   ---------------------------------------------------------- */
function consultarMenuRol() {
    var rolIds = $('#rol_id').val();
    if (!rolIds || rolIds.length === 0) {
        Biblioteca.notificaciones('Seleccione al menos un rol.', 'Plastiservi', 'warning');
        return;
    }

    var $btn = $('#btnconsultar');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Cargando...');

    $.ajax({
        url: '/admin/menu-rol/datos',
        type: 'GET',
        data: { rol_id: rolIds, _token: tokenCsrf },
        success: function (resp) {
            rolesSeleccionados = resp.roles;
            renderStickyBar(resp.roles);
            $('#arbol-menu').html(buildTreeHtml(resp.menu, 0));
            $('#contenedor-sticky').show();
            $('#contenedor-arbol').show();
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Consultar');
        },
        error: function () {
            Biblioteca.notificaciones('Error al cargar los datos.', 'Plastiservi', 'danger');
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Consultar');
        }
    });
}

/* ----------------------------------------------------------
   2. BARRA STICKY
   ---------------------------------------------------------- */
function renderStickyBar(roles) {
    var html = '';
    $.each(roles, function (i, rol) {
        html += '<span class="label label-info">' + escapeHtml(rol.nombre) + '</span>';
    });
    $('#roles-badges').html(html);
}

/* ----------------------------------------------------------
   3. ÁRBOL
   ---------------------------------------------------------- */
function buildTreeHtml(menus, nivel) {
    var html = '';
    $.each(menus, function (i, menu) {
        var tieneHijos  = menu.submenu && menu.submenu.length > 0;
        var paddingLeft = 15 + (nivel * 28);
        var bgClass     = 'bg-nivel-' + Math.min(nivel, 3);

        html += '<div class="menu-item-wrapper" data-id="' + menu.id + '">';
        html += '<div class="menu-fila ' + bgClass + '" style="padding-left:' + paddingLeft + 'px;">';

        if (tieneHijos) {
            html += '<span class="toggle-menu-btn" data-target="children-' + menu.id + '">'
                  + '<i class="fa fa-chevron-right toggle-icono" id="icon-' + menu.id + '"></i>'
                  + '</span>';
        } else {
            html += '<span class="toggle-placeholder"></span>';
        }

        html += '<span class="menu-nombre-wrap">'
              + '<i class="fa ' + escapeHtml(menu.icono || 'fa-circle-o') + '"></i>'
              + ' <span>' + escapeHtml(menu.nombre) + '</span>'
              + '</span>';

        html += '<span class="checks-rol-container">';
        $.each(rolesSeleccionados, function (j, rol) {
            var checked = menu.roles[rol.id] ? 'checked' : '';
            var inputId = 'chk-' + menu.id + '-' + rol.id;
            html += '<label class="check-rol-label" for="' + inputId + '">'
                  + '<input type="checkbox"'
                  + ' id="' + inputId + '"'
                  + ' class="menu_rol_check"'
                  + ' data-menuid="' + menu.id + '"'
                  + ' data-rolid="' + rol.id + '"'
                  + ' ' + checked + '>'
                  + '<span class="check-rol-nombre">' + escapeHtml(rol.nombre) + '</span>'
                  + '</label>';
        });
        html += '</span>';

        html += '</div>';

        if (tieneHijos) {
            html += '<div class="menu-children" id="children-' + menu.id + '" style="display:none;">';
            html += buildTreeHtml(menu.submenu, nivel + 1);
            html += '</div>';
        }

        html += '</div>';
    });
    return html;
}

/* ----------------------------------------------------------
   4. ACORDEÓN
   ---------------------------------------------------------- */
$(document).on('click', '.toggle-menu-btn', function () {
    var target = $(this).data('target');
    var $icono = $(this).find('.toggle-icono');
    $('#' + target).slideToggle(150, function () {
        if ($('#' + target).is(':visible')) {
            $icono.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        } else {
            $icono.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        }
    });
});

/* ----------------------------------------------------------
   5. CHECKBOX: guarda solo ese ítem, sin propagar ni tri-state
   ---------------------------------------------------------- */
$(document).on('change', '.menu_rol_check', function () {
    var menuId = parseInt($(this).data('menuid'));
    var rolId  = parseInt($(this).data('rolid'));
    var estado = this.checked ? 1 : 0;

    $.ajax({
        url: '/admin/menu-rol',
        type: 'POST',
        data: { menu_id: menuId, rol_id: rolId, estado: estado, _token: tokenCsrf },
        success: function (resp) {
            Biblioteca.notificaciones(resp.respuesta, 'Plastiservi', estado == 1 ? 'success' : 'warning');
        }
    });
});

/* ----------------------------------------------------------
   Utilidad: escapar HTML
   ---------------------------------------------------------- */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
