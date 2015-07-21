<?php
/**
 * Unittest für Corework\Model
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Corework;

use Corework\Model;

/**
 * Model test case.
 * 
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Corework\Model
	 * 
	 * @var Model
	 */
	private $Model;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		// TODO Auto-generated ModelTest::setUp()
		
		$this->Model = new Model(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		// TODO Auto-generated ModelTest::tearDown()
		
		$this->Model = null;
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Model->__call()
	 * 
	 * @covers Corework\Model::__call
	 * 
	 * @return void
	 */
	public function testCall()
	{
		/**
		 * @todo ModelTest: __call test not implemented
		 */
	
	}
	
	/**
	 * Tests Model->__construct()
	 * 
	 * @covers Corework\Model::__construct
	 * 
	 * @return void
	 */
	public function testConstruct()
	{
		/**
		 * @todo ModelTest: __construct test not implemented
		 */
	
	}
	
	/**
	 * Tests Model->setCreated()
	 * 
	 * @return void
	 */
	public function testSetCreated()
	{
		/**
		 * @todo ModelTest: setCreated test not implemented
		 */
	
	}
	
	/**
	 * Tests Model->setModified()
	 * 
	 * @return void
	 */
	public function testSetModified()
	{
		/**
		 * @todo ModelTest: setModified test not implemented
		 */
	
	}

}

