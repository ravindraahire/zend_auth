<?php
namespace phpreboot\tddworkshop;

use phpreboot\tddworkshop\Calculator;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $calculator;

    public function setUp()
    {
        $this->calculator = new Calculator();
    }

    public function tearDown()
    {
        $this->calculator = null;
    }

    public function testAddReturnsAnInteger()
    {
        $result = $this->calculator->add();

        $this->assertInternalType('integer', $result, 'Result of `add` is not an integer.');
    }

    public function testAddWithoutParameterReturnsZero()
    {
        $result = $this->calculator->add();
        $this->assertSame(0, $result, 'Empty string on add do not return 0');
    }

    public function testAddWithSingleNumberReturnsSameNumber()
    {
        $result = $this->calculator->add('3');
        $this->assertSame(3, $result, 'Add with single number do not returns same number');
    }

    public function testAddWithTwoParametersReturnsTheirSum()
    {
        $result = $this->calculator->add('2,4');

        $this->assertSame(6, $result, 'Add with two parameter do not returns correct sum');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function  testAddWithNonStringParameterThrowsException()
    {
        $this->calculator->add(5, 'Integer parameter do not throw error');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddWithNonNumbersThrowException()
    {
        $this->calculator->add('1,a', 'Invalid parameter do not throw exception');
    }

    /**
     * @dataProvider integerDataProvider
     */
    public function testAddWithMultipleParameters($param1, $param2)
    {
        $result = $this->calculator->add($param1);
        $this->assertSame($param2, $result, 'Add with two parameter do not returns correct sum');
    }

    public function integerDataProvider()
    {
        return array (
            array('1,2', 3),
            array('2\n3,6', 11),
            array('2:3,6', 11),
			array('2,3,6,10,1503', 21),
			array('', 0),
			array('4,7,3,4,7,3,5,6,7,4,3,2,5,7,5,3,4,6,7,8,9,5,5,5,4,3,2', 133),
			//array('\\;\\3;4;5', 12)
        );
    }
	
	/**
	 * @expectedException \InvalidArgumentException
	*/
	public function testAddWithNegativeNumbers()
	{
		$this->calculator->add('1,-2,-3', 'Invalid parameter do not throw exception');
	}
}