<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DeviceRecords Model
 *
 * @property \App\Model\Table\DevicesTable|\Cake\ORM\Association\BelongsTo $Devices
 * @property \App\Model\Table\EmployeesTable|\Cake\ORM\Association\BelongsTo $Employees
 *
 * @method \App\Model\Entity\DeviceRecord get($primaryKey, $options = [])
 * @method \App\Model\Entity\DeviceRecord newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DeviceRecord[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DeviceRecord|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DeviceRecord|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DeviceRecord patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DeviceRecord[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DeviceRecord findOrCreate($search, callable $callback = null, $options = [])
 */
class DeviceRecordsTable extends Table
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

        $this->setTable('device_records');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Devices', [
            'foreignKey' => 'device_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Employees', [
            'foreignKey' => 'emp_id',
            'joinType' => 'INNER'
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
            ->dateTime('from_date')
            ->requirePresence('from_date', 'create')
            ->notEmpty('from_date');

        $validator
            ->dateTime('to_date')
            ->requirePresence('to_date', 'create')
            ->notEmpty('to_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['device_id'], 'Devices'));
        $rules->add($rules->existsIn(['emp_id'], 'Employees'));

        return $rules;
    }

    // public function update($id)
    // {
    //     // echo "function";
    //     // $record=$this->find()->where(['device_id'=>$device_id, 'to_date IS NULL']);
    //     // $id =  $record->first()['id'];
    //     $records = $this->get($id);
    //     echo  DboSource::expression('NOW()');
    //     //$records->to_date =  DboSource::expression('NOW()');
    //     $this->save($records);
    // }
}
