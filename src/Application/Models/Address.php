<?php

namespace Corework\Application\Models;

use Corework\Model as BaseModel;

/**
 * Class Address
 *
 * @category Corework
 * @package  Corework\Application\Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class Address extends BaseModel
{

	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Street
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true)
	 */
	protected $street;

	/**
	 * City
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true)
	 */
	protected $city;

	/**
	 * Zipcode
	 *
	 * @var string
	 *
	 * @Column(type="string", length=16, nullable=true)
	 */
	protected $zipcode;

	/**
	 * Province
	 *
	 * @var string
	 *
	 * @Column(type="string", length=32, nullable=true)
	 */
	protected $province;

	/**
	 * User
	 *
	 * @var \Corework\Application\Models\User
	 *
	 * @OneToOne(targetEntity="App\Models\User", fetch="LAZY", inversedBy="address")
	 */
	protected $user;
}
