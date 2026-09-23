<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DocumentValidationService
{
    private $dniApis = [
        'apisperu' => 'https://dniruc.apisperu.com/api/v1/dni/{numero}?token=',
        'apis_net_pe' => 'https://api.apis.net.pe/v1/dni?numero={numero}',
        'apisunat' => 'https://apisunat.com/personas/{numero}',
    ];
    
    private $rucApis = [
        'apisperu' => 'https://dniruc.apisperu.com/api/v1/ruc/{numero}?token=',
        'apis_net_pe' => 'https://api.apis.net.pe/v1/ruc?numero={numero}',
        'apisunat' => 'https://apisunat.com/empresas/{numero}',
    ];

    /**
     * Valida un DNI usando múltiples APIs con fallback
     */
    public function validateDni($numero)
    {
        // Validar formato DNI (8 dígitos)
        if (!preg_match('/^\d{8}$/', $numero)) {
            return [
                'success' => false,
                'message' => 'El DNI debe tener 8 dígitos'
            ];
        }

        // Intentar con cada API hasta que una funcione
        foreach ($this->dniApis as $apiName => $urlTemplate) {
            try {
                $result = $this->callDniApi($apiName, $numero, $urlTemplate);
                if ($result['success']) {
                    return $result;
                }
            } catch (Exception $e) {
                Log::warning("API {$apiName} falló para DNI {$numero}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'No se pudo validar el DNI en este momento. Intente nuevamente.'
        ];
    }

    /**
     * Valida un RUC usando múltiples APIs con fallback
     */
    public function validateRuc($numero)
    {
        // Validar formato RUC (11 dígitos)
        if (!preg_match('/^\d{11}$/', $numero)) {
            return [
                'success' => false,
                'message' => 'El RUC debe tener 11 dígitos'
            ];
        }

        // Intentar con cada API hasta que una funcione
        foreach ($this->rucApis as $apiName => $urlTemplate) {
            try {
                $result = $this->callRucApi($apiName, $numero, $urlTemplate);
                if ($result['success']) {
                    return $result;
                }
            } catch (Exception $e) {
                Log::warning("API {$apiName} falló para RUC {$numero}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'No se pudo validar el RUC en este momento. Intente nuevamente.'
        ];
    }

    /**
     * Llama a API específica para DNI
     */
    private function callDniApi($apiName, $numero, $urlTemplate)
    {
        $url = str_replace('{numero}', $numero, $urlTemplate);
        
        switch ($apiName) {
            case 'apisperu':
                return $this->callApisPeruDni($url, $numero);
            case 'apis_net_pe':
                return $this->callApisNetPeDni($url, $numero);
            case 'apisunat':
                return $this->callApiSunatDni($url, $numero);
            default:
                throw new Exception("API no soportada: {$apiName}");
        }
    }

    /**
     * Llama a API específica para RUC
     */
    private function callRucApi($apiName, $numero, $urlTemplate)
    {
        $url = str_replace('{numero}', $numero, $urlTemplate);
        
        switch ($apiName) {
            case 'apisperu':
                return $this->callApisPeruRuc($url, $numero);
            case 'apis_net_pe':
                return $this->callApisNetPeRuc($url, $numero);
            case 'apisunat':
                return $this->callApiSunatRuc($url, $numero);
            default:
                throw new Exception("API no soportada: {$apiName}");
        }
    }

    /**
     * APIs Peru - DNI (GRATUITA)
     */
    private function callApisPeruDni($baseUrl, $numero)
    {
        // Token gratuito (configurable)
        $token = config('services.apisperu.token', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAZ21haWwuY29tIn0.7zOGLzSyaWMVzTrJNIZG21CaLZD49v0KjC5kpJGJOWs');
        $url = $baseUrl . $token;
        
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['success']) && $data['success']) {
                return [
                    'success' => true,
                    'data' => [
                        'nombres' => $data['nombres'] ?? '',
                        'apellido_paterno' => $data['apellidoPaterno'] ?? '',
                        'apellido_materno' => $data['apellidoMaterno'] ?? '',
                        'numero_documento' => $numero
                    ],
                    'source' => 'apisperu'
                ];
            }
        }
        
        throw new Exception('API ApisfPeru DNI no disponible');
    }

    /**
     * APIs.net.pe - DNI (GRATUITA con límites)
     */
    private function callApisNetPeDni($url, $numero)
    {
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (!empty($data['nombre'])) {
                // Dividir el nombre completo
                $nombreCompleto = $data['nombre'];
                $partes = explode(' ', trim($nombreCompleto));
                
                return [
                    'success' => true,
                    'data' => [
                        'nombres' => implode(' ', array_slice($partes, 2)) ?: $nombreCompleto,
                        'apellido_paterno' => $partes[0] ?? '',
                        'apellido_materno' => $partes[1] ?? '',
                        'numero_documento' => $numero
                    ],
                    'source' => 'apis.net.pe'
                ];
            }
        }
        
        throw new Exception('API apis.net.pe DNI no disponible');
    }

    /**
     * ApiSunat - DNI (GRATUITA básica)
     */
    private function callApiSunatDni($url, $numero)
    {
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['nombres'])) {
                return [
                    'success' => true,
                    'data' => [
                        'nombres' => $data['nombres'] ?? '',
                        'apellido_paterno' => $data['apellido_paterno'] ?? '',
                        'apellido_materno' => $data['apellido_materno'] ?? '',
                        'numero_documento' => $numero
                    ],
                    'source' => 'apisunat'
                ];
            }
        }
        
        throw new Exception('API ApiSunat DNI no disponible');
    }

    /**
     * APIs Peru - RUC (GRATUITA)
     */
    private function callApisPeruRuc($baseUrl, $numero)
    {
        $token = config('services.apisperu.token', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAZ21haWwuY29tIn0.7zOGLzSyaWMVzTrJNIZG21CaLZD49v0KjC5kpJGJOWs');
        $url = $baseUrl . $token;
        
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['success']) && $data['success']) {
                return [
                    'success' => true,
                    'data' => [
                        'nombre_o_razon_social' => $data['razonSocial'] ?? '',
                        'estado' => $data['estado'] ?? '',
                        'condicion' => $data['condicion'] ?? '',
                        'direccion' => $data['direccion'] ?? '',
                        'departamento' => $data['departamento'] ?? '',
                        'provincia' => $data['provincia'] ?? '',
                        'distrito' => $data['distrito'] ?? '',
                        'numero_documento' => $numero
                    ],
                    'source' => 'apisperu'
                ];
            }
        }
        
        throw new Exception('API ApisPeru RUC no disponible');
    }

    /**
     * APIs.net.pe - RUC (GRATUITA con límites)
     */
    private function callApisNetPeRuc($url, $numero)
    {
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (!empty($data['nombre'])) {
                return [
                    'success' => true,
                    'data' => [
                        'nombre_o_razon_social' => $data['nombre'] ?? '',
                        'estado' => $data['estado'] ?? '',
                        'condicion' => $data['condicion'] ?? '',
                        'direccion' => $data['direccion'] ?? '',
                        'departamento' => $data['departamento'] ?? '',
                        'provincia' => $data['provincia'] ?? '',
                        'distrito' => $data['distrito'] ?? '',
                        'numero_documento' => $numero
                    ],
                    'source' => 'apis.net.pe'
                ];
            }
        }
        
        throw new Exception('API apis.net.pe RUC no disponible');
    }

    /**
     * ApiSunat - RUC (GRATUITA básica)
     */
    private function callApiSunatRuc($url, $numero)
    {
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['razon_social'])) {
                return [
                    'success' => true,
                    'data' => [
                        'nombre_o_razon_social' => $data['razon_social'] ?? '',
                        'estado' => $data['estado'] ?? '',
                        'condicion' => $data['condicion'] ?? '',
                        'direccion' => $data['direccion'] ?? '',
                        'departamento' => $data['departamento'] ?? '',
                        'provincia' => $data['provincia'] ?? '',
                        'distrito' => $data['distrito'] ?? '',
                        'numero_documento' => $numero
                    ],
                    'source' => 'apisunat'
                ];
            }
        }
        
        throw new Exception('API ApiSunat RUC no disponible');
    }
}