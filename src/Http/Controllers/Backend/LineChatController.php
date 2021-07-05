<?php

namespace Ryan\LineKit\Http\Controllers\Backend;

use App\Services\v2\OrganizationService;
use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineChannelService;
use Ryan\LineKit\Services\v2\LineChatService;
use Ryan\LineKit\Services\v2\LineMemberService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineChatController extends Controller
{
    protected $lineChatService;
    protected $lineMemberService;
    protected $lineChannelService;
    protected $organizationService;

    public function __construct(LineChatService $lineChatService, LineMemberService $lineMemberService, LineChannelService $lineChannelService, OrganizationService $organizationService)
    {
        $this->lineChatService = $lineChatService;
        $this->lineMemberService = $lineMemberService;
        $this->lineChannelService = $lineChannelService;
        $this->organizationService = $organizationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineChat', 'read', $request->input('organization.id')]);

        // 讀取組織資訊
        $organization = $this->organizationService->findByValidOrganizationID($request->input('organization.id'));
        if (!$organization) {
            return $this->badRequest('讀取組織發生錯誤');
        }
        $organization->load('line_channel');
        $lineChannel = $organization->line_channel;

        // TODO: 他有讀到資料的只是 IDE 找不到 func 類型會噴紅
        if (!$lineChannel) {
            return $this->badRequest('不存在的官方帳害');
        }
        // 所有訊息
        $list['chatsContacts'] = $this->lineMemberService->getByChatLast($lineChannel->id);
        // 當前機器人資訊
        $list['profileUser'] = $lineChannel;
        return $this->success($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('use-controller', ['LineChat', 'add', $request->input('organization.id')]);

        // 讀取組織資訊
        $organization = $this->organizationService->findByValidOrganizationID($request->input('organization.id'));
        if (!$organization) {
            return $this->badRequest('讀取組織發生錯誤');
        }
        $organization->load('line_channel');
        $lineChannel = $organization->line_channel;

        // TODO: 他有讀到資料的只是 IDE 找不到 func 類型會噴紅
        if (!$lineChannel) {
            return $this->badRequest('不存在的官方帳害');
        }
        try {
            DB::beginTransaction();
            $lineChat = $this->lineChatService->createChatMessage(
                $request->user(),
                $lineChannel,
                $request->input('message'),
                'backend'
            );

            // $lineChat = $this->lineChatService->create($request->all());

            DB::commit();
            return $this->success('新增成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest($e->getMessage() ?: '請聯絡管理員');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->authorize('use-controller', ['LineChat', 'read', $request->input('organization.id')]);

        try {
            // 讀取組織資訊
            $organization = $this->organizationService->findByValidOrganizationID($request->input('organization_id'));
            if (!$organization) {
                return $this->badRequest('讀取組織發生錯誤');
            }
            $organization->load('line_channel');
            $lineChannel = $organization->line_channel;

            // TODO: 他有讀到資料的只是 IDE 找不到 func 類型會噴紅
            if (!$lineChannel) {
                return $this->badRequest('不存在的官方帳害');
            }
            $lineMember = $this->lineMemberService->firstByChannelId($lineChannel['id'], $id);

            $lineChat = $this->lineChatService->getByMemberChatAll($id);

            $data = $lineMember;
            $data['chat'] = $lineChat;
            return $this->success($data);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            return $this->badRequest($e->getMessage() ?: '請聯絡管理員');
        }
    }
}
