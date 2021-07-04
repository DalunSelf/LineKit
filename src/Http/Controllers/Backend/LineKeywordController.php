<?php

namespace Ryan\LineKit\Http\Controllers\Backend\v2;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineKeywordService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineKeywordController extends Controller
{
    protected $lineKeywordService;

    public function __construct(LineKeywordService $lineKeywordService)
    {
        $this->lineKeywordService = $lineKeywordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineKeyword', 'read', $request->input('organization.id')]);

        $list = $this->lineKeywordService->getList($request->all());
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
        $this->authorize('use-controller', ['LineKeyword', 'add', $request->input('organization.id')]);

        $request->validate(
            [
                'type'                          => 'required',
                'keys'                          => 'required_unless:type,100|array',
                'samples'                       => 'required|min:1|array',
            ],
            [
                'keys.required_unless'          => '關鍵字 不能留空。',
            ],
            [
                'type'                          => '觸發類型',
                'keys'                          => '關鍵字',
                'samples'                       => '樣板內容',
            ]
        );

        try {
            DB::beginTransaction();
            $lineKeyword = $this->lineKeywordService->createKeywordData($request->all());

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
        $this->authorize('use-controller', ['LineKeyword', 'read', $request->input('organization.id')]);

        try {
            $lineKeyword = $this->lineKeywordService->getKeywordSingleData($id);

            $data = $lineKeyword->toArray();
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
        $this->authorize('use-controller', ['LineKeyword', 'update', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineKeyword = $this->lineKeywordService->updateKeywordData($id, $request->all());

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
        $this->authorize('use-controller', ['LineKeyword', 'delete', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineKeyword = $this->lineKeywordService->destroy($id);

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

    public function updateKeywordSwitch(Request $request, $id)
    {
        $this->authorize('use-controller', ['LineKeyword', 'update', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineKeyword = $this->lineKeywordService->update($id, [
                'status' => $request['status']
            ]);

            DB::commit();
            return $this->success('狀態修改成功');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest($e->getMessage() ?: '請聯絡管理員');
        }
    }
}
