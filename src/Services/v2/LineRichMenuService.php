<?php

namespace Ryan\LineKit\Services\v2;

use App\Services\Sdk\LineBotRichmenuService;
use Ryan\LineKit\Repositories\v2\LineRichMenuRepository;
use Ryan\LineKit\Services\Service;
use Illuminate\Database\Eloquent\Model;

class LineRichMenuService extends Service
{
    protected $lineRichMenuRepository;

    public function __construct(LineRichMenuRepository $lineRichMenuRepository)
    {
        parent::__construct($lineRichMenuRepository);

        $this->lineRichMenuRepository = $lineRichMenuRepository;
    }

    public function createRichMenuData(array $attributes)
    {
        $lineRichMenu = $this->lineRichMenuRepository->create([
            'line_channel_id'   => $attributes['line_channel']['id'],
            'name'              => $attributes['name'],
            'chatBarText'       => $attributes['chatBarText'],
            'selected'          => $attributes['selected'],
            'size'              => $attributes['size'],
            'areas'             => $attributes['areas'],
            'image'             => $attributes['image'],
            'type'              => $attributes['type'],
            'start_at'          => $attributes['start_at'],
            'end_at'            => $attributes['end_at'],
        ]);

        // 打 Api 創建圖文選單
        $createRichMenuAndUploadImage = (new LineBotRichmenuService())->createRichMenuAndUploadImage(
            $lineRichMenu->line_channel,
            $lineRichMenu,
        );
        return $lineRichMenu;
    }

    public function updateRichMenuData($id, array $attributes)
    {
        $lineRichMenu = $this->lineRichMenuRepository->find($id);

        // 打 Api 創建圖文選單
        $result = $this->lineRichMenuRepository->update($id, [
            'name'              => $attributes['name'],
            'chatBarText'       => $attributes['chatBarText'],
            'selected'          => $attributes['selected'],
            'size'              => $attributes['size'],
            'areas'             => $attributes['areas'],
            'image'             => $attributes['image'],
            'type'              => $attributes['type'],
            'start_at'          => $attributes['start_at'],
            'end_at'            => $attributes['end_at'],
            'status'            => 0,
        ]);

        // 先刷新
        $lineRichMenu->refresh();
        // 刪除選單的
        $deleteRichMenu = (new LineBotRichmenuService())->deleteRichMenu(
            $lineRichMenu->line_channel,
            $lineRichMenu,
        );
        // 打 Api 創建圖文選單
        $createRichMenuAndUploadImage = (new LineBotRichmenuService())->createRichMenuAndUploadImage(
            $lineRichMenu->line_channel,
            $lineRichMenu,
        );
        return $result;
    }

    public function destroyRichMenu($id)
    {
        $lineRichMenu = $this->lineRichMenuRepository->find($id);
        // 刪除選單的
        $deleteRichMenu = (new LineBotRichmenuService())->deleteRichMenu(
            $lineRichMenu->line_channel,
            $lineRichMenu,
        );
        return $lineRichMenu->delete();
    }


    /**
     * 檢查圖文選單狀態
     *
     * @return void
     */
    public function checkRichMenuSync(Model $lineRichMenu)
    {
        $lineRichMenu->refresh();

        // 判斷發送狀態: 1 上架, 2 下架
        if ($lineRichMenu->status === 1) {
            if ($lineRichMenu->type == 'all') {
                // 全體用戶
                (new LineBotRichmenuService())->setDefaultRichMenuId(
                    $lineRichMenu->line_channel,
                    $lineRichMenu,
                );
            }
        } else if ($lineRichMenu->status === 2) {
            if ($lineRichMenu->type == 'all') {
                // 全體用戶
                (new LineBotRichmenuService())->cancelDefaultRichMenuId(
                    $lineRichMenu->line_channel,
                    $lineRichMenu,
                );
            }
        }
    }
}
