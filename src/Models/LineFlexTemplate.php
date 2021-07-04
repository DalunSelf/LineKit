<?php

namespace Ryan\LineKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineFlexTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'flex' => 'array',
    ];
}
