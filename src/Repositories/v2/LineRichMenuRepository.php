<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LineRichMenuRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LineRichMenu $lineRichMenu)
    {
        $this->model = $lineRichMenu;
    }
}
