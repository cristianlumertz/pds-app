<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_ao_acessar_perfil_e_redirecionado_para_login(): void
    {
        $this->get(route('profile.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_header_nao_renderiza_ajuda_ou_rastrear_pedido(): void
    {
        $this->get(route('store.home'))
            ->assertOk()
            ->assertDontSee('Ajuda')
            ->assertDontSee('Rastrear pedido')
            ->assertDontSee('Rastrear Pedido');
    }

    public function test_usuario_logado_ve_meu_perfil_no_header(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('store.home'))
            ->assertOk()
            ->assertSee('Meu perfil')
            ->assertDontSee('Ajuda')
            ->assertDontSee('Rastrear pedido');
    }

    public function test_admin_continua_vendo_botao_admin_no_header(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('store.home'))
            ->assertOk()
            ->assertSee('Meu perfil')
            ->assertSee('Admin');
    }
}
