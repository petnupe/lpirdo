<?php

class PatologiaPacienteListManual extends TPage
{
	protected $form, $datagrid;

	public function __construct()
	{
		parent::__construct();
		$this->form = new BootstrapFormBuilder('form_PatologiaPaciente');
		$this->form->setFormTitle('Patologias por paciente');

		$paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome', 'nome');

		$change_action = new TAction(array($this, 'onChangePaciente'));
		$paciente_id->setChangeAction($change_action);

		$this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );
		$btnFind = $this->form->addAction(_t('Find'), new TAction([$this, 'onReload']), 'fa:search');
		$btnNew = $this->form->addAction(_t('New'), new TAction(['PatologiaPacienteForm', 'onReload']), 'fa:eraser red');

		$this->createDataGrid();
		$panel = new TPanel('', 'white');
		$panel->add($this->datagrid);

		$container = new TVBox();
		$container->style = 'width: 100%';
		$container->add($this->form);
		$container->add($panel);
		parent::add($container);
	}

	function createDataGrid()
	{
		$this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
		$this->datagrid->style = 'width: 100%';
		$this->datagrid->datatable = 'true';
		$column_patologia_id = new TDataGridColumn('patologia_id', 'Patologias', 'left');
		$column_patologia_id->setTransformer([$this, 'getNomePatologia']);
		$this->datagrid->addColumn($column_patologia_id);

		$action1 = new TDataGridAction(['PatologiaPacienteForm', 'onEdit'], ['id'=>'{id}']);
		$action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
		$this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
		$this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
		$this->datagrid->createModel();
	}

	function onReload($param = null)
	{
		$dados = $this->form->getData();
		$dados->paciente_id =  empty($param['paciente_id']) ? $param['key'] : $param['paciente_id'];

		$this->form->setData($dados);

		if(trim($dados->paciente_id)) {
			TTransaction::open('db');
			$repo = new TRepository('PatologiaPaciente');
			$Criteria = new TCriteria;
			$Criteria->add(new TFilter('paciente_id', '=', $dados->paciente_id));
			$objects = $repo->load($Criteria);

			foreach($objects as $object) {
				$this->datagrid->addItem($object);
			}
			TTransaction::close();
		}
	}

	public function show($parms = null)
	{
		parent::show($parms);
	}

	public function getNomePatologia($id = null)
	{
		TTransaction::open('db');
		$Patologia = new Patologia($id);
		TTransaction::close();
		return $Patologia->nome;
	}

	public static function onDelete($param)
	{
		$action = new TAction([__CLASS__, 'Delete']);
		$action->setParameters($param); // pass the key parameter ahead
		new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete?'), $action);
	}

	public static function Delete($param)
	{
		try
		{
			$key = $param['key']; // get the parameter $key
			TTransaction::open('db'); // open a transaction with database
			$object = new PatologiaPaciente($key, FALSE); // instantiates the Active Record
			
			$paciente_id = $object->Paciente->id;
			
			$object->delete(); // deletes the object from the database
			TTransaction::close(); // close the transaction

			//$pos_action = new TAction([__CLASS__, 'onReload']);
			new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
		}
		catch (Exception $e) // in case of exception
		{
			new TMessage('error', $e->getMessage()); // shows the exception error message
			TTransaction::rollback(); // undo all pending operations
		}
		
		AdiantiCoreApplication::postData('form_PatologiaPaciente', __CLASS__, 'onReload', ['key' => $paciente_id]);
	}

	public function onEdit(){}

	public static function onChangePaciente($obj) {
		AdiantiCoreApplication::gotoPage(__CLASS__, 'onReload', $obj);
	}
}





















