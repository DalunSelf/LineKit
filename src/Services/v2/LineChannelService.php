<?php

namespace Ryan\LineKit\Services\v2;

use App\Services\Sdk\LineBotService as SdkLineBotService;
use Ryan\LineKit\Repositories\v2\LineChannelRepository;
use Ryan\LineKit\Services\Sdk\LineBotService;
use Ryan\LineKit\Services\Service;

class LineChannelService extends Service
{
    protected $lineChannelRepository;
    protected $lineBotService;

    public function __construct(LineChannelRepository $lineChannelRepository, SdkLineBotService $lineBotService)
    {
        parent::__construct($lineChannelRepository);

        $this->lineChannelRepository = $lineChannelRepository;
        $this->lineBotService = $lineBotService;
    }

    /**
     * 獲得單筆有效的資料
     *
     * @param  mixed $id
     * @return void
     */
    public function findByValidChannelID($channelID)
    {
        return $this->lineChannelRepository->firstBy('ChannelID', $channelID, ['id', 'Channelsecret', 'Channelaccesstoken']);
    }

    /**
     * 創建 Line 官方帳號
     *
     * @param  mixed $attributes
     * @return void
     */
    public function createLineChannel(array $attributes)
    {
        if (empty($attributes['Channelaccesstoken'])) {
            $createChannelAccessToken = $this->lineBotService->createChannelAccessToken($attributes);
            $attributes['Channelaccesstoken'] = $createChannelAccessToken['access_token'];
        }
        // 獲取機器人資訊
        $botInfo = $this->lineBotService->getBotInfo($attributes);
        // 重新設定 Webhook Url
        $attributes['endpoint'] = config('app.url') . route('webhook.url', ['channelId' => $attributes['ChannelID']], false);
        $setWebhookEndpoint = $this->lineBotService->setWebhookEndpoint($attributes);

        // 更新資料
        $lineChannel = $this->lineChannelRepository->create([
            'organization_id'       => $attributes['organization_id'],
            'ChannelID'             => $attributes['ChannelID'],
            'Channelsecret'         => $attributes['Channelsecret'],
            'Channelaccesstoken'    => $attributes['Channelaccesstoken'],
            'userId'                => $botInfo['userId'],
            'basicId'               => $botInfo['basicId'],
            'displayName'           => $botInfo['displayName'],
            'pictureUrl'            => $botInfo['pictureUrl'] ?? null,
            'endpoint'              => $attributes['endpoint'],
        ]);

        return $lineChannel;
    }

    /**
     * 更新 Line 官方帳號
     *
     * @param  mixed $id
     * @param  mixed $attributes
     * @return void
     */
    public function updateLineChannel($id, array $attributes)
    {
        if (empty($attributes['Channelaccesstoken'])) {
            $createChannelAccessToken = $this->lineBotService->createChannelAccessToken($attributes);
            $attributes['Channelaccesstoken'] = $createChannelAccessToken['access_token'];
        }
        // 獲取機器人資訊
        $botInfo = $this->lineBotService->getBotInfo($attributes);
        // 重新設定 Webhook Url
        $attributes['endpoint'] = config('app.url') . route('webhook.url', ['channelId' => $attributes['ChannelID']], false);
        $setWebhookEndpoint = $this->lineBotService->setWebhookEndpoint($attributes);

        // 更新資料
        $lineChannel = $this->lineChannelRepository->update($id, [
            'organization_id'       => $attributes['organization_id'],
            'ChannelID'             => $attributes['ChannelID'],
            'Channelsecret'         => $attributes['Channelsecret'],
            'Channelaccesstoken'    => $attributes['Channelaccesstoken'],
            'userId'                => $botInfo['userId'],
            'basicId'               => $botInfo['basicId'],
            'displayName'           => $botInfo['displayName'],
            'pictureUrl'            => $botInfo['pictureUrl'] ?? null,
            'endpoint'              => $attributes['endpoint'],
        ]);

        return $lineChannel;
    }
}
