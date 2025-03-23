<?php

use App\Infrastructure\TestManagerKernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new TestManagerKernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
