<?php 

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Application\Util\ArrayHelper;
use App\Application\AppSetting\Model\TestMessageAppSetting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ArrayHelperTest extends TestCase
{
    #[Test]
    public function testAddItemAddsItemSuccessfully(): void
    {
        //Arrange
        $testArray = [];
        $testingValue = 'test';

        //Act
        ArrayHelper::addItem($testArray, $testingValue);

        //Assert
        $this->assertCount(1, $testArray);
        $this->assertSame($testingValue, $testArray[0]);
    }

    #[Test]
    public function testAddItemPreventsDuplicateEntries(): void
    {
        //Arrange
        $testArray = [];
        $testingValue = 'test';

        //Act
        ArrayHelper::addItem($testArray, $testingValue);
        ArrayHelper::addItem($testArray, $testingValue);
        ArrayHelper::addItem($testArray, $testingValue);
        ArrayHelper::addItem($testArray, $testingValue);

        //Assert
        $this->assertCount(1, $testArray);
    }

    #[Test]
    public function testRemoveItemRemovesItemSuccessfully(): void
    {
        //Arrange
        $testingValue1 = 'test1';
        $testingValue2 = 'test2';

        $testArray = [$testingValue1, $testingValue2];

        //Act
        ArrayHelper::removeItem($testArray, $testingValue2);

        //Assert
        $this->assertCount(1, $testArray);
        $this->assertSame($testingValue1, $testArray[0]);
    }

    #[Test]
    public function testRemoveItemDoesNothingWhenItemNotFound(): void
    {
        //Arrange
        $testingValue1 = 'test1';
        $testingValue2 = 'test2';

        $testArray = [$testingValue1];

        //Act
        ArrayHelper::removeItem($testArray, $testingValue2);

        //Assert
        $this->assertCount(1, $testArray);
        $this->assertContains($testingValue1, $testArray);
    }

    #[Test]
    public function testFindFirstByPropertyReturnsCorrectObject(): void
    {
        //Arrange
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

        //Act
        $testMessageAppSetting = ArrayHelper::findFirstByProperty($testMessages, 'getLanguage', 'en');
        
        //Assert
        $this->assertEquals('test 3', $testMessageAppSetting->getIntroduction());
        $this->assertEquals('test 4', $testMessageAppSetting->getConclusion());
    }

    #[Test]
    public function testFindFirstByPropertyReturnsNullWhenNoMatchFound(): void
    {
        //Arrange
        $testMessages = [];

        //Act
        $testMessageAppSetting = ArrayHelper::findFirstByProperty($testMessages, 'getLanguage', 'pl');

        //Assert
        $this->assertNull($testMessageAppSetting);
    }

    #[Test]
    public function testFindFirstByPropertyReturnsNullWhenMethodDoesNotExist(): void
    {
        //Arrange
        $testMessages = [
            (new TestMessageAppSetting())
                ->setIntroduction('test 1')
                ->setConclusion('test 2')
                ->setLanguage('pl')
        ];

        //Act
        $testMessageAppSetting = ArrayHelper::findFirstByProperty($testMessages, 'getFirstMessage', 'pl');

        //Assert
        $this->assertNull($testMessageAppSetting);
    }
}