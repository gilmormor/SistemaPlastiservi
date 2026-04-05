<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenteIaController extends Controller
{
    private $apiKey;
    private $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = env('ANTHROPIC_API_KEY');
    }

    public function index()
    {
        return view('asistenteia.index');
    }

    public function consultar(Request $request)
    {
        $pregunta  = $request->input('pregunta', '');
        $historialRaw = $request->input('historial', '[]');

        // El JS envía historial como JSON.stringify(), hay que decodificarlo
        if (is_string($historialRaw)) {
            $historial = json_decode($historialRaw, true) ?? [];
        } else {
            $historial = is_array($historialRaw) ? $historialRaw : [];
        }

        if (empty(trim($pregunta))) {
            return response()->json(['error' => 'La pregunta no puede estar vacía.'], 422);
        }

        $messages = [];
        foreach ($historial as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $pregunta];

        $claudeResponse = $this->callClaude($this->getSystemPrompt(), $messages);

        if (!$claudeResponse['success']) {
            return response()->json(['error' => $claudeResponse['error']], 500);
        }

        $responseData = $this->parseClaudeResponse($claudeResponse['content']);

        // Si no devolvió JSON válido, responder como texto libre
        if (!$responseData) {
            return response()->json([
                'tipo'     => 'ninguno',
                'respuesta' => $claudeResponse['content'],
                'datos'    => null,
            ]);
        }

        $datos    = null;
        $errorSql = null;

        if (!empty($responseData['sql'])) {
            $sqlResult = $this->ejecutarSQL($responseData['sql']);
            if ($sqlResult['success']) {
                $datos = $sqlResult['datos'];
            } else {
                $errorSql = $sqlResult['error'];
            }
        }

        return response()->json([
            'tipo'          => $responseData['tipo_grafico'] ?? 'tabla',
            'respuesta'     => $responseData['respuesta'] ?? '',
            'titulo_grafico' => $responseData['titulo_grafico'] ?? '',
            'eje_x'         => $responseData['eje_x'] ?? null,
            'eje_y'         => $responseData['eje_y'] ?? null,
            'datos'         => $datos,
            'error_sql'     => $errorSql,
            'assistant_msg' => $responseData['respuesta'] ?? $claudeResponse['content'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Llamada a la API de Claude
    // -------------------------------------------------------------------------
    private function callClaude(string $systemPrompt, array $messages): array
    {
        $payload = json_encode([
            'model'      => 'claude-sonnet-4-6',
            'max_tokens' => 2048,
            'system'     => $systemPrompt,
            'messages'   => $messages,
        ]);

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'error' => 'Error de conexión: ' . $curlError];
        }

        $decoded = json_decode($response, true);

        if ($httpCode !== 200) {
            $msg = $decoded['error']['message'] ?? ('HTTP ' . $httpCode);
            return ['success' => false, 'error' => 'Error API: ' . $msg];
        }

        return [
            'success' => true,
            'content' => $decoded['content'][0]['text'] ?? '',
        ];
    }

    // -------------------------------------------------------------------------
    // Extraer JSON de la respuesta de Claude
    // -------------------------------------------------------------------------
    private function parseClaudeResponse(string $content): ?array
    {
        // Bloque ```json ... ```
        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $content, $matches)) {
            $json = json_decode($matches[1], true);
            if (is_array($json)) {
                return $json;
            }
        }

        // Intento directo
        $json = json_decode($content, true);
        if (is_array($json)) {
            return $json;
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Ejecutar SQL de forma segura (solo SELECT)
    // -------------------------------------------------------------------------
    private function ejecutarSQL(string $sql): array
    {
        $sqlTrim = trim($sql);

        // Solo SELECT
        if (!preg_match('/^SELECT\s/i', $sqlTrim)) {
            return ['success' => false, 'error' => 'Solo se permiten consultas SELECT.'];
        }

        // Palabras clave peligrosas
        $forbidden = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE', 'REPLACE', 'EXEC', 'EXECUTE'];
        foreach ($forbidden as $kw) {
            if (preg_match('/\b' . $kw . '\b/i', $sqlTrim)) {
                return ['success' => false, 'error' => 'Consulta no permitida.'];
            }
        }

        try {
            $results = DB::select($sqlTrim);
            // Convertir a array asociativo plano
            $datos = array_map(function ($row) { return (array) $row; }, $results);
            return ['success' => true, 'datos' => $datos];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // -------------------------------------------------------------------------
    // System prompt con esquema de tablas
    // -------------------------------------------------------------------------
    private function getSystemPrompt(): string
    {
        $schema = $this->getSchema();

        return <<<PROMPT
Eres el asistente de análisis de datos de Plastiservi, empresa manufacturera de plásticos en Chile.
Tienes acceso a la base de datos del sistema ERP de la empresa.

ESQUEMA DE TABLAS:
$schema

INSTRUCCIONES:
- Analiza la pregunta del usuario y genera SQL SELECT para responderla.
- Responde SIEMPRE con un JSON válido (sin texto adicional fuera del JSON) con esta estructura exacta:
{
  "sql": "SELECT ...",
  "respuesta": "Descripción breve de lo que se está consultando",
  "tipo_grafico": "barra|linea|torta|tabla|numero|ninguno",
  "titulo_grafico": "Título descriptivo del resultado",
  "eje_x": "campo_para_eje_x_o_null",
  "eje_y": "campo_para_eje_y_o_null"
}

REGLAS SQL:
- SOLO consultas SELECT. Nunca INSERT, UPDATE, DELETE, DROP, ALTER, CREATE, TRUNCATE.
- Tablas con soft delete (filtrar con ISNULL(tabla.deleted_at)): notaventa, cotizacion, despachosol, despachoord, dte, dtedet, producto, cliente, vendedor.
- Para nombre de vendedor: JOIN persona ON vendedor.persona_id = persona.id → usar persona.nombre.
- Para nombre de cliente: usar cliente.razonsocial (o cliente.nombrefantasia si está disponible).
- DTE tipos: foliocontrol_id = 1 (factura), 2 (guía de despacho), 5 (nota de crédito), 6 (nota de débito).
- Montos: notaventa.total, cotizacion.total, dte.mnttotal.
- Fecha principal: columna fechahora en todas las tablas transaccionales.
- Limitar rankings a 10-20 registros con LIMIT.
- Usar alias claros en español: AS "Vendedor", AS "Total Ventas", AS "Cantidad".

TIPO DE GRÁFICO:
- "barra": comparar categorías (ventas por vendedor, por producto, por región).
- "linea": tendencias en el tiempo (ventas por mes, cotizaciones por semana).
- "torta": distribución porcentual (participación de mercado, mix de productos).
- "tabla": resultados con múltiples columnas o detalle de documentos.
- "numero": un único valor agregado (total del mes, cantidad de NV pendientes).
- "ninguno": pregunta conversacional sin datos o fuera del alcance.

Si la pregunta está fuera del alcance de los datos disponibles, responde con sql: null y tipo_grafico: "ninguno".
PROMPT;
    }

    private function getSchema(): string
    {
        return <<<SCHEMA
-- COTIZACIONES
-- cotizacion: id, sucursal_id, cliente_id, vendedor_id, fechahora, neto, iva, total, aprobstatus (0=pendiente,1=aprobada,2=rechazada) (soft delete)
-- cotizaciondetalle: id, cotizacion_id, producto_id, cant (cantidad), preciounit (precio unitario), subtotal, descuento, producto_nombre (soft delete)

-- NOTAS DE VENTA
-- notaventa: id, sucursal_id, cliente_id, vendedor_id, cotizacion_id, fechahora, neto, iva, total,
--            region_id (JOIN directo a region), provincia_id (JOIN directo a provincia),
--            comuna_id (JOIN directo a comuna), comunaentrega_id (soft delete)
-- IMPORTANTE: para agrupar por región usar notaventa.region_id = region.id directamente
-- IMPORTANTE: para agrupar por comuna usar notaventa.comuna_id = comuna.id directamente
-- notaventadetalle: id, notaventa_id, producto_id, cant (cantidad), preciounit (precio unitario), subtotal, descuento, producto_nombre (soft delete)

-- DOCUMENTOS TRIBUTARIOS (DTE - facturas, guías, notas crédito/débito)
-- dte: id, foliocontrol_id (1=factura, 2=guía de despacho, 5=nota crédito, 6=nota débito), nrodocto, fchemis (fecha emisión), cliente_id, vendedor_id, sucursal_id, mntneto, iva, mnttotal (soft delete)
-- dtedet: id, dte_id, producto_id, qtyitem (cantidad), prcitem (precio unitario), montoitem (monto línea = qty * precio), nmbitem (nombre ítem), dscitem (descripción) (soft delete)

-- CLIENTES Y VENDEDORES
-- cliente: id, razonsocial, nombrefantasia, rut, comunap_id (campo de comuna, con 'p'), regionp_id, provinciap_id (soft delete)
-- IMPORTANTE: en cliente el campo de comuna es comunap_id (NO comuna_id)
-- vendedor: id, persona_id, sta_activo (soft delete)
-- persona: id, nombre, rut (contiene el nombre real del vendedor)

-- PRODUCTOS
-- producto: id, glosa (nombre), sku (soft delete)

-- GEOGRAFÍA
-- comuna: id, nombre, region_id, provincia_id
-- region: id, nombre
-- provincia: id, nombre

-- OTROS
-- sucursal: id, nombre
-- foliocontrol: id, nombre (descripción del tipo de DTE)
SCHEMA;
    }
}
