<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;


/**
 * Devices Controller
 *
 * @property \App\Model\Table\DevicesTable $Devices
 *
 * @method \App\Model\Entity\Device[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DevicesController extends AppController
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
        // $devices = $this->paginate($this->Devices);

        // $this->set(compact('devices'));
        // $this->set('_serialize', true);
        $filter = $this->request->getQuery('filter');
        $device = TableRegistry::get('devices')->find('all')->contain(['DeviceRecords']);
        $first = null;
        $second = null;
        $third = null;
        foreach($device as $data)
        {
            //echo $data->device_records[0]['to_date'];
            $total_time = 0;
            foreach($data->device_records as $item)
            {
                if($item['to_date']==null)
                {
                    $employees = TableRegistry::get('Employees');
                    $data->device_records = "Taken By ".$employees->get($item['emp_id'])['name'];
                }
                else
                {
                    $data->device_records = "Available";
                    $from_date = $item['from_date'];
                    $to_date = $item['to_date'];
                    $diff = $from_date->diff($to_date);
                    $formatted = $diff->s + $diff->i*60 + $diff->h*3600 + $diff->days*24*3600;
                    //$formatted = sprintf('%02d:%02d:%02d', ($diff->days * 24) + $diff->h, $diff->i, $diff->s);
                    //echo $formatted;
                }
                $total_time = $total_time + $formatted;
                $data->total_time = $total_time;
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

            // if($data['type'] == "Laptop")
            // {
            //     $data['id'] = "ZRX-DEV-LAP-".$data['id'];
            // }
            // else if($data['type'] == "Mobile")
            // {
            //     $data['id'] = "ZRX-DEV-MOB-".$data['id'];
            // }
            // else
            // {
            //     $data['id'] = "ZRX-DEV-TAB-".$data['id'];
            // }
            
        }
        echo $second->id;
        $results = [];
        array_push($results, $first);
        array_push($results, $second);
        array_push($results, $third);
        if($filter['show'] == null)
        {
            $this->set('device', $device);
        }
        else 
        {
            $this->set('device', $results);
        }
        //$this->set('device', $results);//final stats page
        //echo "88888888888888888888".$device->first()->device_records[0]['to_date']."7777777777777777";
        $this->set('_serialize', true);
    }

    /**
     * View method
     *
     * @param string|null $id Device id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $device=$this->Devices->get($id, [
            'contain' => ['DeviceRecords']
        ]);
        foreach($device->device_records as $data)
        {
            if($data['to_date'] == null)
            {
                $device->status = "Not Available";
                break;
            }
            else
            {
                $device->status="Available";
            }
        }
        foreach($device->device_records as $data)
        {
            $employees = TableRegistry::get('Employees');
            $data['emp_id'] = $employees->get($data['emp_id'])['name'];
        }
        // foreach($device->device_records as $data)
        // {
        //     if($data['to_date']==null)
        //     {
        //         echo "eureka".$data['id'];
        //     }
        // }
        //echo "8888888888888888888888888888888888".$device['device_records'][0]['id']."7777777777777777777777777777777777777777";
        $this->set('device', $device);
        $this->set('_serialize', true);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $device = $this->Devices->newEntity();
        if ($this->request->is('post')) {
            $device = $this->Devices->patchEntity($device, $this->request->getData());
            if ($this->Devices->save($device)) {
                $this->Flash->success(__('The device has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The device could not be saved. Please, try again.'));
        }
        $this->set(compact('device'));
        $this->set('_serialize', true);
    }

    /**
     * Edit method
     *
     * @param string|null $id Device id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $device = $this->Devices->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $device = $this->Devices->patchEntity($device, $this->request->getData());
            if ($this->Devices->save($device)) {
                $this->Flash->success(__('The device has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The device could not be saved. Please, try again.'));
        }
        $this->set(compact('device'));
        $this->set('_serialize', true);
    }

    /**
     * Delete method
     *
     * @param string|null $id Device id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $device = $this->Devices->get($id);
        if ($this->Devices->delete($device)) {
            $this->Flash->success(__('The device has been deleted.'));
        } else {
            $this->Flash->error(__('The device could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
