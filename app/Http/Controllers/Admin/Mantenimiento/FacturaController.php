<?php
namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\FacturaOrdenTrabajo;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = FacturaOrdenTrabajo::with('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.mantenimiento.facturas.index', compact('facturas'));
    }
    
    public function show(FacturaOrdenTrabajo $factura)
    {
        $factura->load('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo', 
                      'ordenTrabajo.detallesRepuestos', 'ordenTrabajo.detallesServicios');
                      
        return view('admin.mantenimiento.facturas.show', compact('factura'));
    }
    
    public function update(Request $request, FacturaOrdenTrabajo $factura)
    {
        $request->validate([
            'metodo_pago' => 'required|string',
            'estado_pago' => 'required|in:pendiente,pagado,anulado',
            'notas' => 'nullable|string',
        ]);
        
        $factura->update($request->all());
        
        // Si se marca como pagada, actualizar el estado de la orden a entregado
        if ($request->estado_pago == 'pagado' && $factura->ordenTrabajo->estado == 'facturado') {
            $factura->ordenTrabajo->update(['estado' => 'entregado']);
        }
        
        return redirect()->route('admin.mantenimiento.facturas.show', $factura->id)
                        ->with('success', 'Factura actualizada con éxito');
    }
    
    public function imprimir(FacturaOrdenTrabajo $factura)
    {
        $factura->load('ordenTrabajo.cliente', 'ordenTrabajo.vehiculo', 
                     'ordenTrabajo.detallesRepuestos', 'ordenTrabajo.detallesServicios');
                     
        return view('admin.mantenimiento.facturas.print', compact('factura'));
    }
}