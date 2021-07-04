<?php

namespace Ryan\LineKit\Http\Controllers\Backend\v2;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LinePushActionRecordService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LinePushActionRecordController extends Controller
{
    protected $linePushActionRecordService;

    public function __construct(LinePushActionRecordService $linePushActionRecordService)
    {
        $this->linePushActionRecordService = $linePushActionRecordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LinePushActionRecord', 'read', null]);

        $list = $this->linePushActionRecordService->getList($request->all());
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
        $this->authorize('use-controller', ['LinePushActionRecord', 'add', null]);

        try {
            DB::beginTransaction();
            $linePushActionRecord = $this->linePushActionRecordService->create($request->all());

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
        $this->authorize('use-controller', ['LinePushActionRecord', 'read', null]);

        try {
            $linePushActionRecord = $this->linePushActionRecordService->getSingleData($id);

            $data = $linePushActionRecord->toArray();
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
        $this->authorize('use-controller', ['LinePushActionRecord', 'update', null]);

        try {
            DB::beginTransaction();
            $linePushActionRecord = $this->linePushActionRecordService->update($id, $request->all());

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
        $this->authorize('use-controller', ['LinePushActionRecord', 'delete', null]);

        try {
            DB::beginTransaction();
            $linePushActionRecord = $this->linePushActionRecordService->destroy($id);

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
