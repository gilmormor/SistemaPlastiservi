<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'cliente/eliminarClienteDirec/*', //Pasa por alto token de eliminar direccion a clientes
        'cotizacion/eliminarCotizacionDetalle/*', //Pasa por alto token de eliminar detalle Cotizacion
        'noconformidad/*', //Pasa por alto token de eliminar detalle No Conformidad
        'eliminarfotonc/*', //Pasa por alto token de eliminar detalle No Conformidad
        'jefaturaAreaSuc/asignarjefe/*',
    ];
}
