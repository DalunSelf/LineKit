<?php

namespace Ryan\LineKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineChat extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sender()
    {
        return $this->morphTo();
    }
}
