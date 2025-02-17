<?php

use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

if (!class_exists(KernelTestBehaviour::class)) {
    require_once __DIR__ . '/../vendor/shopware/core/Framework/Test/TestCaseBase/KernelTestBehaviour.php';
}

if (file_exists(__DIR__ . '/../.env')) {
    (new Dotenv())->load(__DIR__ . '/../.env');
}
