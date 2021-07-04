<?php

namespace Ryan\LineKit\Services\v2;

use Ryan\LineKit\Repositories\v2\LineTagRepository;
use Ryan\LineKit\Services\Service;

class LineTagService extends Service
{
    protected $lineTagRepository;

    public function __construct(LineTagRepository $lineTagRepository)
    {
        parent::__construct($lineTagRepository);

        $this->lineTagRepository = $lineTagRepository;
    }
}
