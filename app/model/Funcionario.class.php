<?php
/**
 * Funcionario Active Record
 * @author  <your-name-here>
 */
class Funcionario extends TRecord
{
    const TABLENAME = 'funcionario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('cartaoSus');
        parent::addAttribute('dataNasc');
        parent::addAttribute('dataAdmissao');
        parent::addAttribute('ativo');
    }


}
