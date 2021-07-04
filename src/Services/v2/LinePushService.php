<?php

namespace Ryan\LineKit\Services\v2;

use Ryan\LineKit\Repositories\v2\LineMemberRepository;
use Ryan\LineKit\Repositories\v2\LinePushRepository;
use Ryan\LineKit\Repositories\v2\LineTagRepository;
use Ryan\LineKit\Services\Sdk\LineBotService;
use Ryan\LineKit\Services\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class LinePushService extends Service
{
    protected $linePushRepository;
    protected $lineMemberRepository;
    protected $lineTagRepository;

    public function __construct(LinePushRepository $linePushRepository, LineMemberRepository $lineMemberRepository, LineTagRepository $lineTagRepository)
    {
        parent::__construct($linePushRepository);

        $this->linePushRepository = $linePushRepository;
        $this->lineMemberRepository = $lineMemberRepository;
        $this->lineTagRepository = $lineTagRepository;
    }

    /**
     * createPushData
     *
     * @param  mixed $attributes
     * @return void
     */
    public function createPushData(array $attributes)
    {
        $linePush = $this->linePushRepository->create([
            'line_channel_id'   => $attributes['line_channel']['id'],
            'send_target'       => $attributes['send_target'],
            'send_tags'         => $attributes['send_tags'] ?? null,
            'send_type'         => $attributes['send_type'],
            'send_time'         => $attributes['send_time'] ?? null,
        ]);
        if (isset($attributes['samples'])) {
            $samples = collect($attributes['samples'])->transform(function ($item) {
                $item['original_value'] = $item['parameter_value'];
                return $item;
            });
            $this->proccesRelationWithRequest($linePush->samples(), $samples->toArray());
        }
        $this->checkMessagePushSync($linePush, 0); // 0 為立即發送
        return $linePush;
    }

    /**
     * getPushSingleData
     *
     * @param  mixed $id
     * @return void
     */
    public function getPushSingleData($id)
    {
        $linePush = $this->linePushRepository->find($id);

        $data = $linePush->toArray();
        $data['samples'] = $linePush->samples->toArray();
        return collect($data);
    }

    /**
     * updatePushData
     *
     * @param  mixed $id
     * @param  mixed $attributes
     * @return void
     */
    public function updatePushData($id, array $attributes)
    {
        $linePush = $this->linePushRepository->find($id);
        if (isset($attributes['samples'])) {
            $this->proccesRelationWithRequest($linePush->samples(), $attributes['samples']);
            // $this->refreshFormat($linePush->id, $linePush->samples->toArray());
        }

        $result = $this->linePushRepository->update($id, [
            'send_target'       => $attributes['send_target'],
            'send_tags'         => $attributes['send_tags'] ?? null,
            'send_type'         => $attributes['send_type'],
            'send_time'         => $attributes['send_time'] ?? null,
        ]);

        $this->checkMessagePushSync($linePush, 0); // 0 為立即發送
        return $result;
    }

    protected function refreshFormat($linePushId, array $samples)
    {
        // 紀錄動作事件
        $recordActionEvents = [];

        // 重新寫入 sample 樣板內容的格式
        $re_update = $samples;

        foreach ($samples as $sampleIndex => $sample) {
            if ($sample['type'] !== 'module') {
                break;
            }

            $carousels = $sample['parameter_value']['contents']['contents'];
            foreach ($carousels as $carouselIndex => $carousel) {
                // 用迴圈處理 Header Hero Body Footer 比較快
                $contents = ['header', 'hero', 'body', 'footer'];
                foreach ($contents as $content) {
                    if (isset($carousel[$content])) {
                        Arr::set(
                            $re_update,
                            $sampleIndex . 'parameter_value.contents.contents.' . $carouselIndex . '.' . $content,
                            $this->processActionItems($carousel[$content], $content, $sample['id'], $linePushId, $recordActionEvents)
                        );
                    }
                }
            }

            // $this->linePushService->proccesRelationWithRequest($sample->action_records(), $action_items);

            // // 更新重新更新的格式
            // $sample->update([
            //     'parameter_value' => $re_update['parameter_value']
            // ]);
        }
        // info($re_update);
        info($recordActionEvents);
    }

    protected function processActionItems($item, $flex_type, $linePushSampleId, $line_push_id, &$recordActionEvents)
    {
        if (isset($item['action'])) {
            $random_code = \Illuminate\Support\Str::random(30);
            $recordActionEvents[] = [
                // 'type' =>  $type,
                'line_push_id'          => $line_push_id,
                'line_push_sample_id'   => $linePushSampleId,
                'serial_code'           => $random_code,
                'display_text'          => $flex_type . '-' . $item['type'],
                'original_format'       => $item,
                'click_total_count'     => 0,
            ];
            // 轉換格式
            if (isset($item['action']['type']) && $item['action']['type'] === 'message') {
                $item['action']['type'] = 'postback';
                $item['action']['data'] = json_encode(['type' => 'LINE_PUSH', 'serial_code' => $random_code]);
                $item['action']['displayText'] = $item['action']['text'];
                unset($item['action']['text']);
            }
            if (isset($item['action']['type']) && $item['action']['type'] === 'postback') {
                $item['action']['type'] = 'postback';
                $item['action']['data'] = json_encode(['type' => 'LINE_PUSH', 'serial_code' => $random_code]);
                $item['action']['displayText'] = $item['action']['displayText'];
            }
            if (isset($item['action']['type']) && $item['action']['type'] === 'uri') {
                $item['action']['uri'] = route('short.url', ['serial_code' => $random_code]);
            }
        }

        if (isset($item['contents'])) {
            foreach ($item['contents'] as $index => $content) {
                Arr::set(
                    $item,
                    'contents.' . $index,
                    $this->processActionItems($content, $flex_type, $linePushSampleId, $line_push_id, $recordActionEvents)
                );
            }
        }
        return $item;
    }

    /**
     * 檢查訊息推播是否需立即發送或預約發送
     *
     * @param  mixed $linePush      推播 Model
     * @param  mixed $send_type     推播類型: 0 立即 | 1 預約
     * @return void
     */
    public function checkMessagePushSync(Model $linePush, $send_type)
    {
        $linePush->refresh();
        // 判斷發送狀態
        if ($linePush->send_type === $send_type) {
            $lineMember = collect([]);
            if ($linePush->send_target === 0) {
                // 全體用戶
                $lineMember = $this->lineMemberRepository->getBy('line_channel_id', $linePush['line_channel_id']);
            } else if ($linePush->send_target === 1) {
                // 指定標籤
                $tag_ids = collect($linePush->send_tags)->pluck('id');
                $lineMember = $this->lineMemberRepository->getByChannelIdAndTagIds($linePush['line_channel_id'], $tag_ids->toArray());
            }
            // 指取得會員 userIds 集合準備進行推播
            $userIds = $lineMember->pluck('userId');
            if (count($userIds) > 500) {
                // 如果推播人數大於 500 先不做推播
                $linePush->error_message = '推播人數大於 500 人';
                $linePush->status = 2;
                $linePush->update();
                return false;
            }

            // 開始推播
            $multicast = (new LineBotService())->multicast(
                $linePush->line_channel,
                $userIds->toArray(),
                $linePush->samples->toArray()
            );

            // 更新狀態
            if (isset($multicast['message'])) { // 推播發生錯誤會有 message 訊息
                $linePush->error_message = $multicast['message'];
                $linePush->status = 2;
            } else {
                $linePush->status = 1;
            }
            $linePush->update();
        }
    }
}
