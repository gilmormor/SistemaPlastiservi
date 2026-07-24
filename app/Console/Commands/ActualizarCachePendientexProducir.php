<?php

namespace App\Console\Commands;

use App\Models\InvMov;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActualizarCachePendientexProducir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'producto:actualizar-cache-pendientexproducir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula la caché de pendiente por producir (stock - pendiente por despachar) por producto y sucursal. Se ejecuta cada 5 minutos vía scheduler para que calcprecioprodsn (#stockM) no tenga que ejecutar la consulta pesada en cada producto ingresado.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $aux_inicio = microtime(true);

        // Se autentica como el usuario id=1 (tiene acceso a todas las sucursales) porque
        // Producto::pendientexProducto y InvMov::stocksql restringen por auth()->id()->sucursales.
        // Es solo lectura: no se guarda ni modifica nada a nombre de este usuario.
        Auth::loginUsingId(1);

        $aux_meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        $aux_mesanno = $aux_meses[(int)date('n')] . " " . date('Y');

        $sucursales = Sucursal::whereNull('deleted_at')->get();
        $aux_totalfilas = 0;

        foreach ($sucursales as $sucursal) {
            $request = new Request();
            $request->merge([
                'sucursal_id'     => $sucursal->id,
                'groupby'         => ' group by notaventadetalle.producto_id ',
                'orderby'         => ' order by notaventadetalle.producto_id ',
                'FiltarxVendedor' => false,
                'mesanno'         => $aux_mesanno,
                'tipobodega'      => '1,2',
                'aprobstatus'     => '3',
            ]);

            //Mismas 2 consultas que usa el reporte reportinvstockbppendxprod, ejecutadas UNA vez por sucursal
            //(no una vez por producto), para pagar el costo de la vista/joins una sola vez.
            $pendientexprods = Producto::pendientexProducto($request, 2, 1);
            $datas = InvMov::stocksql($request, "producto.id");

            $InvStocks = [];
            foreach ($datas as $data) {
                $InvStocks[$data->producto_id] = $data->stock;
            }

            $aux_valores = []; // producto_id => difcantpend

            foreach ($pendientexprods as $pendientexprod) {
                $aux_cantpend = $pendientexprod->cant - $pendientexprod->cantdesp;
                //Misma fórmula del reporte: sin stock registrado, la diferencia queda como el pendiente en negativo
                $aux_dif = $aux_cantpend * -1;
                if (isset($InvStocks[$pendientexprod->producto_id])) {
                    $aux_dif = $InvStocks[$pendientexprod->producto_id] - $aux_cantpend;
                }
                $aux_valores[$pendientexprod->producto_id] = $aux_dif;
            }
            //Productos con stock pero sin pendiente: la diferencia es el stock completo
            foreach ($InvStocks as $productoId => $stock) {
                if (!isset($aux_valores[$productoId])) {
                    $aux_valores[$productoId] = $stock;
                }
            }

            //Laravel 6.2 no tiene DB::table()->upsert() (llegó en Laravel 8): se usa INSERT ... ON DUPLICATE KEY UPDATE en bloques
            $aux_ahora = now()->format('Y-m-d H:i:s');
            foreach (array_chunk(array_keys($aux_valores), 500, true) as $aux_chunk) {
                if (empty($aux_chunk)) {
                    continue;
                }
                $aux_placeholders = [];
                $aux_bindings = [];
                foreach ($aux_chunk as $productoId) {
                    $aux_placeholders[] = "(?, ?, ?, ?, ?)";
                    $aux_bindings[] = $productoId;
                    $aux_bindings[] = $sucursal->id;
                    $aux_bindings[] = $aux_valores[$productoId];
                    $aux_bindings[] = $aux_ahora;
                    $aux_bindings[] = $aux_ahora;
                }
                $aux_sql = "INSERT INTO producto_pendientexproducir_cache
                    (producto_id, sucursal_id, difcantpend, created_at, updated_at)
                    VALUES " . implode(',', $aux_placeholders) . "
                    ON DUPLICATE KEY UPDATE difcantpend = VALUES(difcantpend), updated_at = VALUES(updated_at)";
                DB::insert($aux_sql, $aux_bindings);
                $aux_totalfilas += count($aux_chunk);
            }
        }

        $aux_segundos = round(microtime(true) - $aux_inicio, 1);
        $this->info("Caché actualizada: $aux_totalfilas filas (producto+sucursal) en {$aux_segundos}s.");
    }
}
