<?php 

declare(strict_types=1);

namespace App\Tests\Util;

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
}