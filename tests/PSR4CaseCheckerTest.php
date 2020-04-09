<?php
/*
 * This file is part of the fyrts/psr-4-case-checker library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PSR4CaseChecker\Tests;

use PHPUnit\Framework\TestCase;
use PSR4CaseChecker\PSR4CaseChecker;
use PSR4CaseChecker\ClassnameCasingException;
use PSR4CaseChecker\Tests\Classes;

class PSR4CaseCheckerTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		
		PSR4CaseChecker::init();
	}
    
    public function testCorrectCaseClass()
    {
        $result = Classes\CorrectCaseNamespace\CorrectCaseClass::testMethod();
        $this->assertTrue($result);
    }
	
	public function testWrongCaseClass()
	{
        $this->expectException(ClassnameCasingException::class);
		$result = Classes\CorrectCaseNamespace\WrongCaseClass::testMethod();
	}
    
    public function testWrongCaseNamespace()
    {
        $this->expectException(ClassnameCasingException::class);
        $result = Classes\WrongCaseNamespace\CorrectCaseClass::testMethod();
    }
}
