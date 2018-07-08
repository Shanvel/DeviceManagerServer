<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Devices Model
 *
 * @property \App\Model\Table\DeviceRecordsTable|\Cake\ORM\Association\HasMany $DeviceRecords
 *
 * @method \App\Model\Entity\Device get($primaryKey, $options = [])
 * @method \App\Model\Entity\Device newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Device[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Device|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Device|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Device patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Device[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Device findOrCreate($search, callable $callback = null, $options = [])
 */
class DevicesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('devices');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('DeviceRecords', [
            'foreignKey' => 'device_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('type')
            ->maxLength('type', 100)
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->scalar('os')
            ->maxLength('os', 100)
            ->requirePresence('os', 'create')
            ->notEmpty('os');

        $validator
            ->scalar('version')
            ->maxLength('version', 10)
            ->requirePresence('version', 'create')
            ->notEmpty('version');

        return $validator;
    }
    /**
     * getById method
     *
     * @param CakePHP object device
     * @return CakePHP object device
     */
    public function getById($device)
    {
        foreach($device->device_records as $data){
            if($data['to_date'] == null){
                $device->status = "Not Available";
                break;
            }
            else{
                $device->status="Available";
            }
        }
        foreach($device->device_records as $data){
            $employees = TableRegistry::get('Employees');
            $data['emp_id'] = $employees->get($data['emp_id'])['name'];
        }
        return $device;
    }

    /**
     * getStats method
     *
     * @param CakePHP object device
     * @return CakePHP object device
     */
    public function getStats($device)
    {
        $first = null;
        $second = null;
        $third = null;
        foreach($device as $data){
            if($first == null){
                $first = $data;
            }
            else if($data->total_time > $first->total_time){
                $third = $second;
                $second = $first;
                $first = $data;
            }
            else if($second==null || $data->total_time > $second->total_time){
                $third = $second;
                $second = $data;
            }
            else if($third==null || $data->total_time > $third->total_time){
                $third = $data;
            }
        }
        $results = [];
        array_push($results, $first);
        array_push($results, $second);
        array_push($results, $third);
        return $results;
    }

    /**
     * getAvailable method
     *
     * @param CakePHP object device
     * @return CakePHP object device
     */
    public function getAvailable($device)
    {
        $output = array();
        foreach($device as $data){
            if($data->device_records == "Available"){
                $temp = array();
                array_push($temp, $data->id);
                array_push($temp, $data->full_id);
                array_push($output,$temp);
            }
        }
        return $output;
    }

    /**
     * getAll method
     *
     * @param CakePHP object device
     * @return CakePHP object device
     */
    public function getAll()
    {
        $device = TableRegistry::get('devices')->find('all')->contain(['DeviceRecords']);
        
        foreach($device as $data){
            $total_time = 0;
            foreach($data->device_records as $item){
                if($item['to_date'] ==null){
                    $employees = TableRegistry::get('Employees');
                    $data->device_records = "Taken By ".$employees->get($item['emp_id'])['name'];
                }
                else{
                    $data->device_records = "Available";
                    $from_date = $item['from_date'];
                    $to_date = $item['to_date'];
                    $diff = $from_date->diff($to_date);
                    $formatted = $diff->s + $diff->i*60 + $diff->h*3600 + $diff->days*24*3600;
                }
                $total_time = $total_time + $formatted;
                $data->total_time = $total_time;
            }
        }
        return $device;
    }

}
