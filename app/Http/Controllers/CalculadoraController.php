<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalculadoraController extends Controller
{
    /**
     * Muestra el formulario principal.
     */
    public function index()
    {
        // Creamos la lista manualmente
        $vendedores = collect([
            (object)['id' => 1, 'name' => 'Braulio Duarte-Jefe de Venta Empresarial-7886-2971'],
            (object)['id' => 2, 'name' => 'Richard Toribio-Venta Domiciliar-75591323'],
            (object)['id' => 3, 'name' => 'Angie Castro - Venta Domiciliar- 8786-0121'],
            (object)['id' => 4, 'name' => 'Stephany Mejia-Gerente Comercial- 8998-0892'],
            (object)['id' => 5, 'name' => 'Guiermo Moreno-Venta Occidente- 8588-3456'],
        ]);

        return view('welcome', compact('vendedores'));
    }

    /**
     * Procesa los datos y genera la vista de la factura.
     */
    public function generarFactura(Request $request)
    {
        // 1. Decodificar servicios con fallback a array vacío
        $serviciosElegidos = json_decode($request->input('servicios'), true);
        $serviciosElegidos = is_array($serviciosElegidos) ? $serviciosElegidos : [];

        // 2. Capturar datos de Branding y Datos del Formulario del Cliente
        $empresa  = $request->input('empresa', 'Espumas');
        $color    = $request->input('color', '#003399');
        $logo     = $request->input('logo', 'LOGOPNG.png');

        // Mapeo directo de tus inputs del formulario
        $cliente   = $request->input('cliente_nombre', 'Cliente General');
        $contacto  = $request->input('cliente_contacto', 'N/A');
        $direccion = $request->input('cliente_direccion', 'N/A');
        $telefono  = $request->input('cliente_telefono', 'N/A');
        $ruc       = $request->input('cliente_id', 'N/A');

        // 2.1 Capturar y procesar el Vendedor Seleccionado
        $vendedores = collect([
            1 => (object)['nombre' => 'Braulio Duarte', 'cargo' => ' Jefe de Venta Empresarial', 'tel' => '7886-2971'],
            2 => (object)['nombre' => 'Richard Toribio', 'cargo' => 'Venta Domiciliar', 'tel' => '7559-1323'],
            3 => (object)['nombre' => 'Angie Castro', 'cargo' => 'Venta Domiciliar', 'tel' => '8786-0121'],
            4 => (object)['nombre' => 'Stephany Mejia', 'cargo' => 'Gerente Comercial', 'tel' => '8998-0892'],
            5 => (object)['nombre' => 'Guiermo Moreno', 'cargo' => 'Venta Occidente', 'tel' => '8588-3456'],
        ]);

        $vendedorId = $request->input('user_id');
        $vendedorAsignado = $vendedores->get($vendedorId, (object)[
            'nombre' => 'Jammy Silva',
            'cargo' => 'Supervisora - Coordinadora',
            'tel' => '8588-5337'
        ]);

        // 3. Ubicación y Ruta
        $zona     = $request->input('zona', 'N/A');
        $ruta     = $request->input('ruta', 'N/A');
        $subruta  = $request->input('subruta', 'N/A');

        // 4. Procesar Totales y Normalizar nombres de servicios
        $subtotalServicios = 0;
        foreach ($serviciosElegidos as &$s) {
            if (!isset($s['nombre'])) {
                $s['nombre'] = $s['descripcion'] ?? $s['desc'] ?? 'Servicio/Producto';
            }

            // Aseguramos que precio y cantidad sean numéricos
            $precio   = isset($s['precio']) ? (float)$s['precio'] : 0;
            $cantidad = isset($s['cantidad']) ? (float)$s['cantidad'] : (isset($s['cant']) ? (float)$s['cant'] : 1);

            // Re-asignamos al array mapeando las llaves exactas que pide tu factura
            $s['desc'] = $s['nombre'];
            $s['cant'] = $cantidad;
            $s['precio'] = $precio;

            $subtotalServicios += ($precio * $cantidad);
        }
        unset($s);

        // 5. Capturar Gastos Extra y Descuentos
        $transporte = (float)$request->input('transporte', 0);
        $descuento  = (float)$request->input('descuento_total', 0);

        // 6. Configurar Moneda y Tasa de Cambio Dinámica
        $moneda = $request->input('moneda', 'C$');
        $tasaCambio = 37.00; // 🚀 Sincronizado perfectamente con tu JavaScript de tasa 37

        // 🚀 CONVERSIÓN MATEMÁTICA: Si la moneda seleccionada es dólares, dividimos los valores base
        if ($moneda === '$') {
            $subtotalServicios = $subtotalServicios / $tasaCambio;
            $transporte = $transporte / $tasaCambio;
            $descuento = $descuento / $tasaCambio;

            // También convertimos los precios unitarios de la lista de items para que la tabla cuadre
            foreach ($serviciosElegidos as &$item) {
                $item['precio'] = $item['precio'] / $tasaCambio;
            }
            unset($item);
        }

        // 6.1 Cálculos Finales (Procesados limpiamente en la moneda correcta)
        $subtotalGeneral = ($subtotalServicios + $transporte) - $descuento;

        // IVA (15%)
        $iva = $request->has('aplicar_iva') ? ($subtotalGeneral * 0.15) : 0;
        $totalFinal = $subtotalGeneral + $iva;

        // 7. Configurar Moneda (🚀 CORREGIDO: Eliminada la línea estática que forzaba Córdobas)
        $monedaSimbolo = $moneda;

        // 8. Fecha y hora Nicaragua
        $ahora = Carbon::now('America/Managua');

        // Un contador autogenerado simple en base a la hora para tu factura
        $nuevoContador = $request->input('contador', $ahora->format('mdHis'));

        // 9. Pasar todas las variables ajustadas a la vista de la factura
        return view('factura', [
            'empresa'            => $empresa,
            'color'              => $color,
            'logo'               => $logo,
            'cliente'            => $cliente,
            'contacto'           => $contacto,
            'direccion'          => $direccion,
            'telefono'           => $telefono,
            'ruc'                => $ruc,
            'zona'               => $zona,
            'ruta'               => $ruta,
            'subruta'            => $subruta,
            'fecha'              => $ahora->format('d/m/Y'),
            'hora'               => $ahora->format('h:i A'),
            'nuevoContador'      => $nuevoContador,
            'items'              => $serviciosElegidos,
            'transporte'         => $transporte,
            'descuento'          => $descuento,
            'subtotal'           => $subtotalGeneral,
            'iva'                => $iva,
            'total'              => $totalFinal,
            'moneda_simbolo'     => $monedaSimbolo,

            // Datos del vendedor asignado dinámico para la firma
            'responsable_nombre' => $vendedorAsignado->nombre,
            'responsable_cargo'  => $vendedorAsignado->cargo,
            'responsable_tel'    => $vendedorAsignado->tel,
        ]);
    }
}
