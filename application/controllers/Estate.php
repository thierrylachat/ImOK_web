<?php

defined('BASEPATH') OR exist('No direct script access allowed');

class Estate extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Build_date_model', 'City_model', 'District_model',
			'Estate_model', 'Estate_types_model', 'Exposition_model', 'Heating_types_model',
			'Outside_conditions_model', 'Furniture_model', 'Room_type_model', 'Windows_type_model',
			'Ground_covering_model', 'Wall_covering_model', 'Heating_type_model', 'Customer_model']);
		$this->load->helper(['url', 'url_helper', 'form', 'date', 'directory']);
		$this->load->library(['form_validation']);
	}
	public function index()
	{
		$data['title'] = 'Accueil de biens';
		$data['estateList'] = $this->Estate_model->getAllEstates();

		// Chargement des vues, avec envoi du tableau $data
		$this->load->view('common/_header', $data);
		$this->load->view('estate/index', $data);
		$this->load->view('common/_footer', $data);
	}
	public function create()
	{
		$data = $this->load_estate_componenents();

		$data['title'] = 'Ajout d\'un bien';
		// S'il n'a pas d'erreurs
		if ($this->form_validation->run() === TRUE) {
			// On crée le bien
			$this->Estate_model->createEstate();
			// On récupère son id
			$lastId = $this->db->insert_id();
			$this->uploadImage($lastId);
			redirect(base_url('index.php/estate/details/'.$lastId));
		}

		// Chargement des vues, avec envoi du tableau $data
		$this->load->view('common/_header', $data);
		$this->load->view('estate/create', $data);
		$this->load->view('common/_footer', $data);
	}

	public function search()
	{
		$term = $this->input->get('term');
		$this->db->like('name', $term);
		$this->db->or_like('zip_code', $term);
		$data = $this->db->get('cities')->result();

		echo json_encode($data);
	}

	public function details($id)
	{
		$data['title'] = 'Details bien';
		$data['estate'] = $this->Estate_model->getEstates($id);

		$path = 'upload/img/estate/'.$id;
		$map = directory_map($path);

		$this->load->view('common/_header', $data);
		$this->load->view('estate/details', $data);
		$this->load->view('common/_footer', $data);
	}

	public function edit($id)
	{
		$data = $this->load_estate_componenents();
		$data['title'] = 'Modification d\'un bien';

		$data['estate'] = $this->Estate_model->getEstates($id);

		$this->load->view('common/_header', $data);
		$this->load->view('estate/edit', $data);
		$this->load->view('common/_footer', $data);
	}

	public function uploadImage($id)
	{
		// On compte le nombre de photo
		$count = count($_FILES['image-upload']['name']);
		// S'il n'existe pas, on crée le dossier
		if (!is_dir('upload/img/estate/'.$id)) {
			mkdir('./upload/img/estate/' . $id, 0775, TRUE);
		}

		for($i=0;$i<$count;$i++)
		{
			if(!empty($_FILES['image-upload']['name'][$i]))
			{
				$_FILES['file']['name'] = $_FILES['image-upload']['name'][$i];
				$_FILES['file']['type'] = $_FILES['image-upload']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['image-upload']['tmp_name'][$i];
				$_FILES['file']['error'] = $_FILES['image-upload']['error'][$i];
				$_FILES['file']['size'] = $_FILES['image-upload']['size'][$i];
				// Config de l'upload
				$config['upload_path'] = 'upload/img/estate/'.$id;
				$config['allowed_types'] = 'jpg|jpeg|png|gif';
				$config['max_size'] = '2048';
				$config['file_name'] = $_FILES['image-upload']['name'][$i];

				$this->load->library('upload',$config);

				$this->upload->do_upload('file');

			}
		}
	}

	public function load_estate_componenents()
	{
		$data['dates']	= $this->Build_date_model->getAll();
		$data['furnituresList'] = $this->Furniture_model->getAll();
		$data['roomTypeList'] = $this->Room_type_model->getAll();
		$data['windowsTypeList'] = $this->Windows_type_model->getAll();
		$data['groundCoveringsList'] = $this->Ground_covering_model->getAll();
		$data['wallCoveringsList'] = $this->Wall_covering_model->getAll();
		$data['heatingTypesList'] = $this->Heating_type_model->getAll();
		$data['outsideConditionList'] = $this->Outside_conditions_model->getAll();
		$data['customerList'] = $this->Customer_model->getCustomers();
		$data['estateTypeList'] = $this->Estate_types_model->getAll();
		$data['expositionsList'] = $this->Exposition_model->getAll();

		return $data;
	}
}
