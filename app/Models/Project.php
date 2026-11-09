<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'tech_stack',
        'started_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
