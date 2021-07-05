<?php

namespace Ryan\LineKit\Models;

use App\Models\Traits\CommonHasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineRichMenu extends Model
{
    use HasFactory;

    // 通用一對一關聯
    use CommonHasOne;

    protected $guarded = [];

    protected $casts = [
        'selected' => 'boolean',
        'size' => 'array',
        'areas' => 'array',
    ];
}
