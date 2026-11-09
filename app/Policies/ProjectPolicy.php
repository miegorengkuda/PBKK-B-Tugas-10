<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Tentukan apakah user dapat melihat detail proyek.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    /**
     * Tentukan apakah user dapat memperbarui data proyek.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    /**
     * Tentukan apakah user dapat menghapus proyek (soft delete).
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    /**
     * Tentukan apakah user dapat memulihkan proyek (restore).
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
