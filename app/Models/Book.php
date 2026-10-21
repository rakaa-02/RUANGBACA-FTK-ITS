<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['judul', 'no_induk', 'lokasi'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
                ->orWhere('no_induk', 'like', "%{$keyword}%")
                ->orWhere('lokasi', 'like', "%{$keyword}%");
        });
    }
}
