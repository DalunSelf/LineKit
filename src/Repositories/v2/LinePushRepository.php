<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LinePushRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LinePush $linePush)
    {
        $this->model = $linePush;
    }
}
