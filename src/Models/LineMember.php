<?php

namespace Ryan\LineKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineMember extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chat()
    {
        return $this->morphMany(LineChat::class, 'sender');
    }

    public function tags()
    {
        return $this->belongsToMany(LineTag::class, 'line_tag_has_member', 'line_member_id', 'line_tag_id');
    }
}
