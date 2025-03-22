<?php 

declare(strict_types=1);

namespace App\Tests\Util;

use App\Domain\Model\TestMessageAppSetting;
use App\Util\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function testAddItemSuccessfullyAddsItem(): void
    {
        $testArray = [];
        $testingValue = 'test';

        ArrayHelper::addItem($testArray, $testingValue);

        $this->assertCount(1, $testArray);
        $this->assertSame($testingValue, $testArray[0]);
    }

    public function testAddItemPreventsDuplicateEntries(): void
    {
        $testArray = [];
        $testingValue = 'test';

        ArrayHelper::addItem($testArray, $testingValue);
        ArrayHelper::addItem($testArray, $testingValue);
        ArrayHelper::addItem($testArray, $testingValue);
        ArrayHelper::addItem($testArray, $testingValue);

        $this->assertCount(1, $testArray);
    }

    public function testRemoveItemSuccessfullyRemovesItem(): void
    {
        $testingValue1 = 'test1';
        $testingValue2 = 'test2';

        $testArray = [
            $testingValue1, $testingValue2
        ];

        ArrayHelper::removeItem($testArray, $testingValue2);

        $this->assertCount(1, $testArray);
        $this->assertSame($testingValue1, $testArray[0]);
    }

    public function testRemoveItemDoesNothingIfItemNotPresent(): void
    {
        $testingValue1 = 'test1';
        $testingValue2 = 'test2';

        $testArray = [
            $testingValue1
        ];

        ArrayHelper::removeItem($testArray, $testingValue2);

        $this->assertCount(1, $testArray);
        $this->assertContains($testingValue1, $testArray);
    }

    public function testFindFirstByPropertyReturnsCorrectObject(): void
    {
        $testMessages = [
            (new TestMessageAppSetting())
                ->setIntroduction('test 1')
                ->setConclusion('test 2')
                ->setLanguage('pl'),

            (new TestMessageAppSetting())
                ->setIntroduction('test 3')
                ->setConclusion('test 4')
                ->setLanguage('en'),

            (new TestMessageAppSetting())
                ->setIntroduction('test 4')
                ->setConclusion('test 5')
                ->setLanguage('fr'),
        ];

        $testMessageAppSetting = ArrayHelper::findFirstByProperty($testMessages, 'getLanguage', 'en');
        
        $this->assertEquals('test 3', $testMessageAppSetting->getIntroduction());
        $this->assertEquals('test 4', $testMessageAppSetting->getConclusion());
    }

    public function testFindFirstByPropertyReturnsNullWhenNoMatchFound(): void
    {
        $testMessages = [];

        $testMessageAppSetting = ArrayHelper::findFirstByProperty($testMessages, 'getLanguage', 'pl');

        $this->assertNull($testMessageAppSetting);
    }

    public function testFindFirstByPropertyReturnsNullWhenMethodNotExists(): void
    {
        $testMessages = [
            (new TestMessageAppSetting())
                ->setIntroduction('test 1')
                ->setConclusion('test 2')
                ->setLanguage('pl')
        ];

        $testMessageAppSetting = ArrayHelper::findFirstByProperty($testMessages, 'getFirstMessage', 'pl');

        $this->assertNull($testMessageAppSetting);
    }
}