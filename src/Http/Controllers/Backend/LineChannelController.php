<?php

namespace Ryan\LineKit\Http\Controllers\Backend;

use App\Services\Sdk\LineBotService;
use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineChannelService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineChannelController extends Controller
{
    protected $lineChannelService;
    protected $lineBotService;

    public function __construct(LineChannelService $lineChannelService, LineBotService $lineBotService)
    {
        $this->lineChannelService = $lineChannelService;
        $this->lineBotService = $lineBotService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineChannel', 'read', null]);

        $list = $this->lineChannelService->getList($request->all());
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
        $this->authorize('use-controller', ['LineChannel', 'add', null]);

        try {
            DB::beginTransaction();
            if (!$request->Channelaccesstoken) {
                $createChannelAccessToken = $this->lineBotService->createChannelAccessToken($request);
                $request->merge(['Channelaccesstoken' => $createChannelAccessToken['access_token']]);
            }
            $lineChannel = $this->lineChannelService->firstOrNew([
                'ChannelID'             => $request['ChannelID'],
                'Channelsecret'         => $request['Channelsecret'],
                'organization_id'       => $request['organization']['id'],
                'Channelaccesstoken'    => $request['Channelaccesstoken'],
            ]);
            $botInfo = $this->lineBotService->getBotInfo($lineChannel);
            $lineChannel->fill([
                'userId'        => $botInfo['userId'],
                'basicId'       => $botInfo['basicId'],
                'displayName'   => $botInfo['displayName'],
                'pictureUrl'    => $botInfo['pictureUrl'] ?? null,
                'endpoint'      => config('app.url') . route('webhook.url', ['channelId' => $lineChannel->ChannelID], false),
            ])->save();
            $setWebhookEndpoint = $this->lineBotService->setWebhookEndpoint($lineChannel);

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
    public function show($id)
    {
        $this->authorize('use-controller', ['LineChannel', 'read', null]);

        try {
            $lineChannel = $this->lineChannelService->getSingleData($id);

            $data = $lineChannel->toArray();
            $data['roles'] = $lineChannel->roles->toArray();
            $data['acls'] = $lineChannel->ability->pluck('id')->toArray();
            return $this->success($data);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            return $this->badRequest($e->getMessage() ?: '請聯絡管理員');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('use-controller', ['LineChannel', 'update', null]);

        try {
            DB::beginTransaction();
            $lineChannel = $this->lineChannelService->update($id, $request->all());

            DB::commit();
            return $this->success('更新成功');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest($e->getMessage() ?: '請聯絡管理員');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('use-controller', ['LineChannel', 'delete', null]);

        try {
            DB::beginTransaction();
            $lineChannel = $this->lineChannelService->destroy($id);

            DB::commit();
            return $this->success('刪除成功');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest($e->getMessage() ?: '請聯絡管理員');
        }
    }
}
