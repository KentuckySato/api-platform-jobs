<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Recruiter;

class RecruiterStateProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor, private ProcessorInterface $removeProcessor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (! $data instanceof Recruiter) {
            return false;
        }

        if ($operation instanceof Post) {
            // Set the created at date
            $data->setCreatedAt(new \DateTimeImmutable());

            // Set and generate the reference of the company with this pattern (CMP + year + month + day + hour + minute + second)
            $data->setReference('REC' . date('YmdHis'));
        }
        if ($operation instanceof Put) {
            $data->setUpdatedAt(new \DateTimeImmutable());
        }
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $result;
    }
}
