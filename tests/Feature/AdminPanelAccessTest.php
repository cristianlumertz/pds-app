<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_comum_nao_acessa_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_acessa_modulos_principais_do_painel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $routes = [
            'admin.dashboard',
            'admin.products.index',
            'admin.categories.index',
            'admin.pedidos.index',
            'admin.payments.index',
            'admin.stock.index',
            'admin.stock-movements.index',
            'admin.coupons.index',
            'admin.users.index',
            'admin.reports.index',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }
}
