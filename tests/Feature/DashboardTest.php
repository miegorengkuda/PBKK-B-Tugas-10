<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest tidak dapat mengakses dashboard dan diarahkan ke login.
     */
    public function test_guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * User terautentikasi dapat mengakses dashboard dan melihat overview statistik.
     */
    public function test_authenticated_user_can_access_dashboard_and_see_statistics(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso']);
        Project::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Proyek AI Pertama',
            'status'  => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
        $response->assertSee('Total Pengguna Terdaftar');
        $response->assertSee('Total Proyek Akademik');
        $response->assertSee('Proyek Aktif (Running)');
        $response->assertSee('Proyek AI Pertama');
        $response->assertSee('Budi Santoso');
        $response->assertSee('adminlte-inter.css');
        $response->assertSee('Inter');
    }

    /**
     * Dashboard menghitung query agregat database secara akurat.
     */
    public function test_dashboard_displays_correct_database_aggregates(): void
    {
        // Buat 2 user tambahan
        User::factory()->count(2)->create();

        $user = User::factory()->create();

        // Buat berbagai status proyek
        Project::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        Project::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        Project::factory()->create(['user_id' => $user->id, 'status' => 'draft']);
        Project::factory()->create(['user_id' => $user->id, 'status' => 'archived']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalUsers', 3);
        $response->assertViewHas('totalProjects', 4);
        $response->assertViewHas('activeProjects', 2);
        $response->assertViewHas('draftProjects', 1);
        $response->assertViewHas('archivedProjects', 1);
        $response->assertViewHas('myTotalProjects', 4);
        $response->assertViewHas('myActiveProjects', 2);
    }

    /**
     * Dashboard menampilkan proyek terbaru milik pengguna.
     */
    public function test_dashboard_displays_user_recent_projects(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myProject = Project::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Riset Agentic AI ITS',
        ]);

        $otherProject = Project::factory()->create([
            'user_id' => $otherUser->id,
            'title'   => 'Riset Orang Lain',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Riset Agentic AI ITS');
        $response->assertDontSee('Riset Orang Lain');
    }
}
