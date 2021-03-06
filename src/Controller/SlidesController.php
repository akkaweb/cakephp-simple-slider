<?php
namespace SimpleSlider\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use SimpleSlider\Controller\AppController;

/**
 * Slides Controller
 *
 * @property \SimpleSlider\Model\Table\SlidesTable $Slides
 */
class SlidesController extends AppController
{

    /**
     * Index method
     *
     * @param mixed $sliderId Slider ID
     * @return \Cake\Network\Response|void
     */
    public function index($sliderId = null)
    {
        $exists = $this->Slides->Sliders->exists(['id' => $sliderId]);
        if (!$exists) {
            throw new RecordNotFoundException(__('Cannot find the provided slider'));
        }
        $this->paginate = [
            'contain' => ['Sliders']
        ];
        $query = $this->Slides
            ->find('all', [
                'contain' => [
                    'SlideImages' => [
                        'sort' => [
                            'created' => 'DESC'
                        ]
                    ]
                ]
            ])
            ->where(['slider_id' => $sliderId]);

        if ($query->isEmpty()) {
            $this->Flash->set(__('No slides were found. Please add one.'));
            return $this->redirect(['action' => 'add', $sliderId]);
        }
        $slides = $this->paginate($query);

        $this->set(compact('slides'));
        $this->set('_serialize', ['slides']);
    }

    /**
     * Add method
     *
     * @param mixed $sliderId Slider ID
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add($sliderId = null)
    {
        $slide = $this->Slides->newEntity();
        if ($this->request->is('post')) {
            $sliderId = $this->request->data['slider_id'];
            $slide = $this->Slides->patchEntity($slide, $this->request->data);
            if ($this->Slides->save($slide)) {
                $this->Flash->success(__('The slide has been saved.'));
                if ($this->_upload($slide->get('id'))) {
                    return $this->redirect(['action' => 'index', $sliderId]);
                }
                $this->Flash->error(__('Failed to upload image'));
            } else {
                $this->Flash->error(__('The slide could not be saved. Please, try again.'));
            }
        }
        $exists = $this->Slides->Sliders->exists(['id' => $sliderId]);
        if (!$exists) {
            throw new RecordNotFoundException(__('Cannot find the provided slider'));
        }
        $this->set(compact('slide', 'sliderId'));
        $this->set('_serialize', ['slide']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Slide id.
     * @param mixed $sliderId Slider ID
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $sliderId = null)
    {
        $slide = $this->Slides->get($id, [
            'contain' => [
                'SlideImages' => [
                    'sort' => [
                        'created' => 'DESC'
                    ]
                ]
            ]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sliderId = $this->request->data['slider_id'];
            $slide = $this->Slides->patchEntity($slide, $this->request->data);
            if ($this->Slides->save($slide)) {
                $this->Flash->success(__('The slide has been saved.'));
                if ($this->request->data['file'] && $this->_upload($slide->get('id'))) {
                    return $this->redirect(['action' => 'index', $sliderId]);
                }
            } else {
                $this->Flash->error(__('The slide could not be saved. Please, try again.'));
            }
        }
        $exists = $this->Slides->Sliders->exists(['id' => $sliderId]);
        if (!$exists) {
            throw new RecordNotFoundException(__('Cannot find the provided slider'));
        }
        $this->set(compact('slide', 'sliderId'));
        $this->set('_serialize', ['slide']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Slide id.
     * @param mixed $sliderId Slider ID
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null, $sliderId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $slide = $this->Slides->get($id);
        if ($this->Slides->delete($slide)) {
            $this->Flash->success(__('The slide has been deleted.'));
        } else {
            $this->Flash->error(__('The slide could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index', $sliderId]);
    }

    /**
     * Uploads and stores the related file.
     *
     * @param  int|null $slideId id of the relate slide
     * @return bool           flag
     */
    protected function _upload($slideId = null)
    {
        $entity = $this->Slides->SlideImages->newEntity();
        $entity = $this->Slides->SlideImages->patchEntity(
            $entity,
            $this->request->data
        );
        if ($this->Slides->SlideImages->uploadImage($slideId, $entity)) {
            $this->Flash->set(__('Upload successful'));
            return true;
        }

        return false;
    }
}
