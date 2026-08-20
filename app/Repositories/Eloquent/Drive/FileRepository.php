<?php

namespace App\Repositories\Eloquent\Drive;

use App\Models\File;
use App\Repositories\Interfaces\Drive\FileRepositoryInterface;

class FileRepository implements FileRepositoryInterface
{
    public function create(array $data): File
    {
        return File::create($data);
    }
}
