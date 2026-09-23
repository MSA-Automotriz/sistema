<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Version;
use App\Models\AnioModelo;
use App\Models\Color;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

HeadingRowFormatter::default('none'); // Disable automatic slugging of headers

class VehiculoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Validar los datos de la fila
        $validator = Validator::make($row, [
            'Fecha de compra' => 'required|date_format:Y-m-d',
            'Precio compra' => 'required|numeric|min:0',
            'Nro de factura' => 'required|string|max:255',
            'Marca' => 'required|string|max:255',
            'Modelo' => 'required|string|max:255',
            'Version' => 'required|string|max:255',
            'Año' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'Serie VIN' => 'required|string|max:17|unique:catalogos,serie_vin',
            'Color' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Error en la fila: ' . json_encode($validator->errors()->all()));
        }

        // Buscar o crear Marca
        $marca = Marca::firstOrCreate(
            ['nombre' => $row['Marca']],
            ['nombre' => $row['Marca']]
        );

        // Buscar o crear Modelo
        $modelo = Modelo::firstOrCreate(
            [
                'marca_id' => $marca->id,
                'nombre' => $row['Modelo'],
            ],
            [
                'marca_id' => $marca->id,
                'nombre' => $row['Modelo'],
                'duracion_garantia' => null,
                'cantidad_anos' => null,
                'ficha_tecnica' => null,
            ]
        );

        // Buscar o crear Version
        $version = Version::firstOrCreate(
            [
                'marca_id' => $marca->id,
                'modelo_id' => $modelo->id,
                'nombre' => $row['Version'],
            ],
            [
                'marca_id' => $marca->id,
                'modelo_id' => $modelo->id,
                'nombre' => $row['Version'],
                'carroceria' => null,
                'cilindrada' => null,
                'transmision' => null,
                'traccion' => null,
                'combustible_id' => null,
            ]
        );

        // Buscar o crear AnioModelo
        $anioModelo = AnioModelo::firstOrCreate(
            [
                'marca_id' => $marca->id,
                'modelo_id' => $modelo->id,
                'version_id' => $version->id,
                'anio' => $row['Año'],
            ],
            [
                'marca_id' => $marca->id,
                'modelo_id' => $modelo->id,
                'version_id' => $version->id,
                'anio' => $row['Año'],
                'precio' => $row['Precio compra'],
                'moneda' => 'USD',
            ]
        );

        // Buscar o crear Color
        $color = Color::firstOrCreate(
            ['nombre' => $row['Color']],
            ['nombre' => $row['Color'], 'hexadecimal' => null]
        );

        // Crear Vehiculo
        return new Vehiculo([
            'marca_id' => $marca->id,
            'modelo_id' => $modelo->id,
            'version_id' => $version->id,
            'anio_modelo_id' => $anioModelo->id,
            'color_id' => $color->id,
            'fotografia' => null,
            'fecha_compra' => $row['Fecha de compra'],
            'precio_compra' => $row['Precio compra'],
            'nro_factura' => $row['Nro de factura'],
            'serie_vin' => $row['Serie VIN'],
        ]);
    }
}

class VehiculoImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.productos-servicios.vehiculos.pestanas');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new VehiculoImport, $request->file('file'));
            return redirect()->back()->with('success', 'Vehículos importados exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}