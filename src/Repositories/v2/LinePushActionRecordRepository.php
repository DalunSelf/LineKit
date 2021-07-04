<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LinePushActionRecordRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LinePushActionRecord $linePushActionRecord)
    {
        $this->model = $linePushActionRecord;
    }
}
