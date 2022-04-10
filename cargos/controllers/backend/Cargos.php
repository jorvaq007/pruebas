<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Cargos Controller
*| --------------------------------------------------------------------------
*| Cargos site
*|
*/
class Cargos extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_cargos');
		$this->load->model('group/model_group');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Cargoss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('cargos_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['cargoss'] = $this->model_cargos->get($filter, $field, $this->limit_page, $offset);
		$this->data['cargos_counts'] = $this->model_cargos->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/cargos/index/',
			'total_rows'   => $this->data['cargos_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Cargos List');
		$this->render('backend/standart/administrator/cargos/cargos_list', $this->data);
	}
	
	/**
	* Add new cargoss
	*
	*/
	public function add()
	{
		$this->is_allowed('cargos_add');

		$this->template->title('Cargos New');
		$this->render('backend/standart/administrator/cargos/cargos_add', $this->data);
	}

	/**
	* Add New Cargoss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('cargos_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('cargo', 'Cargo', 'trim|required|max_length[150]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'cargo' => $this->input->post('cargo'),
			];

			
			
			$save_cargos = $this->model_cargos->store($save_data);
            

			if ($save_cargos) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_cargos;
					$this->data['message'] = cclang('success_save_data_stay',
					    
					    [
						anchor('administrator/cargos/edit/' . $save_cargos, 'Editar Cargos'),
						anchor('administrator/cargos', ' Volver a la lista')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/cargos/edit/' . $save_cargos, 'Editar Cargos')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/cargos');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/cargos');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validaci&oacute;n fallada';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		$this->response($this->data);
	}
	
		/**
	* Update view Cargoss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('cargos_update');

		$this->data['cargos'] = $this->model_cargos->find($id);

		$this->template->title('Cargos Update');
		$this->render('backend/standart/administrator/cargos/cargos_update', $this->data);
	}

	/**
	* Update Cargoss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('cargos_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
				$this->form_validation->set_rules('cargo', 'Cargo', 'trim|required|max_length[150]');
		

		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'cargo' => $this->input->post('cargo'),
			];


			
			
			$save_cargos = $this->model_cargos->change($id, $save_data);

			if ($save_cargos) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/cargos', ' Volver a la lista')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/cargos');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/cargos');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validaci&oacute;n fallada';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		$this->response($this->data);
	}
	
	/**
	* delete Cargoss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('cargos_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'cargos'), 'success');
        } else {
            set_message(cclang('error_delete', 'cargos'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Cargoss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('cargos_view');

		$this->data['cargos'] = $this->model_cargos->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Cargos Detail');
		$this->render('backend/standart/administrator/cargos/cargos_view', $this->data);
	}
	
	/**
	* delete Cargoss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$cargos = $this->model_cargos->find($id);

		
		
		return $this->model_cargos->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('cargos_export');

		$this->model_cargos->export(
			'cargos', 
			'cargos',
			$this->model_cargos->field_search
		);
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('cargos_export');

		$this->model_cargos->pdf('cargos', 'cargos');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('cargos_export');

		$table = $title = 'cargos';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_cargos->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	
}


/* End of file cargos.php */
/* Location: ./application/controllers/administrator/Cargos.php */