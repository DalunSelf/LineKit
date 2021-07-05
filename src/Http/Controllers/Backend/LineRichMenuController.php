<?php

namespace Ryan\LineKit\Http\Controllers\Backend;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineRichMenuService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineRichMenuController extends Controller
{
    protected $lineRichMenuService;

    public function __construct(LineRichMenuService $lineRichMenuService)
    {
        $this->lineRichMenuService = $lineRichMenuService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineRichMenu', 'read', $request->input('organization.id')]);

        $list = $this->lineRichMenuService->getList($request->all());
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
        $this->authorize('use-controller', ['LineRichMenu', 'add', $request->input('organization.id')]);

        $request->validate(
            [
                'name'                          => 'required|string',
                'chatBarText'                   => 'required|string',
                'selected'                      => 'required|bool',
                'type'                          => 'required|string',
                'size'                          => 'required|array',
                'image'                         => 'required',
                // Areas 格式
                'areas.*.action.uri'            => 'required_if:areas.*.action.type,uri|string',
                'areas.*.action.text'           => 'required_if:areas.*.action.type,message|string',
                'areas.*.action.data'           => 'required_if:areas.*.action.type,postback|string',
            ],
            [
                // 'keys.required_unless'          => '關鍵字 不能留空。',
            ],
            [
                'name'                          => '選單名稱',
                'chatBarText'                   => '選單列顯示文字',
                'type'                          => '設定對象',
                'image'                         => '版面圖片',
                'start_at'                      => '啟用時間',
                'end_at'                        => '結束時間',
                'areas.*.action.uri'            => '動作版型 開啟網址',
                'areas.*.action.text'           => '動作版型 發送訊息',
                'areas.*.action.data'           => '動作版型 切換圖文選單',
            ]
        );

        try {
            DB::beginTransaction();
            $lineRichMenu = $this->lineRichMenuService->createRichMenuData($request->all());

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
        $this->authorize('use-controller', ['LineRichMenu', 'read', $request->input('organization.id')]);

        try {
            $lineRichMenu = $this->lineRichMenuService->getSingleData($id);

            $data = $lineRichMenu->toArray();
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
        $this->authorize('use-controller', ['LineRichMenu', 'update', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineRichMenu = $this->lineRichMenuService->updateRichMenuData($id, $request->all());

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
    public function destroy(Request $request, $id)
    {
        $this->authorize('use-controller', ['LineRichMenu', 'delete', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineRichMenu = $this->lineRichMenuService->destroyRichMenu($id);

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
