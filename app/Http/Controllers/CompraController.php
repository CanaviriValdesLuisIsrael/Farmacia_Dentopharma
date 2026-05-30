<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use App\Models\LoteProducto;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            // =========================
            // VALIDACIONES
            // =========================
            $request->validate([
                'cliente' => 'required|string|max:255',
                'dni' => 'required|numeric',
                'carrito' => 'required|array|min:1',
                'carrito.*.id_producto' => 'required|integer',
                'carrito.*.cantidad' => 'required|integer|min:1',
                'carrito.*.precio' => 'required|numeric|min:0'
            ]);

            // =========================
            // CREAR CLIENTE
            // =========================
            // =========================
            // BUSCAR O CREAR CLIENTE
            // =========================
            $cliente = Cliente::where('ci', $request->dni)->first();

            if (!$cliente) {

                $cliente = Cliente::create([
                    'nombre' => $request->cliente,
                    'ci' => $request->dni,
                    'apellido' => '',
                    'nro_contacto' => '',
                    'tipo_cliente' => 'Normal'
                ]);
            }

            // =========================
            // CREAR VENTA
            // =========================
            $venta = Venta::create([
                'fecha_venta' => now(),
                'total_venta' => 0,
                'metodo_pago' => 'Efectivo',
                'id_empleado' => auth()->user()->empleado->id_empleado,
                'id_cliente' => $cliente->id_cliente
            ]);

            $totalVenta = 0;

            // =========================
            // RECORRER CARRITO
            // =========================
            foreach ($request->carrito as $item) {

                $cantidadNecesaria = (int) $item['cantidad'];
                $precio = (float) $item['precio'];

                // =========================
                // LOTES VALIDOS (NO VENCIDOS)
                // =========================
                $lotes = LoteProducto::where('id_producto', $item['id_producto'])
                    ->where('cantidad_por_caja', '>', 0)
                    ->whereDate('fecha_vencimiento', '>', now())
                    ->orderBy('fecha_vencimiento', 'asc') // FEFO
                    ->lockForUpdate()
                    ->get();

                if ($lotes->isEmpty()) {
                    throw new \Exception("El producto no tiene stock disponible o está vencido");
                }

                // =========================
                // CONSUMO DE LOTES
                // =========================
                foreach ($lotes as $lote) {

                    if ($cantidadNecesaria <= 0) break;

                    $disponible = (int) $lote->cantidad_por_caja;

                    if ($disponible <= 0) continue;

                    $cantidadTomada = min($disponible, $cantidadNecesaria);
                    $subtotal = $cantidadTomada * $precio;

                    // =========================
                    // GUARDAR DETALLE
                    // =========================
                    DetalleVenta::create([
                        'id_venta' => $venta->id_venta,
                        'id_lote' => $lote->id_lote,
                        'id_producto' => $item['id_producto'], //  CLAVE
                        'cantidad' => $cantidadTomada,
                        'precio_unitario' => $precio,
                        'subtotal' => $subtotal
                    ]);

                    // =========================
                    // ACTUALIZAR STOCK DEL LOTE
                    // =========================
                    $lote->cantidad_por_caja -= $cantidadTomada;
                    $lote->save();

                    $cantidadNecesaria -= $cantidadTomada;
                    $totalVenta += $subtotal;
                }

                // =========================
                // VALIDAR STOCK SUFICIENTE
                // =========================
                if ($cantidadNecesaria > 0) {
                    throw new \Exception("Stock insuficiente para el producto ID: {$item['id_producto']}");
                }
            }

            // =========================
            // ACTUALIZAR TOTAL VENTA
            // =========================
            $venta->total_venta = $totalVenta;
            $venta->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'id_venta' => $venta->id_venta
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // =========================================
    // BUSCAR CLIENTE POR CI
    // =========================================
    public function buscarCliente($ci)
    {
        $cliente = Cliente::where('ci', $ci)->first();

        if ($cliente) {

            return response()->json([
                'success' => true,
                'cliente' => $cliente
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
