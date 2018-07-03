<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DeviceRecordsFixture
 *
 */
class DeviceRecordsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'device_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'from_date' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'to_date' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'emp_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'emp_id' => ['type' => 'index', 'columns' => ['emp_id'], 'length' => []],
            'device_id' => ['type' => 'index', 'columns' => ['device_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'device_records_ibfk_1' => ['type' => 'foreign', 'columns' => ['emp_id'], 'references' => ['employees', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'device_records_ibfk_2' => ['type' => 'foreign', 'columns' => ['device_id'], 'references' => ['devices', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'device_id' => 1,
                'from_date' => '2018-06-27 08:04:40',
                'to_date' => '2018-06-27 08:04:40',
                'emp_id' => 1
            ],
        ];
        parent::init();
    }
}
