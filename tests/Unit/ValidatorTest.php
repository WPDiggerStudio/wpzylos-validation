<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Validation\Validator;

/**
 * Tests for Validator class.
 */
class ValidatorTest extends TestCase
{
    public function testPassesWithValidData(): void
    {
        $validator = new Validator(
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'required|string', 'email' => 'required|email']
        );

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator->fails());
    }

    public function testFailsWithInvalidData(): void
    {
        $validator = new Validator(
            ['name' => '', 'email' => 'invalid'],
            ['name' => 'required', 'email' => 'email']
        );

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
    }

    public function testRequiredRuleFails(): void
    {
        $validator = new Validator(
            ['name' => ''],
            ['name' => 'required']
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function testEmailRuleFails(): void
    {
        $validator = new Validator(
            ['email' => 'not-an-email'],
            ['email' => 'email']
        );

        $this->assertTrue($validator->fails());
    }

    public function testMinRuleForString(): void
    {
        $validator = new Validator(
            ['name' => 'ab'],
            ['name' => 'min:3']
        );

        $this->assertTrue($validator->fails());
    }

    public function testMaxRuleForString(): void
    {
        $validator = new Validator(
            ['name' => 'abcdef'],
            ['name' => 'max:5']
        );

        $this->assertTrue($validator->fails());
    }

    public function testInRulePasses(): void
    {
        $validator = new Validator(
            ['status' => 'active'],
            ['status' => 'in:active,inactive,pending']
        );

        $this->assertTrue($validator->passes());
    }

    public function testInRuleFails(): void
    {
        $validator = new Validator(
            ['status' => 'unknown'],
            ['status' => 'in:active,inactive']
        );

        $this->assertTrue($validator->fails());
    }

    public function testNullableSkipsOtherRules(): void
    {
        $validator = new Validator(
            ['age' => null],
            ['age' => 'nullable|int|min:18']
        );

        $this->assertTrue($validator->passes());
    }

    public function testValidatedReturnsValidData(): void
    {
        $validator = new Validator(
            ['name' => 'John', 'email' => 'john@example.com', 'extra' => 'ignored'],
            ['name' => 'required', 'email' => 'required']
        );

        $validated = $validator->validated();

        $this->assertArrayHasKey('name', $validated);
        $this->assertArrayHasKey('email', $validated);
        $this->assertArrayNotHasKey('extra', $validated);
    }

    public function testErrorsReturnsMessageBag(): void
    {
        $validator = new Validator(
            ['name' => ''],
            ['name' => 'required']
        );

        $validator->fails();
        $errors = $validator->errors();

        $this->assertTrue($errors->has('name'));
        $this->assertNotEmpty($errors->first('name'));
    }
}
