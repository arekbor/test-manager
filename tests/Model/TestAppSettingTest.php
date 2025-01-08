<?php 

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\TestAppSetting;
use App\Model\TestClauseAppSetting;
use App\Model\TestMessageAppSetting;
use PHPUnit\Framework\TestCase;

class TestAppSettingTest extends TestCase
{
    public function testAddMessageSuccessfullyAddsMessage(): void
    {
        $testAppSetting = new TestAppSetting();
        $testMessage = $this->createMock(TestMessageAppSetting::class);

        $testAppSetting->addTestMessage($testMessage);

        $this->assertCount(1, $testAppSetting->getTestMessages());
        $this->assertSame($testMessage, $testAppSetting->getTestMessages()[0]);
    }

    public function testAddMessagePreventsDuplicateEntries(): void
    {
        $testAppSetting = new TestAppSetting();
        $testMessage = $this->createMock(TestMessageAppSetting::class);

        $testAppSetting->addTestMessage($testMessage);
        $testAppSetting->addTestMessage($testMessage);

        $this->assertCount(1, $testAppSetting->getTestMessages());
    }

    public function testAddClauseSuccessfullyAddsClause(): void
    {
        $testAppSetting = new TestAppSetting();
        $testClause = $this->createMock(TestClauseAppSetting::class);

        $testAppSetting->addTestClause($testClause);

        $this->assertCount(1, $testAppSetting->getTestClauses());
        $this->assertSame($testClause, $testAppSetting->getTestClauses()[0]);
    }

    public function testRemoveClauseSuccessfullyRemovesClause(): void
    {
        $testAppSetting = new TestAppSetting();
        $testClause = $this->createMock(TestClauseAppSetting::class);
        $testAppSetting->addTestClause($testClause);

        $testAppSetting->removeTestClause($testClause);

        $this->assertNotContains($testClause, $testAppSetting->getTestClauses());
        $this->assertCount(0, $testAppSetting->getTestClauses());
    }

    public function testRemoveMessageDoesNothingIfMessageNotPresent(): void
    {
        $testAppSetting = new TestAppSetting();

        $testMessage1 = $this->createMock(TestMessageAppSetting::class);
        $testMessage2 = $this->createMock(TestMessageAppSetting::class);

        $testAppSetting->addTestMessage($testMessage1);

        $testAppSetting->removeTestMessage($testMessage2);

        $this->assertContains($testMessage1, $testAppSetting->getTestMessages());
        $this->assertCount(1, $testAppSetting->getTestMessages());
    }
}