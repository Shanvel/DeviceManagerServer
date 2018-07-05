<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

/**
 * Employees Controller
 *
 * @property \App\Model\Table\EmployeesTable $Employees
 *
 * @method \App\Model\Entity\Employee[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EmployeesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function beforeRender(event $event) {
        $this->setCorsHeaders();
    }

    public function beforeFilter(event $event) {
        if ($this->request->is('options')) {
            $this->response->header('Access-Control-Allow-Origin', '*');
            $this->setCorsHeaders();
            return $this->response;
        }
    }

    private function setCorsHeaders() {
        $this->response->cors($this->request)
            ->allowOrigin(['*'])
            ->allowMethods(['*'])
            ->allowHeaders(['x-xsrf-token', 'Origin', 'Content-Type', 'X-Auth-Token'])
            ->allowCredentials(['true'])
            ->exposeHeaders(['Link'])
            ->maxAge(300)
            ->build();
    }
    public function index()
    {
        //$employees = $this->paginate($this->Employees);
        $filter = $this->request->getQuery('filter');
        $employee = TableRegistry::get('employees')->find('all')->contain(['DeviceRecords']);
        $first = null;
        $second = null;
        $third = null;
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
        //echo $first->id;
        //echo $second->id;
        $results = [];
        array_push($results, $first);
        array_push($results, $second);
        array_push($results, $third);
        // $data->rank[0] = $first;
        // $data->rank[1] = $second;
        // $data->rank[2] = $third;
        //$this->set(compact('employees'));
        if($filter['show'] == null)
        {
            $this->set('employee', $employee);
        }
        else 
        {
            $this->set('employee', $results);
        }
        //display for everything else
        //$this->set('employee', $results);//final stats page
        $this->set('_serialize', true);
    }

    /**
     * View method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $employee = $this->Employees->get($id, ['contain' => ['DeviceRecords']]);
        $flag=0;
        $takenDevices = array();
        foreach($employee->device_records as $item)
        {
            if($item['to_date'] == null) {
                //array_push($takenDevices, $item['device_id']);
                $devices = TableRegistry::get('Devices');
                array_push($takenDevices, $devices->get($item['device_id'])['full_id']);
            }
        }
        $employee = $this->Employees->get($id);
        $employee->devices = $takenDevices;
        $this->set('employee', $employee);
        $this->set('_serialize', true);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $employee = $this->Employees->newEntity();
        if ($this->request->is('post')) {
            $employee = $this->Employees->patchEntity($employee, $this->request->getData());
            if ($this->Employees->save($employee)) {
                $this->Flash->success(__('The employee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The employee could not be saved. Please, try again.'));
        }
        $this->set(compact('employee'));
        $this->set('_serialize', true);
    }

    /**
     * Edit method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $employee = $this->Employees->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'put'])) {
            $employee = $this->Employees->patchEntity($employee, $this->request->getData());
            if ($this->Employees->save($employee)) {
                $this->Flash->success(__('The employee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The employee could not be saved. Please, try again.'));
        }
        $this->set(compact('employee'));
        $this->set('_serialize', true);
    }

    /**
     * Delete method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $employee = $this->Employees->get($id);
        if ($this->Employees->delete($employee)) {
            $this->Flash->success(__('The employee has been deleted.'));
        } else {
            $this->Flash->error(__('The employee could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
