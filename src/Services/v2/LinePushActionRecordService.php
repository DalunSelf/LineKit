<?php

namespace Ryan\LineKit\Services\v2;

use Ryan\LineKit\Repositories\v2\LinePushActionRecordRepository;
use Ryan\LineKit\Services\Service;

class LinePushActionRecordService extends Service
{
    protected $linePushActionRecordRepository;

    public function __construct(LinePushActionRecordRepository $linePushActionRecordRepository)
    {
        parent::__construct($linePushActionRecordRepository);

        $this->linePushActionRecordRepository = $linePushActionRecordRepository;
    }
}
