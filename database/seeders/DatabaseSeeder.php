<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * This seeder is idempotent: running it multiple times will NOT create
     * duplicate demo users or duplicate projects.
     */
    public function run(): void
    {
        // ── Demo user (firstOrCreate — safe to run repeatedly) ──────────────
        $demo = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('password123'),
            ]
        );

        // ── Sample projects for demo user (only if they don't exist yet) ────
        if ($demo->projects()->count() === 0) {
            $projects = [
                [
                    'title'       => 'AI Gmail Inbox Manager',
                    'description' => 'Agent AI yang menganalisis dan mengelompokkan email serta memberikan rekomendasi pengelolaan dengan persetujuan pengguna.',
                    'category'    => 'Agentic AI',
                    'status'      => 'active',
                    'tech_stack'  => 'Python, LangGraph, Gemini, Gmail API, Google OAuth 2.0',
                    'started_at'  => '2024-09-01',
                ],
                [
                    'title'       => 'Sistem Informasi Akademik ITS',
                    'description' => 'Aplikasi web untuk memonitor data akademik mahasiswa secara real-time.',
                    'category'    => 'Web Development',
                    'status'      => 'draft',
                    'tech_stack'  => 'Laravel, Vue.js, PostgreSQL',
                    'started_at'  => '2024-08-15',
                ],
                [
                    'title'       => 'Analisis Sentimen Berita Akademik',
                    'description' => 'Penelitian NLP untuk menganalisis sentimen berita seputar dunia akademik Indonesia.',
                    'category'    => 'Research',
                    'status'      => 'archived',
                    'tech_stack'  => 'Python, Hugging Face Transformers, FastAPI',
                    'started_at'  => '2024-03-01',
                ],
            ];

            foreach ($projects as $data) {
                $demo->projects()->create($data);
            }
        }
    }
}
