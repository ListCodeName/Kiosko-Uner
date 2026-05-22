<?php

namespace Tests\Feature;

use App\Models\Egreso;
use App\Models\Group;
use App\Models\Ingreso;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfesorEconTest extends TestCase
{
    use RefreshDatabase;

    protected User $profesor;
    protected User $student;
    protected Group $group;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un profesor
        $this->profesor = User::create([
            'name' => 'Profesor Test',
            'username' => 'profesortest',
            'email' => 'profesor@test.com',
            'role' => 'profesor',
            'password' => bcrypt('password123'),
        ]);

        // Crear un alumno
        $this->student = User::create([
            'name' => 'Student Test',
            'username' => 'studenttest',
            'email' => 'student@test.com',
            'role' => 'alumno',
            'password' => bcrypt('password123'),
        ]);

        // Crear un grupo a cargo de este profesor
        $this->group = Group::create([
            'name' => 'Grupo A',
            'description' => 'Grupo Mañana',
            'professor_id' => $this->profesor->id,
        ]);

        // Asociar alumno al grupo
        $this->group->students()->attach($this->student->id);
    }

    /**
     * Test que valida que los invitados (guests) no puedan acceder al endpoint económico.
     */
    public function test_guests_cannot_access_economic_endpoint(): void
    {
        $response = $this->getJson('/profesor/api/performance/economico');
        $response->assertStatus(401); // Redirección o no autorizado
    }

    /**
     * Test que valida que un alumno no pueda acceder al endpoint económico del profesor.
     */
    public function test_students_cannot_access_professor_economic_endpoint(): void
    {
        $response = $this->actingAs($this->student)->getJson('/profesor/api/performance/economico');
        $response->assertStatus(302); // Redirect instead of 403 because of RoleMiddleware
    }

    /**
     * Test que valida que el profesor autenticado sí acceda exitosamente.
     */
    public function test_profesor_can_access_economic_endpoint(): void
    {
        $response = $this->actingAs($this->profesor)->getJson('/profesor/api/performance/economico');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'period' => [
                    'start',
                    'end',
                ],
                'totals' => [
                    'ganancia_efectiva',
                    'perdida_efectiva',
                    'balance_neto',
                    'margen',
                ],
                'groups' => [
                    '*' => [
                        'id',
                        'name',
                        'member_count',
                        'ventas_total',
                        'compras_total',
                        'ingresos_total',
                        'egresos_total',
                        'ganancia_efectiva',
                        'perdida_efectiva',
                        'balance_neto',
                        'pct_ingreso',
                    ]
                ]
            ]);
    }

    /**
     * Test que valida la precisión de los cálculos históricos vs de rango de fechas
     * y el comportamiento correcto de la desduplicación.
     */
    public function test_economic_calculation_accuracy_and_date_filtering(): void
    {
        // 1. Crear transacciones dentro del período actual (Mayo 2026)
        // Venta cobrada: aporta a Ventas
        $sale1 = new Sale([
            'user_id' => $this->student->id,
            'cliente' => 'Cliente A',
            'total' => 2000.00,
            'metodo_pago' => 'efectivo',
            'estado' => 'pagado',
        ]);
        $sale1->created_at = '2026-05-10 10:00:00';
        $sale1->save();

        // Venta cobrada automática que genera Ingreso automático (venta_kiosco)
        // Este ingreso automático debe excluirse de los ingresos manuales para evitar doble cómputo.
        Ingreso::create([
            'fecha' => '2026-05-10',
            'tipo' => 'venta_kiosco',
            'descripcion' => 'Venta POS #1',
            'monto' => 2000.00,
            'estado' => 'efectuado',
            'user_id' => $this->student->id,
        ]);

        // Ingreso manual efectuado: aporta a Otros Ingresos
        Ingreso::create([
            'fecha' => '2026-05-15',
            'tipo' => 'donacion',
            'descripcion' => 'Donación cooperadora',
            'monto' => 500.00,
            'estado' => 'efectuado',
            'user_id' => $this->student->id,
        ]);

        // Compra automatizada: representada por Egreso tipo insumos con descripción especial
        Egreso::create([
            'fecha' => '2026-05-12',
            'tipo' => 'insumos',
            'descripcion' => 'Compra mercadería #123',
            'monto' => 1200.00,
            'estado' => 'efectuado',
            'user_id' => $this->student->id,
        ]);

        // Egreso manual: aporta a Otros Egresos
        Egreso::create([
            'fecha' => '2026-05-18',
            'tipo' => 'servicio',
            'descripcion' => 'Internet',
            'monto' => 300.00,
            'estado' => 'efectuado',
            'user_id' => $this->student->id,
        ]);

        // 2. Crear transacciones fuera del período actual (Futuro: Junio 2026)
        // Aportará al Histórico consolidado, pero NO al rango de Mayo 2026.
        $sale2 = new Sale([
            'user_id' => $this->student->id,
            'cliente' => 'Cliente B',
            'total' => 1000.00,
            'metodo_pago' => 'transferencia',
            'estado' => 'pagado',
        ]);
        $sale2->created_at = '2026-06-05 15:30:00';
        $sale2->save();

        // Realizamos petición para el rango de Mayo 2026
        $response = $this->actingAs($this->profesor)->getJson(
            '/profesor/api/performance/economico?start_date=2026-05-01&end_date=2026-05-31'
        );

        $response->assertStatus(200);

        // Validaciones en Banners Históricos (Total Acumulado Histórico, que incluye Junio 2026)
        // Histórico Ganancia = Mayo Ventas (2000) + Mayo Ingreso Manual (500) + Junio Ventas (1000) = 3500.00
        // Histórico Pérdida = Mayo Compra (1200) + Mayo Egreso Manual (300) = 1500.00
        // Histórico Balance = 3500 - 1500 = 2000.00
        $data = $response->json();
        $this->assertEquals(3500.00, $data['totals']['ganancia_efectiva']);
        $this->assertEquals(1500.00, $data['totals']['perdida_efectiva']);
        $this->assertEquals(2000.00, $data['totals']['balance_neto']);
        $this->assertEquals(57.1, $data['totals']['margen']);

        // Validaciones en la Grilla de Grupos (Filtrado por Mayo 2026, excluyendo Junio 2026)
        // Rango Ventas = 2000.00
        // Rango Compras = 1200.00
        // Rango Otros Ingresos (donacion) = 500.00
        // Rango Otros Egresos (servicio) = 300.00
        // Rango Ganancia Efectiva = 2000 + 500 = 2500.00
        // Rango Pérdida Efectiva = 1200 + 300 = 1500.00
        // Rango Balance Neto = 2500 - 1500 = 1000.00
        // Rango Pct Ingreso = (2500 / 4000) * 100 = 63%
        $groupData = $response->json('groups.0');
        $this->assertEquals('Grupo A', $groupData['name']);
        $this->assertEquals(1, $groupData['member_count']);
        $this->assertEquals(2000.00, $groupData['ventas_total']);
        $this->assertEquals(1200.00, $groupData['compras_total']);
        $this->assertEquals(500.00, $groupData['ingresos_total']);
        $this->assertEquals(300.00, $groupData['egresos_total']);
        $this->assertEquals(2500.00, $groupData['ganancia_efectiva']);
        $this->assertEquals(1500.00, $groupData['perdida_efectiva']);
        $this->assertEquals(1000.00, $groupData['balance_neto']);
        $this->assertEquals(63, $groupData['pct_ingreso']);
    }
}
