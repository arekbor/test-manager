<?php 

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Test;
use DateTime;
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{
    public function testIsValidWhenExpirationInFeature(): void
    {
        $test = new Test();
        $test->setExpiration((new DateTime())->modify('+5 days'));
        $test->setSubmission(null);

        $this->assertTrue($test->isValid(), 'Should return true when expiration is in the future.');
    }

    public function testIsNotValidWhenSubmissionIsNotNull(): void
    {
        $test = new Test();
        $test->setExpiration((new DateTime())->modify('+5 days'));
        $test->setExpiration(new DateTime());

        $this->assertFalse($test->isValid(), 'Should return false when submission is not null');
    }

    public function testIsNotValidWhenExpirationInPast(): void
    {
        $test = new Test();
        $test->setExpiration((new DateTime())->modify('-12 hours'));
        $test->setSubmission(null);

        $this->assertFalse($test->isValid(), 'Should return false when expiration is in the past');
    }

    public function testIsNotValidWhenExpirationIsNull(): void
    {
        $test = new Test();
        $test->setExpiration(null);

        $this->assertFalse($test->isValid(), 'Should return false when expiration is null');
    }
}