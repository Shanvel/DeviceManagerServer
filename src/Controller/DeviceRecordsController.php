<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

/**
 * DeviceRecords Controller
 *
 * @property \App\Model\Table\DeviceRecordsTable $DeviceRecords
 *
 * @method \App\Model\Entity\DeviceRecord[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DeviceRecordsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    
    public function index()
    {
        $this->paginate = [
            'contain' => ['Devices', 'Employees']
        ];
        $deviceRecords = $this->paginate($this->DeviceRecords);

        $this->set(compact('deviceRecords'));
        $this->set('_serialize', true);
    }

    /**
     * View method
     *
     * @param string|null $id Device Record id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $deviceRecord = $this->DeviceRecords->get($id, [
            'contain' => ['Devices', 'Employees']
        ]);

        $this->set('deviceRecord', $deviceRecord);
        $this->set('_serialize', true);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $deviceRecord = $this->DeviceRecords->newEntity();
        echo "inside function";
        if ($this->request->is('post')) {
            $deviceRecord = $this->DeviceRecords->patchEntity($deviceRecord, $this->request->getData('deviceRecord'), ['validate'=>false]);
            if ($this->DeviceRecords->save($deviceRecord)) {
                $this->Flash->success(__('The device record has been saved.'));

                //return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The device record could not be saved. Please, try again.'));
        }
        $devices = $this->DeviceRecords->Devices->find('list', ['limit' => 200]);
        $employees = $this->DeviceRecords->Employees->find('list', ['limit' => 200]);
        $this->set(compact('deviceRecord', 'devices', 'employees'));
        
    }

    /**
     * Edit method
     *
     * @param string|null $id Device Record id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $deviceRecord = $this->DeviceRecords->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $deviceRecord = $this->DeviceRecords->patchEntity($deviceRecord, $this->request->getData('deviceRecord'));
            if ($this->DeviceRecords->save($deviceRecord)) {
                $this->Flash->success(__('The device record has been saved.'));

                //return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The device record could not be saved. Please, try again.'));
        }
        $devices = $this->DeviceRecords->Devices->find('list', ['limit' => 200]);
        $employees = $this->DeviceRecords->Employees->find('list', ['limit' => 200]);
        $this->set(compact('deviceRecord', 'devices', 'employees'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Device Record id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $deviceRecord = $this->DeviceRecords->get($id);
        if ($this->DeviceRecords->delete($deviceRecord)) {
            $this->Flash->success(__('The device record has been deleted.'));
        } else {
            $this->Flash->error(__('The device record could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
