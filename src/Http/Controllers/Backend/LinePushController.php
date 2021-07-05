<?php

namespace Ryan\LineKit\Http\Controllers\Backend;

use Ryan\LineKit\Http\Controllers\Controller;
use Ryan\LineKit\Services\v2\LinePushService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LinePushController extends Controller
{
    protected $linePushService;

    public function __construct(LinePushService $linePushService)
    {
        $this->linePushService = $linePushService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('use-controller', ['LinePush', 'read', $request->input('organization.id')]);

        $list = $this->linePushService->getList($request->all());
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
        $this->authorize('use-controller', ['LinePush', 'add', $request->input('organization.id')]);

        $request->validate(
            [
                'send_target'                   => 'required',
                'send_type'                     => 'required',
                'samples'                       => 'required|min:1|array',
            ],
            [
                // 'keys.required_unless'          => '關鍵字 不能留空。',
            ],
            [
                'send_target'                   => '發送對象',
                'send_type'                     => '發送方式',
                'samples'                       => '樣板內容',
            ]
        );

        try {
            DB::beginTransaction();
            $linePush = $this->linePushService->createPushData($request->all());

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
        $this->authorize('use-controller', ['LinePush', 'read', $request->input('organization.id')]);

        try {
            $linePush = $this->linePushService->getSingleData($id);

            $data = $linePush->toArray();

            $data['samples'] = $linePush->samples->toArray();
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
        $this->authorize('use-controller', ['LinePush', 'update', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $linePush = $this->linePushService->updatePushData($id, $request->all());
            // $linePush = $this->linePushService->getSingleData($id);
            // if (isset($request['samples'])) {

            //     $processActionItems = function ($item, $flex_type, $line_push_id) use (&$processActionItems, &$action_items) {
            //         if (isset($item['action'])) {
            //             $random_code = \Illuminate\Support\Str::random(30);
            //             $action_items[] = [
            //                 // 'type' =>  $type,
            //                 'line_push_id'   => $line_push_id,
            //                 'serial_code'           => $random_code,
            //                 'display_text'          => $flex_type . '-' . $item['type'],
            //                 'original_format'       => $item,
            //                 'click_total_count'     => 0,
            //             ];
            //             // 轉換格式
            //             if (isset($item['action']['type']) && $item['action']['type'] === 'message') {
            //                 $item['action']['type'] = 'postback';
            //                 $item['action']['data'] = json_encode(['type' => 'LINE_PUSH', 'serial_code' => $random_code]);
            //                 $item['action']['displayText'] = $item['action']['text'];
            //                 unset($item['action']['text']);
            //             }
            //             if (isset($item['action']['type']) && $item['action']['type'] === 'postback') {
            //                 $item['action']['type'] = 'postback';
            //                 $item['action']['data'] = json_encode(['type' => 'LINE_PUSH', 'serial_code' => $random_code]);
            //                 $item['action']['displayText'] = $item['action']['displayText'];
            //             }
            //             if (isset($item['action']['type']) && $item['action']['type'] === 'uri') {
            //                 $item['action']['uri'] = route('short.url', ['serial_code' => $random_code]);
            //             }
            //         }

            //         if (isset($item['contents'])) {
            //             foreach ($item['contents'] as $index => $content) {
            //                 Arr::set(
            //                     $item,
            //                     'contents.' . $index,
            //                     $processActionItems($content, $flex_type, $line_push_id)
            //                 );
            //             }
            //             // $boxItems['contents']['qqqqq'] = '111';
            //             // $boxItems['contents'] = collect($items['contents'])->transform(function ($item) {
            //             //     if ($item->has('action') && $item->has('message')) {
            //             //         $item['action']['type'] = 'postback';
            //             //         $item['action']['data'] = $item['action']['text'];
            //             //         $item['action']['displayText'] = $item['action']['text'];
            //             //         unset($item['action']['text']);
            //             //     }
            //             //     return $item;
            //             // });
            //         }
            //         return $item;
            //     };

            //     info($request['samples']);
            //     $this->linePushService->proccesRelationWithRequest($linePush->samples(), $request['samples']);
            //     foreach ($linePush->samples as $sample) {
            //         if ($sample['type'] !== 'module') {
            //             break;
            //         }
            //         // 重新寫入 sample 樣板內容的格式
            //         $re_update = $sample->toArray();

            //         $action_items = [];
            //         $carousels = $sample['original_value']['contents']['contents'];
            //         foreach ($carousels as $index => $carousel) {
            //             $header = $carousel['header'] ?? null;
            //             $hero   = $carousel['hero'] ?? null;
            //             $body   = $carousel['body'] ?? null;
            //             $footer = $carousel['footer'] ?? null;
            //             if ($header) {
            //                 Arr::set(
            //                     $re_update,
            //                     'parameter_value.contents.contents.' . $index . '.header',
            //                     $processActionItems($header, 'header', $linePush->id)
            //                 );
            //             }
            //             if ($hero) {
            //                 Arr::set(
            //                     $re_update,
            //                     'parameter_value.contents.contents.' . $index . '.hero',
            //                     $processActionItems($hero, 'hero', $linePush->id)
            //                 );
            //             }
            //             if ($body) {
            //                 Arr::set(
            //                     $re_update,
            //                     'parameter_value.contents.contents.' . $index . '.body',
            //                     $processActionItems($body, 'body', $linePush->id)
            //                 );
            //             }
            //             if ($footer) {
            //                 Arr::set(
            //                     $re_update,
            //                     'parameter_value.contents.contents.' . $index . '.footer',
            //                     $processActionItems($footer, 'footer', $linePush->id)
            //                 );
            //             }
            //         }

            //         $this->linePushService->proccesRelationWithRequest($sample->action_records(), $action_items);

            //         // 更新重新更新的格式
            //         $sample->update([
            //             'parameter_value' => $re_update['parameter_value']
            //         ]);
            //     }
            // }

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
        $this->authorize('use-controller', ['LinePush', 'delete', $request->input('organization.id')]);

        try {
            DB::beginTransaction();
            $linePush = $this->linePushService->destroy($id);

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
