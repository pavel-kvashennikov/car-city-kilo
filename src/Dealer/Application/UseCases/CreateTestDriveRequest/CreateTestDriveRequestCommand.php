<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\CreateTestDriveRequest;

final class CreateTestDriveRequestCommand
{
    public function __construct(
        public readonly int $vehicleId,
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $email = null,
        public readonly ?string $preferredDate = null,
        public readonly ?string $comment = null,
        public readonly ?int $userId = null,
    ) {}
}
