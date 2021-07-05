<?php

namespace Ryan\LineKit\Http\Controllers\Backend;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LineFlexTemplateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineFlexTemplateController extends Controller
{
    protected $lineFlexTemplateService;

    public function __construct(LineFlexTemplateService $lineFlexTemplateService)
    {
        $this->lineFlexTemplateService = $lineFlexTemplateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LineFlexTemplate', 'read', null]);

        // 每次讀取將重新 SEEDER 建立
        if (config('app.debug') == true) {
            $files = new \Illuminate\Filesystem\Filesystem;
            $list_json = $this->lineFlexTemplateService->getAll();
            $stub = $files->get(base_path('app/Console/Commands/Make/stubs/flexTemplateSeeder.stub'));
            $stub = str_replace(
                '{{ json }}',
                urlencode(json_encode($list_json->toArray(), JSON_UNESCAPED_UNICODE)),
                $stub
            );
            $files->put(base_path('database/seeders/FlexTemplateSeeder.php'), $stub);
        }

        $list = $this->lineFlexTemplateService->getList($request->all());
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
        $this->authorize('use-controller', ['LineFlexTemplate', 'add', null]);

        try {
            DB::beginTransaction();
            $lineFlexTemplate = $this->lineFlexTemplateService->create([
                'name'      => $request->input('name'),
                'flex'      => json_decode($request->input('flex'), true),
            ]);

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
        $this->authorize('use-controller', ['LineFlexTemplate', 'read', null]);

        try {
            $lineFlexTemplate = $this->lineFlexTemplateService->getSingleData($id);

            $data = $lineFlexTemplate->toArray();
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
        $this->authorize('use-controller', ['LineFlexTemplate', 'update', null]);

        try {
            DB::beginTransaction();
            $lineFlexTemplate = $this->lineFlexTemplateService->update($id, [
                'name'      => $request->input('name'),
                'flex'      => json_decode($request->input('flex'), true),
            ]);

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
        $this->authorize('use-controller', ['LineFlexTemplate', 'delete', null]);

        try {
            DB::beginTransaction();
            $lineFlexTemplate = $this->lineFlexTemplateService->destroy($id);

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
