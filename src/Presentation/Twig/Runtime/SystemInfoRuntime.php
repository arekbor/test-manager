<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Runtime;

use App\Domain\Entity\SecurityUser;
use App\Presentation\Util\ByteConversion;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SystemInfoRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly ParameterBagInterface $parameterBag
    ) {}

    private const PATH_NOT_FOUND_INFO = "Path not found";

    /**
     * @return array<string, mixed>
     */
    public function getSystemInfo(): array
    {
        /**
         * @var SecurityUser $user
         */
        $user = $this->security->getUser();

        $timezone = date_default_timezone_get();

        $systemLogsPath = $this->parameterBag->get('app.logs.path');
        $systemLogsPath = !empty($systemLogsPath) ? $systemLogsPath : self::PATH_NOT_FOUND_INFO;

        $videosPath = $this->parameterBag->get('app.videos.path');
        $videosPath = !empty($videosPath) ? $videosPath : self::PATH_NOT_FOUND_INFO;

        $testResultsPath = $this->parameterBag->get('app.testResults.path');
        $testResultsPath = !empty($testResultsPath) ? $testResultsPath : self::PATH_NOT_FOUND_INFO;

        $testResultsTotalSize = $this->getDirectorySize($testResultsPath);
        $videosTotalSize = $this->getDirectorySize($videosPath);

        $systemTotalSize = disk_total_space("/");
        $systemTotalSize = $systemTotalSize !== false ? ByteConversion::formatBytes($systemTotalSize) : '0 Bytes';

        $systemFreeSpace = disk_free_space("/");
        $systemFreeSpace = $systemFreeSpace !== false ? ByteConversion::formatBytes($systemFreeSpace) : '0 Bytes';

        return [
            'systemTimezone' => $timezone,
            'currentUserEmail' => $user->getEmail(),
            'systemLogsPath' => $systemLogsPath,
            'videosPath' => $videosPath,
            'testResultsPath' => $testResultsPath,
            'testResultsTotalSize' => $testResultsTotalSize,
            'videosTotalSize' => $videosTotalSize,
            'systemTotalSize' => $systemTotalSize,
            'systemFreeSpace' => $systemFreeSpace
        ];
    }

    private function getDirectorySize(string $path): string
    {
        $bytesTotal = 0;

        $path = realpath($path);
        if ($path != false && file_exists($path)) {
            $recursiveDirectoryIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            foreach (new \RecursiveIteratorIterator($recursiveDirectoryIterator) as $file) {
                if (!$file instanceof \SplFileInfo) {
                    break;
                }

                $bytesTotal += $file->getSize();
            }
        }

        return ByteConversion::formatBytes($bytesTotal);
    }
}
