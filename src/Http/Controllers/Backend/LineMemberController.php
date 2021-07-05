<?php

namespace Ryan\LineKit\Http\Controllers\Backend;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineMemberService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineMemberController extends Controller
{
    protected $lineMemberService;

    public function __construct(LineMemberService $lineMemberService)
    {
        $this->lineMemberService = $lineMemberService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineMember', 'read', $request->input('organization.id')]);

        $list = $this->lineMemberService->getList($request->all());
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
        $this->authorize('use-controller', ['LineMember', 'add', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $this->lineMemberService->create($request->all());

            DB::commit();
            return $this->success('新增成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest('請聯絡管理員');
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
        $this->authorize('use-controller', ['LineMember', 'read', $request->input('organization.id')]);

        try {
            $data = $this->lineMemberService->getSingleData($id);
            return $this->success($data);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            return $this->badRequest('請聯絡管理員');
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
        $this->authorize('use-controller', ['LineMember', 'update', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $this->lineMemberService->update($id, $request->all());
            DB::commit();
            return $this->success('更新成功');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest('請聯絡管理員');
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
        $this->authorize('use-controller', ['LineMember', 'delete', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $this->lineMemberService->destroy($id);
            DB::commit();
            return $this->success('刪除成功');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFound('找無此資料');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest('請聯絡管理員');
        }
    }
}
