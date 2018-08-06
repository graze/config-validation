<?php
/**
 * This file is part of graze/config-validation.
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/config-validation/blob/master/LICENSE.md
 * @link    https://github.com/graze/config-validation
 */

namespace Graze\ConfigValidation\Test\Unit;

use Graze\ConfigValidation\ObjectValidator;
use Graze\ConfigValidation\Test\TestCase;
use Respect\Validation\Validator as v;

class ObjectValidatorTest extends TestCase
{
    public function testDefaultValidator()
    {
        $validator = new ObjectValidator();

        $this->assertTrue($validator->isAllowUnspecified());
        $this->assertEquals('->', $validator->getSeparator());
    }

    public function testSetAllowUnspecified()
    {
        $validator = new ObjectValidator();

        $this->assertTrue($validator->isAllowUnspecified());
        $this->assertSame($validator, $validator->setAllowUnspecified(false));
        $this->assertFalse($validator->isAllowUnspecified());
    }

    public function testSetSeparator()
    {
        $validator = new ObjectValidator();

        $this->assertEquals('->', $validator->getSeparator());
        $this->assertSame($validator, $validator->setSeparator('.'));
        $this->assertEquals('.', $validator->getSeparator());
    }

    /**
     * @dataProvider defaultsDataProvider
     *
     * @param object $input
     * @param object $expected
     *
     * @throws \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     */
    public function testSimpleValidation($input, $expected)
    {
        $validator = (new ObjectValidator())
            ->optional('defaults->path', v::stringType(), '/some/path')
            ->optional('defaults->group', v::stringType(), 'group')
            ->required('must', v::stringType()->equals('be here'));

        $this->assertTrue($validator->isValid($input), 'The input should be valid');
        $actual = $validator->validate($input);

        $this->assertEquals(
            $expected,
            $actual,
            'the generated output should be the same as the expected object'
        );
    }

    /**
     * @return array
     */
    public function defaultsDataProvider()
    {
        return [
            [
                (object) ['must' => 'be here'],
                (object) ['must' => 'be here', 'defaults' => (object) ['path' => '/some/path', 'group' => 'group']],
            ],
            [
                (object) ['must' => 'be here', 'defaults' => (object) ['path' => '/a/path']],
                (object) ['must' => 'be here', 'defaults' => (object) ['path' => '/a/path', 'group' => 'group']],
            ],
            [
                (object) ['must' => 'be here', 'other' => 'cake'],
                (object) ['must' => 'be here', 'defaults' => (object) ['path' => '/some/path', 'group' => 'group'], 'other' => 'cake'],
            ],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param object $input
     *
     * @throws \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     * @expectedException \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     */
    public function testFailedValidation($input)
    {
        $validator = (new ObjectValidator())
            ->optional('defaults->path', v::stringType(), '/some/path')
            ->optional('defaults->group', v::stringType(), 'group')
            ->required('must', v::stringType()->equals('be here'));

        $this->assertFalse($validator->isValid($input));
        $validator->validate($input);
    }

    /**
     * @return array
     */
    public function invalidDataProvider()
    {
        return [
            [(object) ['must' => 'be here', 'defaults' => (object) ['path' => 1]]],
            [(object) ['must' => 'be here', 'defaults' => (object) ['group' => 2]]],
            [(object) ['must' => 'be here', 'defaults' => 'monkey']],
            [(object) ['must' => 'be here', 'defaults' => (object) ['path' => ['idea' => 'poop']]]],
            [[]],
        ];
    }

    /**
     * @dataProvider doNotAllowUnspecifiedData
     *
     * @param object $input
     *
     * @throws \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     * @expectedException \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     */
    public function testDoNotAllowUnspecified($input)
    {
        $validator = (new ObjectValidator(false))
            ->required('stuff')
            ->optional('cake', v::intType());

        $this->assertFalse($validator->isAllowUnspecified());

        $validator->validate($input);
    }

    /**
     * @return array
     */
    public function doNotAllowUnspecifiedData()
    {
        return [
            [(object) ['stuff', 'monkey']],
            [(object) ['stuff', 'cake' => 4, 'poop']],
        ];
    }

    public function testChildBuilders()
    {
        $validator = (new ObjectValidator())
            ->required('default->stuff')
            ->addChild(
                'default->thing',
                (new ObjectValidator())
                    ->required('cake')
                    ->optional('moon')
            );

        $this->assertFalse($validator->isValid((object) ['default' => (object) ['stuff' => 1]]));
        $this->assertTrue($validator->isValid((object) ['default' => (object) ['stuff' => 1, 'thing' => (object) ['cake' => 'yup']]]));
    }
}
