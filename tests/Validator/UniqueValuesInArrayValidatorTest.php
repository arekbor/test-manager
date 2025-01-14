<?php 

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\UniqueValuesInArray;
use App\Validator\UniqueValuesInArrayValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueValuesInArrayValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $context;
    private UniqueValuesInArrayValidator $validator;

    protected function setUp(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $this->context = $this->createMock(ExecutionContext::class);
        $this->validator = new UniqueValuesInArrayValidator($translator);
        $this->validator->initialize($this->context);
    }

    public function testValidArray(): void
    {
        $constraint = new UniqueValuesInArray('getId');
        $this->context->expects($this->never())->method('buildViolation');

        $object1 = new class {
            public function getId() { return 'id1'; }
        };
        $object2 = new class {
            public function getId() { return 'id2'; }
        };

        $this->validator->validate([$object1, $object2], $constraint);
    }

    public function testInvalidArray(): void
    {
        $constraint = new UniqueValuesInArray('getId');

        $this->context->expects($this->once())->method('buildViolation');

        $object1 = new class {
            public function getId() { return 'id1'; }
        };
        $object2 = new class {
            public function getId() { return 'id1'; }
        };

        $this->validator->validate([$object1, $object2], $constraint);
    }

    public function testNonArrayValue(): void
    {
        $constraint = new UniqueValuesInArray('getId');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate([], $constraint);
    }

    public function testMissingKeyMethod(): void
    {
        $constraint = new UniqueValuesInArray('getId');

        $this->context->expects($this->never())->method('buildViolation');

        $object1 = new class {
            public function getName() { return 'name1'; }
        };
        $object2 = new class {
            public function getName() { return 'name2'; }
        };

        $this->validator->validate([$object1, $object2], $constraint);
    }
}