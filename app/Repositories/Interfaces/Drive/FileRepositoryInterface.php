<?php

namespace App\Repositories\Interfaces\Drive;

interface FileRepositoryInterface
{
    /**
     * Create a new file record in the database.
     */
    public function create(array $data);
}