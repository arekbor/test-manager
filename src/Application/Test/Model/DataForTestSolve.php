<?php

declare(strict_types = 1);

namespace App\Application\Test\Model;

use App\Application\Test\Model\TestSolve;
use Symfony\Component\Uid\Uuid;
use App\Application\Video\Model\TestVideo;

final class DataForTestSolve
{
    private Uuid $testId;
    private string $testCategory;
    private TestSolve $testSolve;

    /**
     * @var TestVideo[] $testVideos
     */
    private array $testVideos;

    public function __construct() {
        $this->testVideos = [];
    }

    public function getTestId(): Uuid
    {
        return $this->testId;
    }

    public function setTestId(Uuid $testId): void
    {
        $this->testId = $testId;
    }

    public function getTestCategory(): string
    {
        return $this->testCategory;
    }

    public function setTestCategory(string $testCategory): void
    {
        $this->testCategory = $testCategory;
    }

    public function getTestSolve(): TestSolve
    {
        return $this->testSolve;
    }

    public function setTestSolve(TestSolve $testSolve): void
    {
        $this->testSolve = $testSolve;
    }

    public function getTestVideos(): array
    {
        return $this->testVideos;
    }

    /**
     * @param TestVideo[] $testVideos
     */
    public function setTestVideos(array $testVideos): static
    {
        $this->testVideos = $testVideos;

        return $this;
    }
}