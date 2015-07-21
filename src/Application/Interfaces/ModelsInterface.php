<?php

namespace Corework\Application\Interfaces;

/**
 * Class ModelsInterface
 *
 * @category Corework
 * @package  Corework\Application\Interfaces
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface ModelsInterface
{

	public function getTableName();

	public function getTablePrefix();

	public function getIdField();

	public function getDataRow();

	public function setDataRow($data = array());

	public function getId();

	public function setId($id);

	public function setCreated($datetime = 'now');

	public function setModified($datetime = 'now');

	public function setCreateduser_Id($userId = 0);

	public function setModifieduser_Id($userId = null);

	public function resetRegisterChange();

	public function clearDataRow($data = array());
}
