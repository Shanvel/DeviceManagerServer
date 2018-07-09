<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

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
    public function getStats()
    {
        $connection = ConnectionManager::get('default');
        $results = $connection->execute('SELECT d.id, d.name, sum(timestampdiff(second, dr.from_date, dr.to_date)) as total_time FROM devices d inner join device_records dr on d.id = dr.device_id where dr.to_date IS NOT null GROUP BY d.id order BY total_time DESC LIMIT 3')->fetchAll('assoc');
        return $results;
    }

    /**
     * getAvailable method
     *
     * @param CakePHP object device
     * @return CakePHP object device
     */
    public function getAvailable()
    {
        $connection = ConnectionManager::get('default');
        $output = $connection->execute('SELECT d.id, d.name from devices d inner join device_records dr on d.id = dr.device_id where d.id not in (SELECT dr.device_id FROM device_records dr INNER join devices d on d.id = dr.device_id where to_date is null)') ->fetchAll('assoc');
        return $output;
    }

    /**
     * getAll method
     *
     * @param void
     * @return CakePHP object device
     */
    public function getAll()
    {
        $device = TableRegistry::get('devices')->find('all')->contain(['DeviceRecords']);
        
        foreach($device as $data){
            foreach($data->device_records as $item){
                if($item['to_date'] ==null){
                    $employees = TableRegistry::get('Employees');
                    $data->device_records = "Taken By ".$employees->get($item['emp_id'])['name'];
                }
                else{
                    $data->device_records = "Available";
            }
        }
        return $device;
    }

    public function getDevices($show, $available)
    {
        $device = $this->getAll();
        if($show == null && $available == null)
        {
            return $device;
        }
        else if($show!=null)
        {
            return $this->getStats($device);
        }
        else if($available!=null)
        {
            return $this->getAvailable();
        }
    }

}
