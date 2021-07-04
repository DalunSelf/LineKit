<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LineFlexTemplateRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LineFlexTemplate $lineFlexTemplate)
    {
        $this->model = $lineFlexTemplate;
    }
}
