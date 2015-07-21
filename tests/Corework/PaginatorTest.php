<?php
/**
 * Unittest für Corework\Paginator
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Corework;

use Corework\Paginator;

/**
 * Paginator test case.
 * 
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Corework\Paginator
	 * 
	 * @var Paginator
	 */
	private $Paginator;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		/**
		 * nothing todo
		 */
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		/**
		 * nothing todo
		 */
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		/**
		 * nothing todo
		 */
	}
	
	/**
	 * Tests Paginator->__construct()
	 * 
	 * @covers Corework\Paginator::__construct
	 * 
	 * @expectedException \PHPUnit_Framework_Error_Warning
	 * 
	 * @return void
	 */
	public function testConstructEXPExceptionNoParameters()
	{
		$this->Paginator = new Paginator();
	}
	
	/**
	 * Tests Paginator->__construct()
	 *
	 * @covers Corework\Paginator::__construct
	 *
	 * @return void
	 */
	public function testConstructEXPNoError()
	{
		$this->Paginator = new Paginator(30);
	}
	
	/**
	 * Tests Paginator->getLimit()
	 * 
	 * @return void
	 */
	public function testGetLimitEXPStandardLimit25()
	{
		$this->Paginator = new Paginator(30);
		$this->assertEquals(25, $this->Paginator->getLimit());
	}
	
	/**
	 * Tests Paginator->getLimit()
	 *
	 * @return void
	 */
	public function testGetLimitEXPNoStandardLimit25BecauseSetLimit30()
	{
		$this->Paginator = new Paginator(30, 0, 30);
		$this->assertNotEquals(25, $this->Paginator->getLimit());
	}
	
	/**
	 * Tests Paginator->getLimit()
	 *
	 * @return void
	 */
	public function testGetLimitEXP30BecauseSetLimit30()
	{
		$this->Paginator = new Paginator(30, 0, 30);
		$this->assertEquals(30, $this->Paginator->getLimit());
	}
	
	/**
	 * Tests Paginator->getOffset()
	 * 
	 * @return void
	 */
	public function testGetOffsetEXP0BecauseFirstPage()
	{
		$this->Paginator = new Paginator(10, 0, 30);
	
	}
	
	/**
	 * Tests Paginator->getOffset()
	 *
	 * @return void
	 */
	public function testGetOffsetEXP25BecauseSecondPageAndStandardLimit()
	{
		$this->Paginator = new Paginator(10, 1);
		$this->assertEquals(25, $this->Paginator->getOffset());
	
	}
	
	/**
	 * Tests Paginator->__toString()
	 * 
	 * @covers Corework\Paginator::__toString
	 * 
	 * @return void
	 */
	public function testToStringEXPEmptyString()
	{
		$this->Paginator = new Paginator(10, 0, 30);
		$this->assertEquals('', $this->Paginator->__toString());
	}
	
}

