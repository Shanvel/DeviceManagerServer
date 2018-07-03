<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Device Entity
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $os
 * @property string $version
 *
 * @property \App\Model\Entity\DeviceRecord[] $device_records
 */
class Device extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */

    protected $_virtual = ['full_id'];


    protected function _getFullId(){
        //echo $this->_properties['type'];
        echo $this->DeviceRecord['to_date'];
        if($this->_properties['type'] == "Laptop")
        {
            return "ZRX-DEV-LAP-".$this->_properties['id'];
        }
        else if($this->_properties['type'] == "Mobile")
        {
            return "ZRX-DEV-MOB-".$this->_properties['id'];
        }
        else
        {
            return "ZRX-DEV-TAB-".$this->_properties['id'];
        }
    }


    protected $_accessible = [
        'id' => true,
        'name' => true,
        'type' => true,
        'os' => true,
        'version' => true,
        'device_records' => true
    ];
}
