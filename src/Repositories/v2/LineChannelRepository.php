<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;
use Ryan\LineKit\Repositories\Traits\PaginateWithPermissionAndRole;

class LineChannelRepository extends Repository
{
    use PaginateWithPermissionAndRole;

    public function __construct(\Ryan\LineKit\Models\LineChannel $lineChannel)
    {
        $this->model = $lineChannel;
    }
}
