<?php
define('_PS_MAGIC_QUOTES_GPC_', get_magic_quotes_gpc());
define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));

function latin1_database_to_utf8()
{
	global $requests, $warningExist;

	$tables = array(
					array('name' => 'address', 'id' => 'id_address', 'fields' => array('alias', 'company', 'name', 'surname', 'address1', 'address2', 'postcode', 'city', 'other', 'phone', 'phone_mobile')),
					array('name' => 'alias', 'id' => 'id_alias', 'fields' => array('alias', 'search')),
					array('name' => 'attribute_group_lang', 'id' => 'id_attribute_group', 'lang' => true, 'fields' => array('name', 'public_name')),
					array('name' => 'attribute_lang', 'id' => 'id_attribute', 'lang' => true, 'fields' => array('name')),
					array('name' => 'carrier', 'id' => 'id_carrier', 'fields' => array('name', 'url')),
					array('name' => 'carrier_lang', 'id' => 'id_carrier', 'lang' => true, 'fields' => array('delay')),
					array('name' => 'cart', 'id' => 'id_cart', 'fields' => array('gift_message')),
					array('name' => 'category_lang', 'id' => 'id_category', 'lang' => true, 'fields' => array('name', 'description', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description')),
					array('name' => 'configuration', 'id' => 'id_configuration', 'fields' => array('name', 'value')),
					array('name' => 'configuration_lang', 'id' => 'id_configuration', 'lang' => true, 'fields' => array('value')),
					array('name' => 'contact', 'id' => 'id_contact', 'fields' => array('email')),
					array('name' => 'contact_lang', 'id' => 'id_contact', 'lang' => true, 'fields' => array('name', 'description')),
					array('name' => 'country', 'id' => 'id_country', 'fields' => array('iso_code')),
					array('name' => 'country_lang', 'id' => 'id_country', 'lang' => true, 'fields' => array('name')),
					array('name' => 'currency', 'id' => 'id_currency', 'fields' => array('name', 'iso_code', 'sign')),
					array('name' => 'customer', 'id' => 'id_customer', 'fields' => array('email', 'passwd', 'name', 'surname')),
					array('name' => 'discount', 'id' => 'id_discount', 'fields' => array('name')),
					array('name' => 'discount_lang', 'id' => 'id_discount', 'lang' => true, 'fields' => array('description')),
					array('name' => 'discount_type_lang', 'id' => 'id_discount_type', 'lang' => true, 'fields' => array('name')),
					array('name' => 'employee', 'id' => 'id_employee', 'fields' => array('name', 'surname', 'email', 'passwd')),
					array('name' => 'feature_lang', 'id' => 'id_feature', 'lang' => true, 'fields' => array('name')),
					array('name' => 'feature_value_lang', 'id' => 'id_feature_value', 'lang' => true, 'fields' => array('value')),
					array('name' => 'hook', 'id' => 'id_hook', 'fields' => array('name', 'title', 'description')),
					array('name' => 'hook_module_exceptions', 'id' => 'id_hook_module_exceptions', 'fields' => array('file_name')),
					array('name' => 'image_lang', 'id' => 'id_image', 'lang' => true, 'fields' => array('legend')),
					array('name' => 'image_type', 'id' => 'id_image_type', 'fields' => array('name')),
					array('name' => 'lang', 'id' => 'id_lang', 'fields' => array('name', 'iso_code')),
					array('name' => 'manufacturer', 'id' => 'id_manufacturer', 'fields' => array('name')),
					array('name' => 'message', 'id' => 'id_message', 'fields' => array('message')),
					array('name' => 'module', 'id' => 'id_module', 'fields' => array('name')),
					array('name' => 'orders', 'id' => 'id_order', 'fields' => array('payment', 'module', 'gift_message', 'shipping_number')),
					array('name' => 'order_detail', 'id' => 'id_order_detail', 'fields' => array('product_name', 'product_reference', 'tax_name', 'download_hash')),
					array('name' => 'order_discount', 'id' => 'id_order_discount', 'fields' => array('name')),
					array('name' => 'order_state', 'id' => 'id_order_state', 'fields' => array('color')),
					array('name' => 'order_state_lang', 'id' => 'id_order_state', 'lang' => true, 'fields' => array('name', 'template')),
					array('name' => 'product', 'id' => 'id_product', 'fields' => array('ean13', 'reference')),
					array('name' => 'product_attribute', 'id' => 'id_product_attribute', 'fields' => array('reference', 'ean13')),
					array('name' => 'product_download', 'id' => 'id_product_download', 'fields' => array('display_filename', 'physically_filename')),
					array('name' => 'product_lang', 'id' => 'id_product', 'lang' => true, 'fields' => array('description', 'description_short', 'link_rewrite', 'meta_description', 'meta_keywords', 'meta_title', 'name', 'available_now', 'available_later')),
					array('name' => 'profile_lang', 'id' => 'id_profile', 'lang' => true, 'fields' => array('name')),
					array('name' => 'quick_access', 'id' => 'id_quick_access', 'fields' => array('link')),
					array('name' => 'quick_access_lang', 'id' => 'id_quick_access', 'lang' => true, 'fields' => array('name')),
					array('name' => 'supplier', 'id' => 'id_supplier', 'fields' => array('name')),
					array('name' => 'tab', 'id' => 'id_tab', 'fields' => array('class_name')),
					array('name' => 'tab_lang', 'id' => 'id_tab', 'lang' => true, 'fields' => array('name')),
					array('name' => 'tag', 'id' => 'id_tag', 'fields' => array('name')),
					array('name' => 'tax_lang', 'id' => 'id_tax', 'lang' => true, 'fields' => array('name')),
					array('name' => 'zone', 'id' => 'id_zone', 'fields' => array('name'))
				);
	
	foreach ($tables AS $table)
	{
		/* Latin1 datas' selection */
		if (!Db::getInstance()->Execute('SET NAMES latin1'))
			echo 'Cannot change the sql encoding to latin1!';
		$query = 'SELECT `'.$table['id'].'`';
		foreach ($table['fields'] AS $field)
			$query .= ', `'.$field.'`';
		if (isset($table['lang']) AND $table['lang'])
			$query .= ', `id_lang`';
		$query .= ' FROM `'._DB_PREFIX_.$table['name'].'`';
		$latin1Datas = Db::getInstance()->ExecuteS($query);
		if ($latin1Datas === false)
		{
			$warningExist = true;
			$requests .= '
				<request result="fail">
					<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
					<sqlMsgError><![CDATA['.htmlentities(Db::getInstance()->getMsgError()).']]></sqlMsgError>
					<sqlNumberError><![CDATA['.htmlentities(Db::getInstance()->getNumberError()).']]></sqlNumberError>
				</request>'."\n";
		}
	
		if (Db::getInstance()->NumRows())
		{
			/* Utf-8 datas' restitution */
			if (!Db::getInstance()->Execute('SET NAMES utf8'))
				echo 'Cannot change the sql encoding to utf8!';
			foreach ($latin1Datas AS $latin1Data)
			{
				$query = 'UPDATE `'._DB_PREFIX_.$table['name'].'` SET';
				foreach ($table['fields'] AS $field)
					$query .= ' `'.$field.'` = \''.pSQL($latin1Data[$field]).'\',';
				$query = rtrim($query, ',');
				$query .= ' WHERE `'.$table['id'].'` = '.intval($latin1Data[$table['id']]);
				if (isset($table['lang']) AND $table['lang'])
					$query .= ' AND `id_lang` = '.intval($latin1Data['id_lang']);
				if (!Db::getInstance()->Execute($query))
				{
					$warningExist = true;
					$requests .= '
						<request result="fail">
							<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
							<sqlMsgError><![CDATA['.htmlentities(Db::getInstance()->getMsgError()).']]></sqlMsgError>
							<sqlNumberError><![CDATA['.htmlentities(Db::getInstance()->getNumberError()).']]></sqlNumberError>
						</request>'."\n";
				}
			}
		}
	}
}
?>