<?php

namespace Ryan\LineKit\Models;

use App\Models\Traits\CommonHasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinePush extends Model
{
    use HasFactory;

    // 通用一對一關聯
    use CommonHasOne;

    protected $guarded = [];

    protected $casts = [
        'send_tags' => 'array',
    ];

    public function samples()
    {
        return $this->hasMany(LinePushSample::class, 'line_push_id', 'id')->orderBy('order');
    }

    public function action_records()
    {
        return $this->hasMany(LinePushActionRecord::class, 'line_push_id', 'id');
    }
}
