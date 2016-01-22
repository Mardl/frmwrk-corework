<?php

namespace Corework\Application\Interfaces;

/**
 * Class ModelsInterface
 *
 * @category Corework
 * @package  Corework\Application\Interfaces
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
/**
 * Interface ModelsInterface
 *
 * @package Corework\Application\Interfaces
 */
interface ModelsInterface {

    /**
     * @return mixed
     */
    public function getTableName();

    /**
     * @return mixed
     */
    public function getTablePrefix();

    /**
     * @return mixed
     */
    public function getIdField();

    /**
     * @return mixed
     */
    public function getDataRow();

    /**
     * @param array $data
     * @return mixed
     */
    public function setDataRow($data = []);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param string $datetime
     * @return mixed
     */
    public function setCreated($datetime = 'now');

    /**
     * @param string $datetime
     * @return mixed
     */
    public function setModified($datetime = 'now');

    /**
     * @param int $userId
     * @return mixed
     */
    public function setCreateduser_Id($userId = 0);

    /**
     * @param null $userId
     * @return mixed
     */
    public function setModifieduser_Id($userId = null);

    /**
     * @return mixed
     */
    public function resetRegisterChange();

    /**
     * @param array $data
     * @return mixed
     */
    public function clearDataRow($data = []);

    /** @param boolean $dateTimeToSave */
    public function setDateTimeToSave($dateTimeToSave);

    /** @return boolean */
    public function isDateTimeToSave();
}
