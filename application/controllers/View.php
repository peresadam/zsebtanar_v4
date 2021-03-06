<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class View extends CI_controller {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct() {

		parent::__construct();

		// Load models
		$this->load->helper('url');
		$this->load->model('Html');
		$this->load->model('Exercises');
		$this->load->model('Session');
		$this->load->model('Statistics');

		// Write statistics of website content
		$this->Statistics->Write('resources/statistics.xlsx');

		// Set session ID
		$this->Session->setSessionID();
	}

	/**
	 * View subtopic
	 *
	 * @param  int $id Subtopic id
	 * @return	void
	 */
	public function Subtopic($id=NULL) {

		$this->load->view('Template');
		$this->Session->setSessionID();

		if ($id) {
			$this->session->set_userdata('method', 'subtopic');
			$this->session->set_userdata('goal', $id);
			$this->session->set_userdata('todo_list', []);
		} else {
			$this->session->unset_userdata('method');
			$this->session->unset_userdata('goal');
			$this->session->unset_userdata('todo_list');
		}

		$type = 'subtopic';

		$this->Session->recordAction($id, $type);

		$data = $this->Html->NavBarMenu($id, $type);
		$this->load->view('NavBar', $data);

		$data = $this->Html->Title($id, $type);
		$data['id_next'] = $this->Exercises->IDNextSubtopic($id);
		$data['id'] = $id;
		$this->load->view('Title', $data);

		if (!$id) {
			$this->load->view('Search');
		} else {
			$data = $this->Exercises->getExerciseList($id);
			$this->load->view('ExerciseList', $data);		
		}

		$this->load->view('Footer');

		$this->Session->PrintInfo();

	}

	public function Exercise($id=1, $level=NULL) {

		if (NULL === $this->session->userdata('method')) {
			$this->session->set_userdata('method', 'exercise');
			$this->session->set_userdata('goal', $id);
			$this->session->set_userdata('todo_list', []);
		}

		$this->Session->UpdateTodoList($id);

		$type = 'exercise';

		$this->load->view('Template');

		if (!$level) {
			$level = $this->Session->getExerciseLevelNext($id);
		}

		$this->Session->recordAction($id, $type, $level);

		$data = $this->Html->NavBarMenu($id, $type);
		$this->load->view('NavBar', $data);


		$data = $this->Html->Title($id, $type);
		$this->load->view('Title', $data);


		$data = $this->Exercises->getExerciseData($id, $level);
		$this->load->view('Exercise', $data);

		$this->load->view('Footer');

		$this->Session->PrintInfo();

	}

	public function Session($type='database', $id=NULL) {

		$this->load->view('Template');

		if ($type == 'database') {
			$data = $this->Session->getActions($id);
		} elseif ($type == 'import') {
			$data = $this->Session->getSavedSessions($id);
		}

		$this->load->view('Session', $data);
		$this->load->view('Footer');

	}
}

?>