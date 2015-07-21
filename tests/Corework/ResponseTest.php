<?php
/**
 * Unittest für Corework\Response
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Corework;

use Corework\Response;

/**
 * Response test case.
 * 
 * @category Unittest
 * @package  Corework
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Corework\Response
	 * 
	 * @var Response
	 */
	private $Response;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Response->redirect()
	 * 
	 * Im CLI-Modus functioniert der Redirect nicht
	 * 
	 * @return void
	 */
	public function testRedirect()
	{
		$this->markTestSkipped('Test funktioniert nicht im CLI');
	}

}

