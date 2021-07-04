<?php

namespace Ryan\LineKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineChannel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function organization()
    {
        return $this->hasOne(LineChannel::class, 'id', 'organization_id');
    }
}
