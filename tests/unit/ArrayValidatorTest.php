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

use Graze\ConfigValidation\ArrayValidator;
use Graze\ConfigValidation\Test\TestCase;
use Respect\Validation\Validator as v;

class ArrayValidatorTest extends TestCase
{
    public function testDefaultValidator()
    {
        $validator = new ArrayValidator();

        $this->assertTrue($validator->isAllowUnspecified());
        $this->assertEquals('.', $validator->getSeparator());
    }

    public function testSetAllowUnspecified()
    {
        $validator = new ArrayValidator();

        $this->assertTrue($validator->isAllowUnspecified());
        $this->assertSame($validator, $validator->setAllowUnspecified(false));
        $this->assertFalse($validator->isAllowUnspecified());
    }

    public function testSetSeparator()
    {
        $validator = new ArrayValidator();

        $this->assertEquals('.', $validator->getSeparator());
        $this->assertSame($validator, $validator->setSeparator('->'));
        $this->assertEquals('->', $validator->getSeparator());
    }

    /**
     * @dataProvider defaultsDataProvider
     *
     * @param array $input
     * @param array $expected
     *
     * @throws \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     */
    public function testSimpleValidation(array $input, array $expected)
    {
        $validator = (new ArrayValidator())
            ->optional('defaults.path', v::stringType(), '/some/path')
            ->optional('defaults.group', v::stringType(), 'group')
            ->required('must', v::stringType()->equals('be here'));

        $this->assertTrue($validator->isValid($input));
        $actual = $validator->validate($input);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function defaultsDataProvider()
    {
        return [
            [
                ['must' => 'be here'],
                ['must' => 'be here', 'defaults' => ['path' => '/some/path', 'group' => 'group']],
            ],
            [
                ['must' => 'be here', 'defaults' => ['path' => '/a/path']],
                ['must' => 'be here', 'defaults' => ['path' => '/a/path', 'group' => 'group']],
            ],
            [
                ['must' => 'be here', 'other' => 'cake'],
                ['must' => 'be here', 'defaults' => ['path' => '/some/path', 'group' => 'group'], 'other' => 'cake'],
            ],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param array $input
     *
     * @throws \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     * @expectedException \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     */
    public function testFailedValidation(array $input)
    {
        $validator = (new ArrayValidator())
            ->optional('defaults.path', v::stringType(), '/some/path')
            ->optional('defaults.group', v::stringType(), 'group')
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
            [['must' => 'be here', 'defaults' => ['path' => 1]]],
            [['must' => 'be here', 'defaults' => ['group' => 2]]],
            [['must' => 'be here', 'defaults' => 'monkey']],
            [['must' => 'be here', 'defaults' => ['path' => ['idea' => 'poop']]]],
            [[]],
        ];
    }

    /**
     * @dataProvider doNotAllowUnspecifiedData
     *
     * @param array $input
     *
     * @throws \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     * @expectedException \Graze\ConfigValidation\Exceptions\ConfigValidationFailedException
     */
    public function testDoNotAllowUnspecified(array $input)
    {
        $validator = (new ArrayValidator(false))
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
            [['stuff', 'monkey']],
            [['stuff', 'cake' => 4, 'poop']],
        ];
    }

    public function testDefaultValidations()
    {
        $validator = (new ArrayValidator())
            ->required('stuff')
            ->optional('cake');

        $this->assertTrue($validator->isValid(['stuff' => 'yup']));
    }

    public function testChildRequireDependencies()
    {
        $validator = (new ArrayValidator())
            ->required('default.stuff.option')
            ->optional('default.thing');

        $this->assertFalse($validator->isValid(['default' => 'nope']));
        $this->assertFalse($validator->isValid([]));
    }
}
