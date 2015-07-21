<?php
/**
 * Unittest für Corework\Config
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Corework;

use Corework\Loader;

/**
 * Loader test case.
 * 
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Loader
	 * 
	 * @var Loader
	 */
	private $Loader;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->Loader = new Loader('unittest', '../../');
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		$this->Loader = null;
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// Do Nothing
	}
	
	/**
	 * Tests Loader->__construct()
	 * 
	 * @covers Corework\Loader::__construct
	 * @expectedException \PHPUnit_Framework_Error_Warning
	 * 
	 * @return void
	 */
	public function testConstructEXP()
	{
		$this->Loader = new Loader();
	}
	
	/**
	 * Tests Loader->register()
	 * 
	 * @return void
	 */
	public function testRegisterEXPTrue()
	{
		$this->assertTrue($this->Loader->register());
	
	}

	/**
	 * Tests Loader->_autoload
	 * 
	 * @covers Corework\Loader::_autoload
	 *
	 * @return void
	 */
	public function testAutoloadEXPLoadingClass()
	{
		$load = new Loader('tests', __DIR__.'/../..');
		$load->register();
		$test = new \tests\TestClasses\ClassTest();
		
		$this->assertInstanceOf('\tests\TestClasses\ClassTest', $test);
	
	}
	
	/**
	 * Tests Loader->_autoload
	 *
	 * @covers Corework\Loader::_autoload
	 *
	 * @return void
	 */
	public function testAutoloadEXPLoadingInterface()
	{
		$load = new Loader('tests', __DIR__.'/../..');
		$load->register();
		$test = new \tests\TestClasses\InterfaceClassTest();
	
		$this->assertInstanceOf('\tests\TestClasses\InterfaceClassTest', $test);
	
	}
	
	/**
	 * Tests Loader->_autoload
	 *
	 * @covers Corework\Loader::_autoload
	 * @expectedException \ErrorException
	 * 
	 * @return void
	 */
	public function testAutoloadEXPErrorException()
	{
		$load = new Loader('tests', __DIR__.'/../..', true);
		$load->register();
		$test = new \tests\TestClasses\InvalidClassName();
	}
}

