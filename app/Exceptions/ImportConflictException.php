<?php

namespace App\Exceptions;

use Exception;

class ImportConflictException extends Exception
{
    public function __construct(private readonly array $conflicts)
    {
        parent::__construct('Import rejected because conflicts were detected.');
    }

    public function conflicts(): array
    {
        return $this->conflicts;
    }
}
