<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LineKeywordRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LineKeyword $lineKeyword)
    {
        $this->model = $lineKeyword;
    }
}
