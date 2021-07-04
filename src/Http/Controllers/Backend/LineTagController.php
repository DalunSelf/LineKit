<?php

namespace Ryan\LineKit\Http\Controllers\Backend\v2;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineTagService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineTagController extends Controller
{
    protected $lineTagService;

    public function __construct(LineTagService $lineTagService)
    {
        $this->lineTagService = $lineTagService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineTag', 'read', $request->input('organization.id')]);

        $list = $this->lineTagService->getList($request->all());
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
        $this->authorize('use-controller', ['LineTag', 'add', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineTag = $this->lineTagService->create($request->all());

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
        $this->authorize('use-controller', ['LineTag', 'read', $request->input('organization.id')]);

        try {
            $lineTag = $this->lineTagService->getSingleData($id);

            $data = $lineTag->toArray();
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
        $this->authorize('use-controller', ['LineTag', 'update', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineTag = $this->lineTagService->update($id, $request->all());

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
        $this->authorize('use-controller', ['LineTag', 'delete', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $lineTag = $this->lineTagService->destroy($id);

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

    public function getAllTag(Request $request)
    {
        return $this->lineTagService->getAll(['id', 'name']);
    }
}
