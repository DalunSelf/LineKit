<?php

namespace Ryan\LineKit\Repositories\v2;

use Ryan\LineKit\Repositories\Repository;

class LineMemberRepository extends Repository
{
    public function __construct(\Ryan\LineKit\Models\LineMember $lineMember)
    {
        $this->model = $lineMember;
    }

    public function firstByChannelIdAndUserId($line_channel_id, $line_member_userId, $columns = ['*'])
    {
        return $this->model->where('line_channel_id', $line_channel_id)->where('userId', $line_member_userId)->first($columns);
    }

    public function firstByChannelId($line_channel_id, $line_member_id, $columns = ['*'])
    {
        return $this->model->where('line_channel_id', $line_channel_id)->where('id', $line_member_id)->first($columns);
    }

    public function getByWithChat($column, $value, $columns = ['*'])
    {
        return $this->model->with('chat')->where($column, $value)->get($columns);
    }

    /**
     * 獲得指定標籤所被貼上的會員名單
     *
     * @param  mixed $line_channel_id
     * @param  mixed $tag_ids
     * @param  mixed $columns
     * @return mixed
     */
    public function getByChannelIdAndTagIds($line_channel_id, array $tag_ids, $columns = ['*'])
    {
        return $this->model->where('line_channel_id', $line_channel_id)
            ->whereHas('tags', function ($query) use ($tag_ids) {
                $query->whereIn('line_tags.id', $tag_ids);
            })
            ->get($columns);
    }
}
