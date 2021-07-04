<?php

namespace Ryan\LineKit\Services\v2;

use Ryan\LineKit\Repositories\v2\LineKeywordRepository;
use Ryan\LineKit\Repositories\v2\LineTagRepository;
use Ryan\LineKit\Services\Service;

class LineKeywordService extends Service
{
    protected $lineKeywordRepository;
    protected $lineTagRepository;

    public function __construct(LineKeywordRepository $lineKeywordRepository, LineTagRepository $lineTagRepository)
    {
        parent::__construct($lineKeywordRepository);

        $this->lineKeywordRepository = $lineKeywordRepository;
        $this->lineTagRepository = $lineTagRepository;
    }

    /**
     * 創建 Line 官方帳號
     *
     * @param  mixed $attributes
     * @return void
     */
    public function createKeywordData(array $attributes)
    {
        $lineKeyword = $this->lineKeywordRepository->create([
            'line_channel_id'   => $attributes['line_channel']['id'],
            'type'              => $attributes['type'],
            'keys'              => $attributes['type'] == 100 ? [] : $attributes['keys'],
            'tags'              => $attributes['tags'] ?? null,
        ]);
        if (isset($attributes['samples'])) {
            $this->proccesRelationWithRequest($lineKeyword->samples(), $attributes['samples']);
        }
        return $lineKeyword;
    }

    /**
     * 獲得關鍵字單筆 Data
     *
     * @param  mixed $id
     * @return Illuminate\Support\Collection
     */
    public function getKeywordSingleData($id)
    {
        $lineKeyword = $this->lineKeywordRepository->find($id);

        $data = $lineKeyword->toArray();
        $data['samples'] = $lineKeyword->samples->toArray();
        return collect($data);
    }

    /**
     * 更新關鍵字 Data
     *
     * @param  mixed $id
     * @param  mixed $attributes
     * @return bool
     */
    public function updateKeywordData($id, array $attributes)
    {
        $lineKeyword = $this->lineKeywordRepository->find($id);
        if (isset($attributes['samples'])) {
            $this->proccesRelationWithRequest($lineKeyword->samples(), $attributes['samples']);
        }

        return $this->lineKeywordRepository->update($id, [
            'type'              => $attributes['type'],
            'keys'              => $attributes['type'] == 100 ? [] : $attributes['keys'],
            'tags'              => $attributes['tags'],
        ]);
    }

    /**
     * 同步更新關鍵字的標籤行為
     *
     * @return void
     */
    public function syncUpdateKeywordTagBehavior($line_member, $line_keyword_tags)
    {
        $line_tag_ids = collect([]);
        foreach ($line_keyword_tags as $tag) {
            $line_tag_ids[] = $this->lineTagRepository->firstOrCreate([
                'line_channel_id' => $line_member['line_channel_id'], 'name' => $tag
            ])->id;
        }
        // 先取得會員所有的標籤 Ids
        $member_tag_ids = $line_member->tags->pluck('id');
        // 合併並且過濾重複 Id
        $tag_ids = array_unique($line_tag_ids->merge($member_tag_ids)->all());
        // 儲存
        $line_member->tags()->sync($tag_ids);
    }
}
