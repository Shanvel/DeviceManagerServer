<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DeviceRecord Entity
 *
 * @property int $id
 * @property int $device_id
 * @property \Cake\I18n\FrozenTime $from_date
 * @property \Cake\I18n\FrozenTime $to_date
 * @property int $emp_id
 *
 * @property \App\Model\Entity\Device $device
 * @property \App\Model\Entity\Employee $employee
 */
class DeviceRecord extends Entity
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

    
    protected $_accessible = [
        'id' => true,
        'device_id' => true,
        'from_date' => true,
        'to_date' => true,
        'emp_id' => true,
        'device' => true,
        'employee' => true
    ];
}
