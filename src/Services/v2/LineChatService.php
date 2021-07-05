<?php

namespace Ryan\LineKit\Services\v2;

use App\Events\LineChatPusher;
use App\Repositories\v2\UserRepository;
use Ryan\LineKit\Repositories\v2\LineChatRepository;
use Ryan\LineKit\Repositories\v2\LineMemberRepository;
use Ryan\LineKit\Services\Service;

class LineChatService extends Service
{
    protected $lineChatRepository;
    protected $lineMemberRepository;
    protected $userRepository;

    public function __construct(LineChatRepository $lineChatRepository, LineMemberRepository $lineMemberRepository, UserRepository $userRepository)
    {
        parent::__construct($lineChatRepository);

        $this->lineChatRepository = $lineChatRepository;
        $this->lineMemberRepository = $lineMemberRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * 讀取會員的對話資料
     *
     * @param  mixed $attributes
     * @return void
     */
    public function getByMemberChatAll($line_member_id)
    {
        // 開始撈當前會員的對話資料
        $paginator = $this->lineChatRepository->paginateByMemberWithSender('line_member_id', $line_member_id, 20);
        return array_reverse($paginator->items());
    }

    /**
     * 從會員創建的對話
     *
     * @param  mixed $line_member
     * @param  mixed $textMessage
     * @return void
     */
    public function createChatMessage($model, $lineChannel, $textMessage, $direction)
    {
        $newMessageData = $model->chat()->create([
            'line_member_id' => $model['id'],
            'direction' => $direction,
            'from' => 'line',
            'message' => $textMessage,
            'unseenMsgs' => 0,
            'time' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $newMessageData->load('sender');
        event(new LineChatPusher($newMessageData, $lineChannel, $model));
    }
}
