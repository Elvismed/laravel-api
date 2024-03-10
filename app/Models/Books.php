<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Books extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'url',
        'gender',
        'author',
       'reads',
       'author_reads',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
