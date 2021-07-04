<?php

namespace Ryan\LineKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinePushSample extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'original_value' => 'array',
        'parameter_value' => 'array',
    ];

    public function action_records()
    {
        return $this->hasMany(LinePushActionRecord::class, 'line_push_sample_id', 'id');
    }
}
