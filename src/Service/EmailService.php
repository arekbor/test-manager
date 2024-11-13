<?php declare(strict_types=1);

namespace App\Service;

class EmailService
{
    public function __construct(
        private AppSettingService $appSettingService,
        private EncryptionService $encryptionService,
    ) {
    }
}