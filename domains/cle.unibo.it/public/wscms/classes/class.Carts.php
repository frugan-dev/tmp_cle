<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	admin/classes/class.Carts.php v.4.5.1. 23/07/2020
*/

class Carts extends Core {
	
	public static $dbTableCategories = '';
	public static $categories_id = '';

    public static $dbTable = ''; 
    public static $dbTablePro = ''; 
	public static $dbTableCartPro = '';
	
    public static $carts_id = NULL;
    public static $sessionId = '';
    public static $token = '';
	public static $langUser = '';

	public static $companies_code = ''; // vecchio 
	public static $cartCompaniesCode = '';
	public static $cartCompaniesCodeAlt = '';

	public static $users_id = '0';

	public static $cartProducts = '';
	public static $total_products_quantity = '0';
	

    public function __construct(){
		parent::__construct();  		
	}

	public static function setCartUserId() 
	{
		//Sql::setDebugMode(1);
		// preleva il carrello di sessione
		Sql::initQuery(self::$dbTable,['*'],[self::$users_id,self::$sessionId,self::$token,self::$companies_code],'users_id = ? AND session_id = ? AND token = ? AND companies_code = ?','created ASC',' LIMIT 1','',false);
		$foo = Sql::getRecord();
		//ToolsStrings::dumpArray($foo);

		if (isset($foo->id)) {
			self::$carts_id = $foo->id;
		} else {
			// altrimenti prende ltimo senza sessione
			Sql::initQuery(self::$dbTable,['*'],[self::$users_id,self::$companies_code],'users_id = ? AND companies_code = ?','created ASC',' LIMIT 1','',false);
			$foo = Sql::getRecord();
			if (isset($foo->id)) {
				self::$carts_id = $foo->id;
			} else {
				self::$carts_id = 0;
			}
		}
		//echo '<br>self::$carts_id: ' . self::$carts_id;
		die('fatto');
	}
	
	public static function setCarts() 
	{
		// controlla se è in sessione
		self::checkCartIfIsInPhpSession();
	}

	public static function checkCartIfIsInPhpSession() 
	{
		if (!isset($_SESSION['carts_id'])) {
			self::addCartNew();
			$_SESSION['carts_id'] = intval(self::$carts_id);
		} else {
			self::$carts_id = intval($_SESSION['carts_id']);
		}	

		// controlla se esiste nel db
		if (self::checkCartIfExists() == false) {
			self::addCartNew();
			$_SESSION['carts_id'] = intval(self::$carts_id);
		}
		//echo '<br>$carts_id: '.self::$carts_id;
	}

	public static function addCartNew() 
	{
    	$f = ['session_id','token','companies_code','users_id','created'];
    	$fv = [self::$sessionId,self::$token,self::$companies_code,self::$users_id,Config::$nowDateTimeIso];
        Sql::initQuery(self::$dbTable,$f,$fv,'','','','',false);
        Sql::insertRecord();   
        self::$carts_id = Sql::getLastInsertedIdVar();
    }
    
	public static function checkCartIfExists() 
	{
    	$result = false;
    	if (Sql::countRecordQry(self::$dbTable,'id','id = ?',[self::$carts_id]) > 0) {   		
    		$result = true;
    	}	
    	return $result;
    }
    
	public static function setCartQtyPro($id,$products_quantity) 
	{
		//Sql::setDebugMode(1);
		// prende dati riga prodotto
		if (intval($id) > 0) {
			Sql::initQuery(self::$dbTableCartPro,['*'],[self::$carts_id,$id],'carts_id = ? AND id = ?','','','',false);
			$product = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
			if (isset($product->id)) { 
				if (intval($products_quantity) > 0) {
					$product->quantity = $products_quantity;
					Sql::initQuery(self::$dbTableCartPro,
					['quantity'],
					[$product->quantity,self::$carts_id,$id]
					,'carts_id = ? AND id = ?','','','',false);
					Sql::updateRecord();
					if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
				}			
			}
		}
	}
	
	public static function clearCartProducts() 
	{	
		Sql::initQuery(self::$dbTableCartPro,['id'],[self::$carts_id],'carts_id = ?','','','',false);
		Sql::deleteRecord();
		if (Core::$resultOp->error > 0) { die('errore db delete cart product'); ToolsStrings::redirect(URL_SITE.'error/db'); }	
	}
	
	public static function deleteCart() 
	{	
		self::clearCartProducts();
		Sql::initQuery(self::$dbTable,['id'],[self::$carts_id],'id = ?','','','',false);
		Sql::deleteRecord();
		if (Core::$resultOp->error > 0) { die('errore db cancellazione carrello'); }	
		self::$carts_id = 0; 
		$_SESSION['carts_id'] = 0;	
    }
    
	public static function delCartProduct($id) 
	{
		Sql::initQuery(self::$dbTableCartPro,['id'],[$id],'id = ?','','','',false);
		Sql::deleteRecord();
		if (Core::$resultOp->error > 0) { die('Errore db cancellazione prodotto'); }
    }
    
	public static function addCartProduct($id,$quantity = 1,$attributes_id = 0) 
	{
		Sql::setDebugMode(1);
		$attributes = Products::getAttributesList($_SESSION['globalCompanyCodeDefaultCode']); 
		//ToolsStrings::dumpArray($attributes);//die('fatto1');
		if ($id  > 0  && $quantity > 0) {
			$attribute = ($attributes[$attributes_id]->title ?? '');
			//echo '<br>attributes_id: '.$attributes_id;
			//echo '<br>attribute: '.$attribute;

			Products::setDbTable(self::$dbTablePro);
			Products::setLangUser(self::$langUser);
			$product = Products::getProductDetails($id);
			ToolsStrings::dumpArray($product);//die('fatto1');

			Sql::setDebugMode(1);
			Config::$queryParams = [];
			Config::$queryParams['tables'] = Config::$DatabaseTables['carts products'];
			Config::$queryParams['fieldsVal'] = [$product->id,$product->users_id,$product->categories_id,$product->companies_code];
			Config::$queryParams['where'] = 'products_id = ? AND users_id = ? AND categories_id = ? AND companies_code = ?';
			Config::$queryParams['fieldsVal'] = [$product->id,self::$users_id,$attribute];
			Config::$queryParams['where'] = 'products_id = ? AND users_id = ? AND attribute = ?';
			Sql::initQuery(Config::$queryParams['tables'],['*'],Config::$queryParams['fieldsVal'],Config::$queryParams['where']);
			$foo = Sql::getRecord();
			if (Core::$resultOp->error > 0) { die('Errore lettura prodotto per vedere se gia esistente'); }
			//ToolsStrings::dumpArray($foo);die('fatto2');

			if (isset($foo->id) && intval($foo->id) > 0) {
				//ToolsSt ings::dump($foo);
				$qty = $foo->quantity + $quantity;
				Config::$queryParams = [];
				Config::$queryParams['tables'] = Config::$DatabaseTables['carts products'];
				Config::$queryParams['fields'] = ['quantity'];
				Config::$queryParams['fieldsVal'] = [$qty,$foo->id];
				Config::$queryParams['where'] = 'id = ?';
				ToolsStrings::dump(Config::$queryParams);
				Sql::initQuery(Config::$queryParams['tables'],Config::$queryParams['fields'],Config::$queryParams['fieldsVal'],Config::$queryParams['where']);
				Sql::updateRecord();
				if (Core::$resultOp->error > 0) { die('Errore aggiornamento quantita prodotto gia esistente'); }	
			} else {

				// salva il prodotto
				$f = [
					'carts_id',
					'users_id',
					'categories_id',
					'products_id',
					'code',
					'price',
					'quantity',
					"companies_code",
					"attribute"
				];
				$fv = [
					self::$carts_id,
					self::$users_id,				
					$product->categories_id,
					$product->id,
					$product->code,
					$product->price,
					$quantity,
					$product->companies_code,
					$attribute		    	
				];
				Sql::initQuery(self::$dbTableCartPro,$f,$fv,'','','','',false);
				Sql::insertRecord();
				if (Core::$resultOp->error > 0) { die('Errore inserimento nuovo prodotto'); }	
				$carts_products_id = Sql::getLastInsertedIdVar();
				

			}
			
			return true;
		} else {
			return false;
		}	
    }
    
    public static function loadCartProducts() {
		//Sql::setDebugMode(1);
    	$obj = [];
     	$f = ['cp.*','product.title'];
    	$fv = [self::$carts_id];
    	$clause = 'cp.carts_id = ?';
        Sql::initQuery(self::$dbTableCartPro.' AS cp INNER JOIN '.self::$dbTablePro.' AS product ON (cp.products_id = product.id)',$f,$fv,$clause,'','','',false);
       	//Sql::setOptAddRowFields(1);
		$pdoObject = Sql::getPdoObjRecords();	
		if (Core::$resultOp->error > 0) {	ToolsStrings::redirect(URL_SITE.'error/db'); }	
		self::$total_products_quantity = 0;
		while ($row = $pdoObject->fetch()) {
			//totali
			self::$total_products_quantity += $row->quantity;
			// pende gli attributi prodotto
			$obja = [];
     		$f = ['name,label,value'];
			$fv = [$row->id];
    		$clause = 'carts_products_id = ?';
			$obja = Sql::getRecords();	
			$row->attributes = $obja;
			$obj[] = $row;		
		}		
		//ToolsStrings::dumpArray($obj);
		self::$cartProducts = $obj;	
	}
	
}