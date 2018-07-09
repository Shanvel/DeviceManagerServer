<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

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

    /**
     * getStats method
     *
     * @param CakePHP object employee
     * @return CakePHP object employee
     */
    public function getStats()
    {

        $connection = ConnectionManager::get('default');
        $results = $connection->execute('SELECT e.id, e.name, sum(timestampdiff(second, d.from_date, d.to_date)) as total_time FROM employees e inner join device_records d on e.id = d.emp_id where d.to_date IS NOT null GROUP BY e.id order BY total_time DESC LIMIT 3')->fetchAll('assoc');
        return $results;
    }

    /**
     * getAll method
     *
     * @param CakePHP object employee
     * @return CakePHP object employee
     */
    public function getAll()
    {
        $employee = TableRegistry::get('employees')->find('all'); 
        return $employee; 
    }

    /**
     * getById method
     *
     * @param id
     * @return CakePHP object employee
     */
    public function getById($id)
    {
        $connection = ConnectionManager::get('default');
        $employee = $connection->execute('SELECT e.id as employee_id, dr.id as record_id, d.id as device_id, d.name as device_name from employees e INNER JOIN device_records dr ON e.id = dr.emp_id INNER JOIN devices d on d.id = dr.device_id where dr.to_date IS Null AND e.id=:id',['id' => $id])->fetchAll('assoc');
        return $employee;
    }
}
