<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LineChatRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LineChat $lineChat)
    {
        $this->model = $lineChat;
    }

    public function paginateByMemberWithSender($column, $value, $perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->model->with('sender')->where($column, $value)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
