<?php

namespace App\Repositories\Eloquent\Drive;

use App\Repositories\Interfaces\Drive\FileRepositoryInterface;
use App\Models\File;

class FileRepository implements FileRepositoryInterface
{
    public function create(array $data)
    {
        // This is the ONLY place in the entire Drive domain 
        // that actually talks to the database.
        return File::create($data);
    }
}