<?php

namespace Ryan\LineKit\Repositories\v2;

use App\Repositories\Traits\PaginateWithPermissionAndRole;
use Ryan\LineKit\Repositories\Repository;

class LineChannelRepository extends Repository
{
    use PaginateWithPermissionAndRole;

    public function __construct(\Ryan\LineKit\Models\LineChannel $lineChannel)
    {
        $this->model = $lineChannel;
    }
}
