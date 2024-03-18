<?php

namespace App\Models;

use App\Models\Scopes\TaskStateScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "title"];

    protected static function booted()
    {
        static::addGlobalScope(new TaskStateScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itemTasks(): HasMany
    {
        return $this->hasMany(ItemTask::class);
    }
}
