<?php

namespace App\Services;

use App\Models\JobFailures;

class JobFailureService
{
    protected $job;
    protected $error;

    public function __construct(string $job, string $error, string $identifier, string $identifier_type)
    {
        $this->job = $job;
        $this->error = $error;
        $this->identifier = $identifier;
        $this->identifier_type = $identifier_type;
    }

    public function createJobFailure()
    {
        return JobFailures::create([
            'job_name' => $this->job,
            'error' => $this->error,
            'identifier' => $this->identifier,
            'identifier_type' => $this->identifier_type
        ]);
    }
}
