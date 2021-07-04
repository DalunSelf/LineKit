<?php

namespace Ryan\LineKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineKeyword extends Model
{
    use HasFactory;

    // 通用一對一關聯
    use Traits\CommonHasOne;

    protected $guarded = [];

    protected $casts = [
        'keys'      => 'array',
        'tags'      => 'array',
        'status'    => 'boolean',
    ];

    public function samples()
    {
        return $this->hasMany(LineKeywordSample::class, 'line_keyword_id', 'id')->orderBy('order');
    }
}
