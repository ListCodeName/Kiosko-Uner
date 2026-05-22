<?php

namespace Tests\Feature;

use App\Models\Compra;
use App\Models\Egreso;
use App\Models\Ingreso;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsQueryTest extends TestCase
{
    use RefreshDatabase;

    protected User $alumno;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario con rol alumno para las peticiones autenticadas
        $this->alumno = User::create([
            'name' => 'Alumno Test',
            'username' => 'alumnotest',
            'email' => 'alumno@test.com',
            'role' => 'alumno',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Test que valida que los invitados (usuarios no autenticados) sean redirigidos
     * o no tengan acceso a los endpoints de estadísticas.
     */
    public function test_guests_cannot_access_stats_endpoints(): void
    {
        $this->getJson('/panel/api/ventas')->assertStatus(401);
        $this->getJson('/panel/api/ingresos')->assertStatus(401);
        $this->getJson('/panel/api/egresos')->assertStatus(401);
        $this->getJson('/api/compras')->assertStatus(401);
    }

    /**
     * Test que valida que un alumno autenticado pueda acceder exitosamente
     * a todos los endpoints requeridos por el módulo de estadísticas.
     */
    public function test_authenticated_alumno_can_access_stats_endpoints(): void
    {
        $this->actingAs($this->alumno)
            ->getJson('/panel/api/ventas')
            ->assertStatus(200);

        $this->actingAs($this->alumno)
            ->getJson('/panel/api/ingresos')
            ->assertStatus(200);

        $this->actingAs($this->alumno)
            ->getJson('/panel/api/egresos')
            ->assertStatus(200);

        $this->actingAs($this->alumno)
            ->getJson('/api/compras')
            ->assertStatus(200);
    }

    /**
     * Test que valida que la API de Ventas retorne la estructura y los datos correctos
     * incluyendo el tipo float para el total, metodo y estado correctos.
     */
    public function test_ventas_api_returns_correct_data_and_structure(): void
    {
        // Crear una venta de prueba
        $sale = Sale::create([
            'user_id' => $this->alumno->id,
            'cliente' => 'Cliente Test',
            'total' => 1500.50,
            'metodo_pago' => 'transferencia',
            'estado' => 'pagado',
            'observaciones' => 'Venta de prueba para testing',
        ]);

        $response = $this->actingAs($this->alumno)->getJson('/panel/api/ventas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'fecha',
                    'hora',
                    'cliente',
                    'total',
                    'metodo',
                    'estado',
                    'obs',
                    'items',
                ]
            ]);

        // Validar tipos de datos y valores del elemento retornado
        $data = $response->json();
        $this->assertNotEmpty($data);
        $firstSale = $data[0];
        
        $this->assertEquals($sale->id, $firstSale['id']);
        $this->assertSame(1500.50, $firstSale['total']); // Debe ser float
        $this->assertEquals('transferencia', $firstSale['metodo']);
        $this->assertEquals('pagado', $firstSale['estado']);
    }

    /**
     * Test que valida que la API de Ingresos retorne la estructura y datos correctos.
     */
    public function test_ingresos_api_returns_correct_data_and_structure(): void
    {
        $ingreso = Ingreso::create([
            'fecha' => '2026-05-21',
            'tipo' => 'donacion',
            'descripcion' => 'Donación manual para el kiosco',
            'monto' => 5000.00,
            'estado' => 'efectuado',
            'user_id' => $this->alumno->id,
        ]);

        $response = $this->actingAs($this->alumno)->getJson('/panel/api/ingresos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ingresos' => [
                    '*' => [
                        'id',
                        'fecha',
                        'tipo',
                        'descripcion',
                        'monto',
                        'estado',
                        'user_id',
                    ]
                ]
            ]);

        $data = $response->json('ingresos');
        $this->assertNotEmpty($data);
        $this->assertEquals($ingreso->id, $data[0]['id']);
        $this->assertEquals(5000.00, $data[0]['monto']);
        $this->assertEquals('donacion', $data[0]['tipo']);
    }

    /**
     * Test que valida que la API de Egresos retorne la estructura y datos correctos.
     */
    public function test_egresos_api_returns_correct_data_and_structure(): void
    {
        $egreso = Egreso::create([
            'fecha' => '2026-05-21',
            'tipo' => 'servicio',
            'descripcion' => 'Pago de Internet',
            'monto' => 3200.00,
            'estado' => 'efectuado',
            'user_id' => $this->alumno->id,
        ]);

        $response = $this->actingAs($this->alumno)->getJson('/panel/api/egresos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'egresos' => [
                    '*' => [
                        'id',
                        'fecha',
                        'tipo',
                        'descripcion',
                        'monto',
                        'estado',
                        'user_id',
                    ]
                ]
            ]);

        $data = $response->json('egresos');
        $this->assertNotEmpty($data);
        $this->assertEquals($egreso->id, $data[0]['id']);
        $this->assertEquals(3200.00, $data[0]['monto']);
        $this->assertEquals('servicio', $data[0]['tipo']);
    }

    /**
     * Test que valida que la API de Compras retorne la estructura y datos correctos.
     */
    public function test_compras_api_returns_correct_data_and_structure(): void
    {
        $compra = Compra::create([
            'fecha' => '2026-05-21',
            'total' => 8500.00,
            'observaciones' => 'Compra de bebidas semanal',
        ]);

        $response = $this->actingAs($this->alumno)->getJson('/api/compras');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'compras' => [
                    '*' => [
                        'id',
                        'fecha',
                        'fecha_raw',
                        'total',
                        'observaciones',
                        'items',
                    ]
                ],
                'total'
            ]);

        $data = $response->json('compras');
        $this->assertNotEmpty($data);
        $this->assertEquals($compra->id, $data[0]['id']);
        $this->assertEquals(8500.00, $data[0]['total']);
    }

    /**
     * Test de lógica de negocio:
     * Verifica que registrar una compra a través del controlador cree automáticamente
     * un Egreso del tipo 'insumos' por el mismo total de la compra.
     */
    public function test_creating_a_compra_creates_automatic_egreso(): void
    {
        // Creamos categoría primero
        $category = ProductCategory::create([
            'name' => 'Bebidas',
            'icon' => '🥤',
            'sort_order' => 1,
            'is_produced' => false,
        ]);

        // Creamos un producto de prueba para la compra
        $product = Product::create([
            'name' => 'Coca Cola 500ml',
            'description' => 'Gaseosa',
            'tipo' => 'reventa',
            'price' => 120.00,
            'stock' => 10,
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $compraData = [
            'fecha' => '2026-05-21',
            'observaciones' => 'Compra automatizada de test',
            'items' => [
                [
                    'product_id' => $product->id,
                    'producto_nombre' => $product->name,
                    'tipo_producto' => 'reventa',
                    'cantidad' => 5,
                    'precio_unitario' => 100.00,
                ]
            ]
        ];

        // Simulamos la inserción vía POST
        $response = $this->actingAs($this->alumno)->postJson('/api/compras', $compraData);

        $response->assertStatus(201);

        // Verificamos que la compra exista en la DB
        $this->assertDatabaseHas('compras', [
            'total' => 500.00,
        ]);

        // Verificamos que se haya generado el Egreso automático correspondiente
        $this->assertDatabaseHas('egresos', [
            'tipo' => 'insumos',
            'monto' => 500.00,
            'estado' => 'efectuado',
        ]);
        
        $egreso = Egreso::where('tipo', 'insumos')->where('monto', 500.00)->first();
        $this->assertNotNull($egreso);
        $this->assertStringStartsWith('Compra mercadería #', $egreso->descripcion);
    }
}
