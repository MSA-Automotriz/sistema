# Cambios e Implementaciones

---

## 1. Sidebar — No tapar el contenido principal
**Fecha:** 20 de Mayo 2026 
**Archivo:** `resources/views/admin/layouts/app.blade.php`

**Problema:** El sidebar al colapsar/expandir tapaba el contenido principal.  
**Solución:** Se agregaron clases CSS en el `<body>` (`sidebar-collapsed` / `sidebar-expanded`) con márgenes dinámicos para el contenido principal.

```css
body.sidebar-collapsed .main-content { margin-left: 5rem; }
body.sidebar-expanded  .main-content { margin-left: 16rem; }
```

---

## 2. Lector de código de barras con carrito — Catálogo de Partes
**Fecha:** 20 de Mayo 2026  
**Archivos modificados:**
- `resources/views/admin/productos-servicios/partes-repuestos/index.blade.php`
- `app/Http/Controllers/Admin/Almacenes/ParteController.php`
- `routes/web.php`

**Funcionalidades implementadas:**
- Campo de texto con soporte para lector físico USB/Bluetooth (captura `Enter` automático)
- Botón de cámara con `html5-qrcode` para escanear desde el celular
- Carrito de compras con controles `+/-`, eliminación de ítems y total
- Búsqueda AJAX por código de barras (sin recargar la página)
- Feedback visual verde/rojo al escanear (éxito/error)
- Auto-refocus del campo después de cada escaneo

**Ruta agregada:**
```php
Route::get('/buscar-codigo', [ParteController::class, 'buscarPorCodigo'])->name('buscar-codigo');
```

**Método agregado en controlador:**
```php
public function buscarPorCodigo(Request $request) // retorna JSON con datos de la parte
```

---

## 3. Alpine.js — Carga via CDN
**Fecha:** 20 de Mayo 2026  
**Archivo:** `resources/views/admin/layouts/app.blade.php`

**Problema:** Alpine.js no estaba instalado en el proyecto (ni en `package.json` ni importado). Todos los botones con `@click`, `x-show`, `x-data` del sistema no funcionaban.  
**Solución:** Se agregó la carga de Alpine.js via CDN en el `<head>`:

```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## 4. Soporte de cámara en red local (HTTP → HTTPS)
**Fecha:** 20 de Mayo 2026  
**Archivo:** `resources/views/admin/productos-servicios/partes-repuestos/index.blade.php`

**Problema:** Los navegadores modernos bloquean el acceso a la cámara en contextos HTTP (no HTTPS). Al acceder desde el celular via `http://192.168.1.191/...` la cámara no funcionaba.  
**Solución en código:** Se agregó detección de `window.isSecureContext` con mensaje de error descriptivo y pasos para habilitar el flag de Chrome:

```
chrome://flags/#unsafely-treat-insecure-origin-as-secure
→ Agregar: http://192.168.1.191
→ Cambiar a Enabled → Relaunch
```

También se mejoró el manejo de errores de cámara con mensajes específicos por tipo (`NotAllowedError`, `NotFoundError`, etc.).

---

---

## 5. Cámara al crear nueva parte (campo Código)
**Fecha:** 21 de Mayo 2026  
**Archivo:** `resources/views/admin/productos-servicios/partes-repuestos/create.blade.php`

**Problema:** Al crear una nueva parte, el código debía ingresarse manualmente.  
**Solución:** Se agregó botón de cámara (`<i class="fas fa-camera">`) junto al campo "Código" usando Alpine.js (`scannerCodigo()`). Al escanear un código de barras/QR, se rellena automáticamente el campo y se posiciona el foco.  
- Usa la misma librería `html5-qrcode@2.3.8`  
- Misma detección de contexto seguro (HTTPS) con mensaje de ayuda  
- Cargado via `@push('scripts')` al final del body

---

---

## 6. Flujo de procesamiento del carrito del escáner (Venta / Taller)
**Fecha:** 21 de Mayo 2026  
**Archivos modificados:**
- `resources/views/admin/productos-servicios/partes-repuestos/index.blade.php`
- `app/Http/Controllers/Admin/Mantenimiento/OrdenTrabajoMantenimientoController.php`
- `routes/web.php`

**Funcionalidades implementadas:**
- Botón "Procesar Carrito" en el pie de la tabla del carrito (visible cuando hay ítems)
- Modal Alpine.js con 2 pasos según destino:
  - **Paso 1:** Elegir destino — "Venta Directa" o "Enviar al Taller"
  - **Paso 2A — Venta Directa:** Búsqueda de cliente (AJAX), forma de pago, tipo de documento, moneda; llama al endpoint existente `admin.ventas.pos.procesar-venta`
  - **Paso 2B — Enviar al Taller:** Búsqueda de órdenes de trabajo activas (AJAX) por código/cliente/placa; agrega los repuestos del carrito a la orden seleccionada

**Rutas agregadas:**
```php
// Buscar órdenes activas (no entregadas)
Route::get('ordenes/buscar-activas', [..., 'buscarActivas'])->name('ordenes.buscar-activas');
// Agregar repuestos en lote a una orden
Route::post('ordenes/{orden}/agregar-repuestos-lote', [..., 'agregarRepuestosLote'])->name('ordenes.agregar-repuestos-lote');
```

**Métodos agregados en `OrdenTrabajoMantenimientoController`:**
```php
public function buscarActivas(Request $request)  // Busca órdenes activas, retorna JSON
public function agregarRepuestosLote(Request $request, OrdenTrabajoMantenimiento $orden) // Agrega ítems del carrito
```

**Fix de z-index:** El modal se colocó fuera del `container-fluid` (que tiene `position: relative; z-index: 10`) para evitar que el contexto de apilamiento CSS bloqueara los clics. El modal usa `z-index: 10000`, `@click.self` para cerrar al hacer clic en el fondo, y `type="button"` en todos los botones para evitar submit accidental.

---

## Pendientes / Por implementar
- [ ] Configurar HTTPS en XAMPP para solución definitiva de cámara sin flags
- [ ] Demora en el tiempo de respuesta
- [ ] Hacerlo adaptable para tèlefono
- [ ] Agregar botones de Imprimir a algunos Paneles
- [ ] 
- [ ]
