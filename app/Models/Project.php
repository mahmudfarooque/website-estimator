<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- ADD THIS

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'current_step'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_project');
    }

    // START: ADD THIS NEW RELATIONSHIP
    public function uploads(): HasMany
    {
        return $this->hasMany(ProjectUpload::class);
    }
    // END: ADD THIS NEW RELATIONSHIP
}