<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LineTagRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LineTag $lineTag)
    {
        $this->model = $lineTag;
    }

    // TODO: 還沒過濾 line_channel_id
    public function getByIdsWithMember(array $tag_ids)
    {
        return $this->model->with('members')->whereIn('id', $tag_ids)->get();
    }
}
