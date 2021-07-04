<?php

namespace Ryan\LineKit\Services\v2;

use App\Events\LineChatPusher;
use Ryan\LineKit\Repositories\v2\LineMemberRepository;
use Ryan\LineKit\Repositories\v2\OrganizationRepository;
use Ryan\LineKit\Services\Service;

class LineMemberService extends Service
{
    protected $lineMemberRepository;
    protected $organizationRepository;

    public function __construct(LineMemberRepository $lineMemberRepository, OrganizationRepository $organizationRepository)
    {
        parent::__construct($lineMemberRepository);

        $this->lineMemberRepository = $lineMemberRepository;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * 獲得官方帳號頻道的會員資料
     *
     * @param  mixed $line_channel_id
     * @param  mixed $line_member_id
     * @return void
     */
    public function firstByChannelIdAndUserId($line_channel_id, $line_member_userId)
    {
        return $this->lineMemberRepository->firstByChannelIdAndUserId($line_channel_id, $line_member_userId);
    }

    public function firstByChannelId($line_channel_id, $line_member_id)
    {
        return $this->lineMemberRepository->firstByChannelId($line_channel_id, $line_member_id);
    }

    /**
     * 讀取會員的對話資料
     *
     * @param  mixed $attributes
     * @return void
     */
    public function getByChatLast($line_channel_id)
    {
        // 開始撈會員的對話資料
        return $this->lineMemberRepository->getByWithChat('line_channel_id', $line_channel_id)
            ->transform(function ($item) {
                // 最後訊息
                $last = $item['chat']->last();
                // 未讀訊息
                $unseenMsgCount = count($item['chat']->where('unseenMsgs', 0));
                unset($item['chat']);
                $item['lastMessage'] = $last ? $last->toArray() : null;
                $item['unseenMsgs'] = $unseenMsgCount;
                return $item;
            })->sortByDesc('lastMessage.time')->values()->all();
    }
}
