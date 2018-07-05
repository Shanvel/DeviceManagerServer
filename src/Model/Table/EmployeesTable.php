<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Employees Model
 *
 * @method \App\Model\Entity\Employee get($primaryKey, $options = [])
 * @method \App\Model\Entity\Employee newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Employee[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Employee|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Employee|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Employee patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Employee[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Employee findOrCreate($search, callable $callback = null, $options = [])
 */
class EmployeesTable extends Table
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

        $this->setTable('employees');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('DeviceRecords', [
            'foreignKey' => 'emp_id',
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
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        return $validator;
    }
    public function displayStats($employee)
    {
        $first = null;
        $second = null;
        $third = null;
        foreach($employee as $data)
        {
            if($first == null)
            {
                $first = $data;
                //echo $first->id;
            }
            else if($data->total_time > $first->total_time)
            {
                $third = $second;
                $second = $first;
                $first = $data;
            }
            else if($second==null || $data->total_time > $second->total_time)
            {
                $third = $second;
                $second = $data;
            }
            else if($third==null || $data->total_time > $third->total_time)
            {
                $third = $data;
            }
        }
        $results = [];
        array_push($results, $first);
        array_push($results, $second);
        array_push($results, $third);
        return $results;
    }
    public function displayAll()
    {
        $employee = TableRegistry::get('employees')->find('all')->contain(['DeviceRecords']);
        
        foreach($employee as $data)
        {
            //echo $data->device_records[0]['to_date'];
            $total_time = 0;
            foreach($data->device_records as $item)
            {
                if($item['to_date']!=null)
                {
                    $from_date = $item['from_date'];
                    $to_date = $item['to_date'];
                    $diff = $from_date->diff($to_date);
                    $formatted = $diff->s + $diff->i*60 + $diff->h*3600 + $diff->days*24*3600;
                    $total_time = $total_time + $formatted;  
                    $data->total_time = $total_time;
                }
            }
            foreach($data->device_records as $item)
            {
                if($item['to_date']==null)
                {
                    //echo $item['device_id'];
                    $data->device_records = $item['device_id'];
                    break;
                }
                else
                {
                    $data->device_records = 0;
                }
            }
            
        }
        //echo $first->id;
        //echo $second->id;
        // $data->rank[0] = $first;
        // $data->rank[1] = $second;
        // $data->rank[2] = $third;
        return $employee;
        //$this->set(compact('employees'));
        
    }
    public function displayById($employee)
    {
        $takenDevices = array();
        foreach($employee->device_records as $item)
        {
            if($item['to_date'] == null) {
                //array_push($takenDevices, $item['device_id']);
                $devices = TableRegistry::get('Devices');
                array_push($takenDevices, $devices->get($item['device_id'])['full_id']);
            }
        }
        return $takenDevices;
    }
}
