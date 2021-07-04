<?php

namespace Ryan\LineKit\Services\v2;

use Ryan\LineKit\Repositories\v2\LineFlexTemplateRepository;
use Ryan\LineKit\Services\Service;

class LineFlexTemplateService extends Service
{
    protected $lineFlexTemplateRepository;

    public function __construct(LineFlexTemplateRepository $lineFlexTemplateRepository)
    {
        parent::__construct($lineFlexTemplateRepository);

        $this->lineFlexTemplateRepository = $lineFlexTemplateRepository;
    }
}
