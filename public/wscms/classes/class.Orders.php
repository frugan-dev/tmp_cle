<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Orders.php v.1.0.0. 06/05/2021
 */

use Soundasleep\Html2Text;

class Orders extends Core
{

	public static $carts_id;
	public static $dbTableAssOrdersUsers = '';
	public static $dbTable = '';
	public static $dbTableProducts = '';
	public static $dbTableUsers = '';
	public static $dbTableCompanies = '';
	public static $dbTableBillingAddresses = '';
	public static $dbTableShipmentAddresses = '';
	public static $usersId = '';
	public static $orderId = '';
	public static $orderNumber = '';
	public static $OrderNote = '';
	public static $OrderUsersId = '';
	public static $OrderCompaniesCode = '';
	public static $OrderCreated = '';
	public static $OrderData = '';
	public static $filterForUsers = true;
	public static $billingAddresses;
	public static $shipmentAddresses;
	public static $orderAllDetails;
	public static $whereDbQueryClause;
	public static $whereAndDbQueryClause;
	public static $fieldsDbQuery;
	public static $fieldsValuesDbQuery;
	public static $langUser = 'it';
	private static $orderFields = [];
	public static $dompdf;

	public static $hideDeleted;
	
	public function __construct()
	{
		parent::__construct();
	}

	/* 
	Aggiorna i riferimenti numero ordine -> id utente. 
	Ozioni richiesta:
	$orders_number = l'ultimo ordine generato
	$users_id = id dell'user corrente
	*/
	public static function cupdateOrderFirstNumberUserAss($orders_number,$users_id)
	{
		$f = ['orders_number'];
		$fv = [$orders_number, $users_id];
		Sql::initQuery(Config::$DatabaseTables['ass_orders_users'], $f, $fv, 'users_id = ?', '', '', '', false);
		Sql::updateRecord();
		if (Core::$resultOp->error > 0) {
			die('erroe aggiornamento numero');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
	}
	
	/* 
	Salva i riferimenti numero ordine -> id utente. Ozioni:
	$orders_number = l'ultimo ordine generato
	$users_id = id dell'user corrente
	*/
	public static function createOrderFirstNumberUserAss($orders_number,$users_id)
	{
		$f = ['orders_number', 'users_id'];
		$fv = [$orders_number, $users_id];
		Sql::initQuery(Config::$DatabaseTables['ass_orders_users'], $f, $fv, '', '', '', false);
		Sql::insertRecord();
		if (Core::$resultOp->error > 0) {
			die('errore db crea ass number');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
	}

	/* 
	Prende l'ultimo numero ordine. 
	Ozioni richiesta:
	$users_id = id dell'user corrente
	Restituisce:
	$orders_number = l'ultimo numero ordime memorizzazto per l'utente corrente
	*/
	public static function getSavedOrderNUmberWhereUserId($users_id)
	{
		$orders_number = 0;
		Sql::initQuery(Config::$DatabaseTables['ass_orders_users'],['*'],[$users_id],'users_id = ?');
		$foo = Sql::getRecord();
		if (isset($foo->orders_number)) {
			$orders_number = $foo->orders_number;
		} else {
			self::createOrderFirstNumberUserAss($orders_number,$users_id);
		}
		return $orders_number;
	} 

	public static function saveInvoice($id): never
	{

		self::getAllOrderDetails($id);
		//ToolsStrings::dump(self::$orderAllDetails);die();
		$html = self::getHtmlInvoice();
		//echo $html;die();
		self::$dompdf->setPaper('A4', 'portait');
		self::$dompdf->loadHtml($html);
		self::$dompdf->render();

		// Output the generated PDF to Browser
		$filename = PATH_TMP_DIR . "Ordine-" . self::$orderAllDetails->number . ".pdf";
		$output = self::$dompdf->output();
		file_put_contents($filename, $output);

		// cnacella il files in tmp
		//ToolsUpload::recursiveDelete(PATH_SITE . "tmp");
		die();
	}

	public static function getInvoice($id): never
	{
		self::getAllOrderDetails($id);
		//ToolsStrings::dump(self::$orderAllDetails);die();
		$html = self::getHtmlInvoice();
		//echo $html;die();
		self::$dompdf->setPaper('A4', 'portait');
		self::$dompdf->loadHtml($html);
		// Render the HTML as PDF
		self::$dompdf->render();

		// Output the generated PDF to Browser
		$filename = self::$orderAllDetails->user->name . "-" . self::$orderAllDetails->number . ".pdf";
		self::$dompdf->stream($filename);
		die();
	}

	public static function getAllOrderDetails($id)
	{
		// trova dettagli ordine
		self::$orderAllDetails = self::getOrderDetails($id);
		// trova prodotti ordine
		self::$orderAllDetails->products = self::getOrderProducts($id);
		// trova i dati del fornitore
		self::$orderAllDetails->fornitore = Users::getUserDetailsFromCompaniesCode(self::$OrderCompaniesCode);
		//ToolsStrings::dump(self::$orderAllDetails->fornitore);die();
		// trova i dati della company
		self::$orderAllDetails->company = self::getOrderCompanyFromCode(self::$OrderCompaniesCode);
		// trova i dati di user
		self::$orderAllDetails->user = self::getOrderUserFromId(self::$OrderUsersId);
	}

	public static function updateOrder($id)
	{
		//print_r(self::$orderFields);
		$f = [];
		$fv = [];
		foreach (self::$orderFields as $key => $value) {
			if ($value != '') {
				$f[] = $key;
				$fv[] = $value;
			}
		}
		$fv[] = $id;
		Sql::initQuery(self::$dbTable, $f, $fv, 'id = ?', '', '', '', false);
		Sql::updateRecord();
		if (Core::$resultOp->error > 0) {
			die('errore db agg ordine');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
	}

	public static function resetOrderfields()
	{
		self::$orderFields = [];
	}

	public static function setOrderFields($value)
	{
		self::$orderFields = $value;
	}

	public static function getOrderProducts($id)
	{
		$obj = [];
		if ($id > 0) {
			Sql::initQuery(self::$dbTableProducts, ['*'], [$id], 'orders_id = ?', '', '', false);
			$pdoObject = Sql::getPdoObjRecords();
			if (Core::$resultOp->error > 0) { die('errore db lettura prodotto carrello'); }
			while ($row = $pdoObject->fetch()) {
				$obj[] = $row;
			}
		}
		return $obj;
	}

	public static function getOrderDetails($id)
	{
		//Core::setDebugMode(1);
		$obj = new stdClass();
		Sql::initQuery(self::$dbTable, ['*'], [$id], 'id = ?', '', '', false);
		$obj = Sql::getRecord();
		if (Core::$resultOp->error > 0) { /*die('errore db get record');*/
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
		/*
		[id] => 15
		[data] => 2021-04-07
		[number] => 16
		[note] => Test
		[status] => open
		[users_id] => 26
		[companies_code] => 00003
		[created] => 2021-03-29 09:32:57
		*/

		Self::$OrderCompaniesCode = $obj->companies_code;
		Self::$OrderUsersId = $obj->users_id;
		return $obj;
	}

	public static function getOrderCompanyFromCode($code)
	{
		//Core::setDebugMode(1);
		$obj = new stdClass();
		Sql::initQuery(
			self::$dbTableCompanies . ' AS company',
			[
				'company.*',
				'(SELECT comuni.nome FROM ' . Sql::getTablePrefix() . 'location_comuni AS comuni WHERE comuni.id = company.location_comuni_id) AS comune',
				'(SELECT province.nome FROM ' . Sql::getTablePrefix() . 'location_province AS province WHERE province.id = company.location_province_id) AS provincia',
				'(SELECT nations.title_' . self::$langUser . ' FROM ' . Sql::getTablePrefix() . 'location_nations AS nations WHERE nations.id = company.location_nations_id) AS nations'
			],
			[$code],
			'company.code = ?',
			'',
			'',
			false
		);
		$obj = Sql::getRecord();
		//ToolsStrings::dump($obj);die();
		if (Core::$resultOp->error > 0) {
			die('errore db get record company');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
		return $obj;
	}

	public static function getOrderUserFromId($usersId)
	{
		$obj = new stdClass();
		Users::$dbTable = self::$dbTableUsers;
		$obj = Users::getUserDetails($usersId);
		return $obj;
	}

	public static function getOrdersList()
	{
		//Core::setDebugMode(1);
		$obj = [];
		$table = self::$dbTable . ' AS orders';
		$table .= ' LEFT JOIN ' . self::$dbTableUsers . ' AS users ON (orders.users_id = users.id)';
		$table .= ' LEFT JOIN ' . self::$dbTableCompanies . ' AS companies ON (orders.companies_code = companies.code)';
		$where = '';
		$and = '';
		$f = [
			'orders.*',
			"CONCAT(users.username,' (',users.name,' ',users.surname,')') AS customer",
			"companies.ragione_sociale AS companies_ragione_sociale"
		];
		$fv = [];

		if (self::$whereDbQueryClause != '') $where .= $and . self::$whereDbQueryClause;
		if (self::$whereAndDbQueryClause != '') $and = self::$whereAndDbQueryClause;
		if (isset(self::$fieldsDbQuery) && is_array(self::$fieldsDbQuery) && count(self::$fieldsDbQuery) > 0) {
			$f = array_merge($f, self::$fieldsDbQuery);
		}
		if (isset(self::$fieldsValuesDbQuery) && is_array(self::$fieldsValuesDbQuery) && count(self::$fieldsValuesDbQuery) > 0) {
			$fv = array_merge($fv, self::$fieldsValuesDbQuery);
		}

		if (self::$OrderCompaniesCode != '') {
			$fv[] = self::$OrderCompaniesCode;
			$where .= $and . 'companies_code = ?';
			$and = ' AND ';
		}

		if (self::$filterForUsers == true) {
			$fv[] = self::$OrderUsersId;
			$where .= $and . 'users_id = ?';
			$and = ' AND ';
		}

		if (self::$hideDeleted == 1) {
			$where .= $and . 'is_deleted = 0';
			$and = ' AND ';
		}

		//echo $where;
		//echo $and;
		//print_r($f);
		//print_r($fv);
		Sql::initQueryBasic($table, $f, $fv, $where);
		Sql::setOrder('orders.created DESC');
		$pdoObject = Sql::getPdoObjRecords();
		if (Core::$resultOp->error > 0) {
			die('errore db get records');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
		while ($row = $pdoObject->fetch()) {
			$obj[] = $row;
		}
		//die();
		return $obj;
	}


	public static function createOrder()
	{

		//ToolsStrings::dumpArray($_POST);die();
		// crea ordine
		self::$OrderCreated = date('Y-m-d H:i:s');
		$f = ['number', 'note', 'users_id', 'companies_code', 'status', 'created', 'data'];
		$fv = [self::$orderNumber, self::$OrderNote, self::$OrderUsersId, self::$OrderCompaniesCode, 'open', self::$OrderCreated, self::$OrderData];
		Sql::initQuery(self::$dbTable, $f, $fv, '', '', '', '', false);
		Sql::insertRecord();
		if (Core::$resultOp->error > 0) {
			die('errore creazione ordine');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
		self::$orderId = Sql::getLastInsertedIdVar();
		$order = ['created'=>self::$OrderCreated,'number'=>self::$orderNumber];

		// aggiorna ass number
		$f = ['orders_number'];
		$fv = [self::$orderNumber, self::$OrderUsersId];
		Sql::initQuery(self::$dbTableAssOrdersUsers, $f, $fv, 'users_id = ?', '', '', '', false);
		Sql::updateRecord();
		if (Core::$resultOp->error > 0) {
			die('erroe aggiornamento numero');
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}

		// aggiungi prodotti
		//Core::setDebugMode(1);
		Carts::loadCartProducts();
		$products = Carts::$cartProducts;
		//ToolsStrings::dumpArray($products);
		if (isset($products) && is_array($products) && count($products) > 0) {
			foreach ($products as $value) {
				$attributes = json_encode($value->attributes);
				$f = [
					'users_id',
					'orders_id',
					'products_id',
					'code',
					'price',
					'quantity',
					'title',
					'companies_code',
					'attribute'
				];
				$fv = [
					self::$OrderUsersId,
					self::$orderId,
					$value->products_id,
					$value->code,
					$value->price,
					$value->quantity,
					$value->title,
					self::$OrderCompaniesCode,
					$value->attribute
				];
				Sql::initQuery(self::$dbTableProducts, $f, $fv, '', '', '', '', false);
				Sql::insertRecord();
				if (Core::$resultOp->error > 0) {
					die('errore inseriemnto prodotti ordine');
					ToolsStrings::redirect(URL_SITE . 'error/db');
				}
				$value->attributesArray = $value->attributes;
				$value->attributes = $attributes;
			}
		}
		//ToolsStrings::dumpArray($products);die();

		// trova i dati della company
		$company = self::getOrderCompanyFromCode(self::$OrderCompaniesCode);

		// trova i dati di user
		$users = self::getOrderUserFromId(self::$OrderUsersId);

		$textEmailCustomer = self::createOrderCustomerHtmlEmail($users, $company, $products);
		$textEmailStaff = self::createOrderStaffHtmlEmail($users, $company, $products);

		//echo $textEmailCustomer;echo $textEmailStaff;die('fatto 5');

		Carts::deleteCart();

		self::sendOrderCustomerEmail($textEmailCustomer, $users, $company, $order, 1);
		self::sendOrderStaffEmail($textEmailStaff, $users, $company, $order, 1);
		//die();

	}

	/* 
	Manda la email riepilogativa dell'ordine al cliente. Ozioni:
	$textemail = testo della email generata
	$user = oggetto con i dati dell'utente che ha creato l'ordine
	$company = oggetto con i dati dell'azienda a qui è indirizzato l'ordine
	$new = specifica che è il primo invio (1) o no (0). Cambia il titolo in caso di rienvio di una email di un ordine già fatto in passato
	*/
	public static function sendOrderCustomerEmail($textHtml, $user, $company, $order, $new = 1)
	{
		if ($user->email != '') {
			$titolo = 'Invio nuovo ordine';
			if ($new == 0) $titolo = 'Reinvio ordine';
			$titolo .= ' numero '.$order['number'].' del '.DateFormat::dateFormating( $order['created'], 'd/m/Y' );

			$titolo = Users::parseEmailText($titolo, $opt = []);
			$textHtml = Users::parseEmailText($textHtml, $opt = []);
			$textPlain = Html2Text::convert($textHtml);
			$opt = [];

			$fromEmail = Core::$globalSettings['default email'];
			$fromLabel = Core::$globalSettings['default email label'];
			if (isset($company->email) && $company->email != '') {
				$fromEmail = $company->email;
				$fromLabel = $company->email;
			}
			$opt['fromEmail'] = $fromEmail;
			$opt['fromLabel'] = $fromLabel;

			$opt['sendDebug'] = Core::$globalSettings['send email debug'];
			$opt['sendDebugEmail'] = Core::$globalSettings['email debug'];
			Mails::sendEmail($user->email, $titolo, $textHtml, $textPlain, $opt);
			//Core::$resultOp->error = 1;
			if (Core::$resultOp->error > 0) {
				ToolsStrings::redirect(URL_SITE . 'error/mail/Errore invio della email ordine al cliente!');
			}
		} else {
			ToolsStrings::redirect(URL_SITE . 'error/mail/Il cliente non ha una email impostata! Siete pregati di contattare l\' amministrazione del sistema!');
		}
	}

	/* 
	Manda la email riepilogativa dell'ordine allo staff. Ozioni:
	$textemail = testo della email generata
	$user = oggetto con i dati dell'utente che ha creato l'ordine
	$company = oggetto con i dati dell'azienda a qui è indirizzato l'ordine
	$new = specifica che è il primo invio (1) o no (0). Cambia il titolo in caso di rienvio di una email di un ordine già fatto in passato
	*/
	public static function sendOrderStaffEmail($textHtml, $user, $company, $order, $new = 1)
	{

		$titolo = 'Creazione nuovo ordine';
		if ($new == 0) $titolo = 'Reinvio dati ordine';
		$titolo .= ' numero '.$order['number'].' del '.DateFormat::dateFormating( $order['created'], 'd/m/Y' );

		$titolo = Users::parseEmailText($titolo, $opt = []);
		$textHtml = Users::parseEmailText($textHtml, $opt = []);
		$textPlain = Html2Text::convert($textHtml);
		$opt = [];

		$fromEmail = Core::$globalSettings['default email'];
		$fromLabel = Core::$globalSettings['default email label'];
		if (isset($company->email) && $company->email != '') {
			$fromEmail = $company->email;
			$fromLabel = $company->email;
		}
		$opt['fromEmail'] = $fromEmail;
		$opt['fromLabel'] = $fromLabel;

		$opt['sendDebug'] = Core::$globalSettings['send email debug'];
		$opt['sendDebugEmail'] = Core::$globalSettings['email debug'];
		$address = [];
		$email = '';
		if (isset($company->email) && $company->email != '') $address[] = $company->email;
		if (isset($company->email1) && $company->email1 != '') $address[] = $company->email1;
		if (isset($company->email2) && $company->email2 != '') $address[] = $company->email2;
		if (isset($address[0])) {
			$email = $address[0];
			unset($address[0]);
		}
		$opt['addBCC'] = $address;
		if ($email != '') {
			Mails::sendEmail($email, $titolo, $textHtml, $textPlain, $opt);
			//Core::$resultOp->error = 1;
			if (Core::$resultOp->error > 0) {
				ToolsStrings::redirect(URL_SITE . 'error/mail/Errore invio della email ordine al fornitore!');
			}
		} else {
			ToolsStrings::redirect(URL_SITE . 'error/mail/Il fornitore non ha una email impostata! Siete pregati di contattare l\' amministrazione del sistema!');
		}
	}

	public static function createOrderCustomerHtmlEmail($users, $company, $products)
	{
		$output = '';
		$output .= '
		<p>Gentile cliente, grazie per aver effettuato un nuovo ordine!<br>In seguito i dettagli:</p>
		<p>
		Fornitore: ' . $company->ragione_sociale . '<br>
		Data Creazione: ' . DateFormat::dateFormating(self::$OrderCreated, 'd/m/Y H:i') . '<br>
		Ordine per il giorno: ' . DateFormat::dateFormating(self::$OrderData, 'd/m/Y') . '<br>
		Numero:' . self::$orderNumber . '<br>
		Note:' . self::$OrderNote . '<br>
		</p>
		<p>
			Prodotti ordinati
		</p>' . self::createOrderProductsTable($products, ['viewPrice'=>false,'viewCode'=>true] );
		return $output;
	}

	public static function createOrderStaffHtmlEmail($users, $company, $products)
	{
		//ToolsStrings::dump($users);
		$output = '';
		$output .= '
		<p>E\' stato creato un nuovo ordine!<br>In seguito i dettagli:</p>
		<p>
		Cliente: ' . $users->name . ' ' . $users->surname . '<br>
		Data Creazione: ' . DateFormat::dateFormating(self::$OrderCreated, 'd/m/Y H:i') . '<br>
		Ordine per il giorno: ' . DateFormat::dateFormating(self::$OrderData, 'd/m/Y') . '<br>
		Numero:' . self::$orderNumber . '<br>
		Note:' . self::$OrderNote . '<br>
		</p>
		<p>
			Prodotti ordinati
		</p>
		' . self::createOrderProductsTable($products, ['viewPrice'=>false,'viewCode'=>true] );
		return $output;
	}

	public static function createOrderProductsTable($products, $opt)
	{
		$optDef = ['viewPrice' => 90, 'viewCode' => 100];
		$opt = array_merge($optDef,$opt);
		/*
		ToolsStrings::dump($opt);
		echo '<br>price: '.$opt['viewPrice'];
		echo '<br>code: '.$opt['viewCode'];
		*/
		$output = '<table border="1" cellpadding="2">
			<thead>
				<tr>
					<th>Nome</th>';
		if (isset($opt['viewCode']) && $opt['viewCode'] == true) {
			$output .= '<th>Codice</th>';
		}
		if (isset($opt['viewPrice']) && $opt['viewPrice'] == true) {
			$output .= '<th>Prezzo</th>';
		}
		$output .= '<th>Unità di misura</th>
					<th>Quantità</th>
				</tr>
			</thead>
			<tbody>';
		if (is_array($products) && count($products) > 0) {
			foreach ($products as $value) {
				$output .= '
				<tr>
					<td>' . $value->title . '</td>';
					if (isset($opt['viewCode']) && $opt['viewCode'] == true) {
						$output .= '<td>' . $value->code . '</td>';
					}			
					if (isset($opt['viewPrice']) && $opt['viewPrice'] == true) {
  						$output .= '<td>€ ' . number_format($value->price,2,',','.') . '</td>';
					}

					$output .= '<td>'.$value->attribute.'</td>';
					$output .= '<td style="text-align:center">' . $value->quantity . '</td>
				</tr>
				';
			}
		}
		$output .= '
			</tbody>
		</table>
		';
		//die($output);
		return $output;
	}

	public static function loadOrderBillingAddresses()
	{
		$f = ['*'];
		$fv = [self::$carts_id];
		$clause = 'carts_id = ?';
		Sql::initQuery(self::$dbTableBillingAddresses, $f, $fv, $clause, '', '', '', false);
		self::$billingAddresses = Sql::getRecord();
		if (Core::$resultOp->error > 0) {
			ToolsStrings::redirect(URL_SITE . 'error/db');
		}
		return self::$billingAddresses;
	}

	public static function addOrderBillingAddresses()
	{
		$f = [
			'carts_id',
			'name',
			'surname',
			'street',
			'city',
			'zip_code',
			'province',
			'state',
			'email',
			'telephone',
			'fax',
			'mobile'

		];
		$fv = [
			self::$carts_id,
			self::$billingAddresses->name,
			self::$billingAddresses->surname,
			self::$billingAddresses->street,
			self::$billingAddresses->city,
			self::$billingAddresses->zip_code,
			self::$billingAddresses->province,
			self::$billingAddresses->state,
			self::$billingAddresses->email,
			self::$billingAddresses->telephone,
			self::$billingAddresses->fax,
			self::$billingAddresses->mobile
		];
		Sql::initQuery(self::$dbTableBillingAddresses, $f, $fv, '', '', '', '', false);
		Sql::insertRecord();
		if (Core::$resultOp->error > 0) {
			ToolsStrings::redirect(URL_SITE . 'error/db');
			die();
		}
	}

	public static function addOrderShipmentAddresses()
	{
		$f = [
			'carts_id',
			'name',
			'surname',
			'street',
			'city',
			'zip_code',
			'province',
			'state',
			'email',
			'telephone',
			'fax',
			'mobile'

		];
		$fv = [
			self::$carts_id,
			self::$shipmentAddresses->name,
			self::$shipmentAddresses->surname,
			self::$shipmentAddresses->street,
			self::$shipmentAddresses->city,
			self::$shipmentAddresses->zip_code,
			self::$shipmentAddresses->province,
			self::$shipmentAddresses->state,
			self::$shipmentAddresses->email,
			self::$shipmentAddresses->telephone,
			self::$shipmentAddresses->fax,
			self::$shipmentAddresses->mobile
		];
		Sql::initQuery(self::$dbTableShipmentAddresses, $f, $fv, '', '', '', '', false);
		Sql::insertRecord();
		if (Core::$resultOp->error > 0) {
			ToolsStrings::redirect(URL_SITE . 'error/db');
			die();
		}
	}

	public static function getHtmlInvoice73x()
	{
		$basePath = URL_SITE . 'templates/default/';
		$templatePath = 'var/www/html/mercato/templates/default/';
		$templatePath = URL_SITE . 'templates/default/';
		$templatePath = 'templates/default/';
		$templatePath = '';
		$dati = self::$orderAllDetails;
		$html = '';
		//ToolsStrings::dump($dati);

		//PATH_SITE.'templates/default/';


		$_lang['date format'] = 'd/m/Y';
		$dati->createdLocale = DateFormat::dateFormating($dati->created, $_lang['date format']);
		$dati->dataLocale = DateFormat::dateFormating($dati->data, $_lang['date format']);

		if ($dati->company->location_comuni_id == 0) $dati->company->comune = $dati->company->comune_alt;
		if ($dati->company->location_province_id == 0) $dati->company->provincia = $dati->company->provincia_alt;

		if ($dati->user->location_comuni_id == 0) $dati->user->comune = $dati->user->comune_alt;
		if ($dati->user->location_province_id == 0) $dati->user->provincia = $dati->user->provincia_alt;
		/*
		$html = <<<HTML
		<!DOCTYPE html>
		<html>
			<head>
				<base href="{$basePath}">
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">

				<title>Fattura</title>

				<link href="{$templatePath}css/bootstrap.min.css" rel="stylesheet">
				<link href="{$templatePath}css/font-awesome.css" rel="stylesheet">

				<link href="{$templatePath}css/animate.css" rel="stylesheet">
				<link href="{$templatePath}css/style.css" rel="stylesheet">

			</head>

			<body class="white-bg">

				<div class="wrapper wrapper-content p-xl">
					<div class="ibox-content p-xl">
						<div class="row">
							<div class="col">
								<h5>Da:</h5>
								<address>
									<strong>{$dati->company->ragione_sociale}</strong><br>
									{$dati->company->street}<br>
									{$dati->company->comune} {$dati->company->zip_code} {$dati->company->provincia}<br>
									<abbr title="Phone">Telefono:</abbr> {$dati->fornitore->telephone}
								</address>
							</div>
							<div class="col text-right">
								<h4>Numero ordine:
								<span class="text-navy">{$dati->number}</span></h4>
								<span>A:</span>
								<address>
									<strong>{$dati->user->name} {$dati->user->surname}</strong><br>
									{$dati->user->street}<br>
									{$dati->user->comune} {$dati->user->zip_code} {$dati->user->provincia}<br>
									<abbr title="Phone">Telefono:</abbr> {$dati->user->telephone}
								</address>
								<p>
									<span><strong>Data creazione:</strong> {$dati->createdLocale}</span><br/>
									<span><strong>Data ordine:</strong> {$dati->dataLocale}</span>
								</p>
							</div>
						</div>

						<div class="table-responsive m-t">
							<table class="table invoice-table">
								<thead>
									<tr>
										<th>Nome</th>
										<th>Unità di misura</th>
										<th>Quantità</th>
									</tr>
								</thead>
								<tbody>
		HTML;
		/*
		if(is_array($dati->products) && count($dati->products)) {
			foreach ($dati->products AS $value) {
				$html .= <<<'HTML'
					<tr>
						<td>{$value->title}</td>
						<td>{$value->attributesArray[0]->label}</td>
						<td>{$value->quantity}</td>
					</tr>
					marker;
				HTML;
			}
		}
		$html .= <<<'HTML'
								</tbody>
							</table>
						</div><!-- /table-responsive -->
						<div class="well m-t"><strong>Note</strong>
						{$dati->note}
						</div>
					</div>
				</div>

			<!-- Mainly scripts -->
			<script src="js/jquery-3.1.1.min.js"></script>
			<script src="js/popper.min.js"></script>
			<script src="js/bootstrap.js"></script>

			</body>
		</html>
		marker;
		HTML;
		*/
		return $html;
	}

	public static function getHtmlInvoice()
	{
		$basePath = URL_SITE . 'templates/default/';
		$templatePath = 'var/www/html/mercato/templates/default/';
		$templatePath = URL_SITE . 'templates/default/';
		$templatePath = 'templates/default/';
		$templatePath = '';
		$dati = self::$orderAllDetails;
		//ToolsStrings::dump($dati);die();
		//PATH_SITE.'templates/default/';
		//ToolsStrings::dump($dati->user);

		$_lang['date format'] = 'd/m/Y';
		$dati->createdLocale = DateFormat::dateFormating($dati->created, $_lang['date format']);
		$dati->dataLocale = DateFormat::dateFormating($dati->data, $_lang['date format']);
		if ($dati->company->location_comuni_id == 0) $dati->company->comune = $dati->company->comune_alt;
		if ($dati->company->location_province_id == 0) $dati->company->provincia = $dati->company->provincia_alt;
		if ($dati->user->location_comuni_id == 0) $dati->user->comune = $dati->user->comune_alt;
		if ($dati->user->location_province_id == 0) $dati->user->provincia = $dati->user->provincia_alt;

		$html = '
		<!DOCTYPE html>
		<html>
			<head>
				<base href="' . $basePath . '">
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Fattura</title>
				<link href="' . $templatePath . 'css/bootstrap.min.css" rel="stylesheet">
				<link href="' . $templatePath . 'css/font-awesome.css" rel="stylesheet">
				<link href="' . $templatePath . 'css/animate.css" rel="stylesheet">
				<link href="' . $templatePath . 'css/style.css" rel="stylesheet">
			</head>

			<body class="white-bg">
				<div class="wrapper wrapper-content p-xl">
					<div class="ibox-content p-xl">
						<div class="row">
							<div class="col">
								<h5>A:</h5>
								<address>
									<strong>' . $dati->company->ragione_sociale . '</strong><br>
									' . $dati->company->street . '<br>
									' . $dati->company->comune . ' ' . $dati->company->zip_code . ' ' . $dati->company->provincia . '<br>
									<abbr title="Phone">Telefono:</abbr> ' . $dati->fornitore->telephone . '
								</address>
							</div>
							<div class="col text-right">
								<h4>Numero ordine:
								<span class="text-navy">' . $dati->number . '</span></h4>
								<span>Da:</span>
								<address>
									<strong>' . $dati->user->name . ' ' . $dati->user->surname . '</strong><br>
									' . $dati->user->street . '<br>
									' . $dati->user->comune . ' ' . $dati->user->zip_code . ' ' . $dati->user->provincia . '<br>
									<abbr title="Phone">Telefono:</abbr> ' . $dati->user->telephone . '
								</address>
								<p>
									<span><strong>Data creazione ordine:</strong> ' . $dati->createdLocale . '</span><br/>
									<span><strong>Data ordine:</strong> ' . $dati->dataLocale . '</span>
								</p>
							</div>
						</div>
						<div class="table-responsive m-t">
							<table class="table invoice-table table-sm">
								<thead>
									<tr>
										<th>Nome</th>
										<th>Codice</th>
										<th>Prezzo</th>
										<th>Unità di misura</th>
										<th>Quantità</th>
									</tr>
								</thead>
								<tbody>
								';
		if (is_array($dati->products) && count($dati->products)) {
			foreach ($dati->products as $value) {
				$html .= '
											<tr>
												<td>' . $value->title . '</td>
												<td>' . $value->code . '</td><td>';
				if ($value->price > 0) {
					$html .= '€ ' . number_format($value->price, 2, ',', '.');
				}
				$html .= '</td><td>' . $value->attribute . '</td>
												<td>' . $value->quantity . '</td>
											</tr>';
			}
		}
		$html .= '
								</tbody>
							</table>
						</div><!-- /table-responsive -->
						<div class="well m-t"><strong>Note</strong>
						' . $dati->note . '
						</div>
					</div>
				</div>

			<!-- Mainly scripts -->
			<script src="js/jquery-3.1.1.min.js"></script>
			<script src="js/popper.min.js"></script>
			<script src="js/bootstrap.js"></script>

			</body>
		</html>';
		//echo $html;die();
		return $html;
	}
}
