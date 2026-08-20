<?php

namespace App\Repositories\Interfaces\Drive;

use App\Models\File;

interface FileRepositoryInterface
{
    public function create(array $data): File;
}
