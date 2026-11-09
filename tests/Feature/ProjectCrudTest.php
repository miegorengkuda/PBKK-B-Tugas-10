<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Guest yang mengakses halaman proyek harus diarahkan ke halaman login.
     */
    public function test_guest_is_redirected_to_login_when_accessing_projects(): void
    {
        $response = $this->get(route('projects.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 2. User hanya dapat melihat proyek miliknya sendiri di halaman index.
     */
    public function test_user_can_view_only_their_own_projects_on_index(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $project1 = Project::factory()->create(['user_id' => $user1->id, 'title' => 'User 1 Project']);
        $project2 = Project::factory()->create(['user_id' => $user2->id, 'title' => 'User 2 Project']);

        $response = $this->actingAs($user1)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee('User 1 Project');
        $response->assertDontSee('User 2 Project');
    }

    /**
     * 3. User dapat membuat proyek baru dan user_id terasosiasi otomatis (anti-spoofing).
     */
    public function test_user_can_create_project_with_valid_data(): void
    {
        $user = User::factory()->create();

        $payload = [
            'title'       => 'Sistem Multi-Agent Baru',
            'description' => 'Eksperimen agentic AI untuk klasifikasi berkas.',
            'category'    => 'Agentic AI',
            'status'      => 'active',
            'tech_stack'  => 'Python, LangGraph, Gemini',
            'started_at'  => '2026-10-01',
            'user_id'     => 999999, // Percobaan spoofing ID lain
        ];

        $response = $this->actingAs($user)->post(route('projects.store'), $payload);

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success');

        // Pastikan user_id yang tersimpan adalah ID user yang sedang login, bukan 999999
        $this->assertDatabaseHas('projects', [
            'title'   => 'Sistem Multi-Agent Baru',
            'user_id' => $user->id,
            'status'  => 'active',
        ]);
    }

    /**
     * 4. Validasi StoreProjectRequest menolak data tanpa judul atau status tidak valid.
     */
    public function test_create_project_validation_via_store_project_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.store'), [
            'title'  => '',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
    }

    /**
     * 5. User dapat melihat detail proyek miliknya.
     */
    public function test_user_can_view_own_project_detail(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertSee($project->title);
    }

    /**
     * 6. User TIDAK DAPAT melihat detail proyek milik user lain (Policy melempar 403 Forbidden).
     */
    public function test_user_cannot_view_other_users_project_returns_403(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $project2 = Project::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('projects.show', $project2));

        $response->assertStatus(403);
    }

    /**
     * 7. User dapat memperbarui proyek miliknya.
     */
    public function test_user_can_update_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Judul Lama',
            'status'  => 'draft',
        ]);

        $response = $this->actingAs($user)->put(route('projects.update', $project), [
            'title'       => 'Judul Baru yang Diperbarui',
            'status'      => 'active',
            'category'    => 'Web App',
            'tech_stack'  => 'Laravel, Tailwind',
            'description' => 'Deskripsi baru.',
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'id'     => $project->id,
            'title'  => 'Judul Baru yang Diperbarui',
            'status' => 'active',
        ]);
    }

    /**
     * 8. User TIDAK DAPAT memperbarui proyek milik user lain (Policy melempar 403 Forbidden).
     */
    public function test_user_cannot_update_other_users_project_returns_403(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $project2 = Project::factory()->create([
            'user_id' => $user2->id,
            'title'   => 'Proyek Asli User 2',
        ]);

        $response = $this->actingAs($user1)->put(route('projects.update', $project2), [
            'title'  => 'Usaha Perubahan Ilegal',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('projects', [
            'id'    => $project2->id,
            'title' => 'Proyek Asli User 2',
        ]);
    }

    /**
     * 9. User dapat melakukan Soft Delete pada proyek miliknya.
     */
    public function test_user_can_soft_delete_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success');

        // Pastikan terhapus lunak (soft deleted)
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    /**
     * 10. User TIDAK DAPAT menghapus proyek milik user lain (403 Forbidden).
     */
    public function test_user_cannot_delete_other_users_project_returns_403(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $project2 = Project::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->delete(route('projects.destroy', $project2));

        $response->assertStatus(403);
        $this->assertNotSoftDeleted('projects', ['id' => $project2->id]);
    }

    /**
     * 11. User dapat memulihkan (Restore) proyek yang terhapus lunak miliknya.
     */
    public function test_user_can_restore_own_trashed_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $project->delete(); // Soft delete

        $this->assertSoftDeleted('projects', ['id' => $project->id]);

        $response = $this->actingAs($user)->patch(route('projects.restore', $project->id));

        $response->assertRedirect(route('projects.trash'));
        $response->assertSessionHas('success');

        // Pastikan tidak lagi soft deleted
        $this->assertNotSoftDeleted('projects', ['id' => $project->id]);
    }

    /**
     * 12. User TIDAK DAPAT memulihkan (Restore) proyek milik user lain (404/403).
     */
    public function test_user_cannot_restore_other_users_trashed_project(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $project2 = Project::factory()->create(['user_id' => $user2->id]);
        $project2->delete();

        $response = $this->actingAs($user1)->patch(route('projects.restore', $project2->id));

        // Karena dibatasi pada $request->user()->projects()->onlyTrashed()->findOrFail($id),
        // pencarian ID milik orang lain akan menghasilkan 404 Not Found
        $response->assertStatus(404);
        $this->assertSoftDeleted('projects', ['id' => $project2->id]);
    }

    /**
     * 13. User dapat melakukan Bulk Delete proyek miliknya via checkbox.
     */
    public function test_user_can_bulk_delete_multiple_projects_via_bulk_request(): void
    {
        $user = User::factory()->create();

        $p1 = Project::factory()->create(['user_id' => $user->id]);
        $p2 = Project::factory()->create(['user_id' => $user->id]);
        $p3 = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('projects.bulk-destroy'), [
            'ids' => [$p1->id, $p2->id],
        ]);

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('projects', ['id' => $p1->id]);
        $this->assertSoftDeleted('projects', ['id' => $p2->id]);
        $this->assertNotSoftDeleted('projects', ['id' => $p3->id]);
    }

    /**
     * 14. Bulk Delete menolak request jika array ids kosong via BulkDestroyProjectRequest.
     */
    public function test_bulk_delete_validates_ids_array_via_form_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('projects.bulk-destroy'), [
            'ids' => [],
        ]);

        $response->assertSessionHasErrors(['ids']);
    }

    /**
     * 15. Bulk Delete TIDAK AKAN menghapus proyek milik user lain meskipun ID-nya disisipkan.
     */
    public function test_bulk_delete_cannot_delete_other_users_projects(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $pUser1 = Project::factory()->create(['user_id' => $user1->id]);
        $pUser2 = Project::factory()->create(['user_id' => $user2->id]);

        // User 1 mencoba mengirim ID miliknya dan ID milik User 2
        $response = $this->actingAs($user1)->delete(route('projects.bulk-destroy'), [
            'ids' => [$pUser1->id, $pUser2->id],
        ]);

        $response->assertRedirect(route('projects.index'));

        // Proyek User 1 terhapus, namun proyek User 2 tetap utuh
        $this->assertSoftDeleted('projects', ['id' => $pUser1->id]);
        $this->assertNotSoftDeleted('projects', ['id' => $pUser2->id]);
    }
}
