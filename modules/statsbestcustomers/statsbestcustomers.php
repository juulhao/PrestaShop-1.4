<?php

/**
  * Statistics
  * @category stats
  *
  * @author Damien Metzger / Epitech
  * @copyright Epitech / PrestaShop
  * @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
  * @version 1.1
  */
  
class StatsBestCustomers extends ModuleGrid
{
	private $_html = null;
	private $_query =  null;
	private $_columns = null;
	private $_defaultSortColumn = null;
	private $_emptyMessage = null;
	private $_pagingMessage = null;
	
	function __construct()
	{
		$this->name = 'statsbestcustomers';
		$this->tab = 'Stats';
		$this->version = 1.0;
		$this->page = basename(__FILE__, '.php');
		
		$this->_defaultSortColumn = 'total';
		$this->_emptyMessage = $this->l('Empty recordset returned');
		$this->_pagingMessage = $this->l('Displaying').' {0} - {1} '.$this->l('of').' {2}';
		
		$this->_columns = array(
			array(
				'id' => 'lastname',
				'header' => $this->l('Lastname'),
				'dataIndex' => 'lastname',
				'width' => 100
			),
			array(
				'id' => 'firstname',
				'header' => $this->l('Firstname'),
				'dataIndex' => 'firstname',
				'width' => 100
			),
			array(
				'id' => 'email',
				'header' => $this->l('Email'),
				'dataIndex' => 'email',
				'width' => 150,
				'align' => "right"
			),
			array(
				'id' => 'total',
				'header' => $this->l('Total'),
				'dataIndex' => 'total',
				'width' => 50,
				'align' => 'right')
		);
		
		parent::__construct();
		
		$this->displayName = $this->l('Best customers');
		$this->description = $this->l('A list of the best customers');
	}
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}
	
	public function hookAdminStatsModules($params)
	{
		$engineParams = array(
			'id' => 'id_customer',
			'title' => $this->displayName,
			'columns' => $this->_columns,
			'defaultSortColumn' => $this->_defaultSortColumn,
			'emptyMessage' => $this->_emptyMessage,
			'pagingMessage' => $this->_pagingMessage
		);
	
		$this->_html = '
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			'.ModuleGrid::engine($engineParams).'
			<h2 class="space">'.$this->l('Develop clients\' loyalty').'</h2>
			<h3>'.$this->l('What should you do?').'</h3>
			<p class="space">
				'.$this->l('Keeping a client is more profitable than capturing a new one. Thus, it is necessary to develop its loyalty, in other words to make him come back in your webshop.').' <br />
				'.$this->l('Word of mouth is also a means to get new satisfied clients; a dissatisfied one won\'t attract new clients.').'<br />
				'.$this->l('In order to achieve this goal you can organize: ').'
				<ul>
					<li>- '.$this->l('Punctual operations: commercial rewards (personalized special offers, product or service offered), non commercial rewards (priority handling of an order or a product), pecuniary rewards (bonds, discount coupons, payback...).').'</li>
					<li>- '.$this->l('Sustainable operations: loyalty or points cards, which not only justify communication between merchant and client, but also offer advantages to clients (private offers, discounts).').'</li>
				</ul>
				'.$this->l('These operations encourage clients to buy and also to come back in your webshop regularly.').' <br />
			</p>
		</fieldset>';
		return $this->_html;
	}
	
	public function getTotalCount()
	{
		$result = Db::getInstance()->GetRow('SELECT COUNT(DISTINCT c.`id_customer`) totalCount FROM `'._DB_PREFIX_.'customer` c
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON c.id_customer = o.id_customer
		WHERE ( SELECT os.`invoice` FROM `'._DB_PREFIX_.'orders` oo LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON oh.`id_order` = oo.`id_order` LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state` WHERE oo.`id_order` = o.`id_order` ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC LIMIT 1 ) = 1');
		return $result['totalCount'];
	}
	
	public function setOption($option)
	{
	}
	
	public function getData()
	{
		$this->_totalCount = $this->getTotalCount();		
		$this->_query = '
		SELECT t.`id_customer` , t.`lastname` , t.`firstname` , t.`email`, SUM(t.`total_paid_real`) AS total
		FROM (
			SELECT c.`id_customer` , c.`lastname` , c.`firstname` , c.`email`, o.`total_paid_real` FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.id_customer = o.id_customer
			WHERE ( SELECT os.`invoice` FROM `'._DB_PREFIX_.'orders` oo LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON oh.`id_order` = oo.`id_order` LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state` WHERE oo.`id_order` = o.`id_order` ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC LIMIT 1 ) = 1
		) t
		GROUP BY t.`id_customer` , t.`lastname` , t.`firstname` , t.`email`
		';	
		if (Validate::IsName($this->_sort))
		{
			$this->_query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) AND Validate::IsSortDirection($this->_direction))
				$this->_query .= ' '.$this->_direction;
		}
		if (($this->_start === 0 OR Validate::IsUnsignedInt($this->_start)) AND Validate::IsUnsignedInt($this->_limit))
			$this->_query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance()->ExecuteS($this->_query);
	}
}
