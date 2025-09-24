<?php
/*	wscms/classes/class.EcomOrder.php v.3.3.0. 21/06/2017 */

class EcomOrder extends Core {
	
	/* campi tabella order_customer per form */
	private static $order_customers_fields = ['id_order','name','surname','street','city','zip_code','province','id_state','name_dest','surname_dest','street_dest','city_dest','zip_code_dest','province_dest','id_state_dest','telephone','email','mobile','fax','codice_fiscale','partita_iva'];		
	private static $order_fields = ['id','total','created','note','done','deleted','lang'];	
	private static $order_products_fields = ['id_order','title','quantity','price'];		
	
	private static $site_states;

	private static $orderId;
	private static $orderData;
	private static $orderTotal;
	
	private static $basket;

	public function __construct() {
		parent::__construct();
		}

	public static function orderInit($id_order = '') {
		self::$resultOp->error = 0;
		self::setOrderId($id_order);
		//echo '<br>id_basket : '.self::$basketId;
		//echo '<br>session_basket : '.self::$basketSession;
		}
		
	public static function saveOrder() {
		self::orderInit('');
		self::saveOrderDetails();
		self::saveOrderCustomer();
		self::saveOrderProducts();	
		}

	public static function saveOrderCustomer() {
		if (Core::$resultOp->error == 0) {
			/* integra con i campi mancanti */
			$_POST['partita_iva'] = '';
			$_POST['codice_fiscale'] = '';
			$_POST['fax'] = '';
			$_POST['mobile'] = '';
			$_POST['id_order'] = self::$orderId;	
			if ($_POST['id_state_dest'] == '') $_POST['id_state_dest'] = 0;
			$fielsValue = Sql::getFieldsValueFromFieldDbAndPost(self::$order_customers_fields);
			Sql::initQuery(DB_TABLE_PREFIX.'ec_order_customers',self::$order_customers_fields,$fielsValue,'');
			Sql::insertRecord();
			}
		}

	public static function saveOrderProducts() {
		if (Core::$resultOp->error == 0) {
			self::setOrderBasket();		
			if (isset(self::$basket->products) && is_array(self::$basket->products) && count(self::$basket->products) > 0) {
				foreach (self::$basket->products AS $key=>$value) {
					$fielsValue = [self::$orderId,$value->title,$value->quantity,$value->price];				
					Sql::initQuery(DB_TABLE_PREFIX.'ec_order_products',self::$order_products_fields,$fielsValue,'');
					Sql::insertRecord();
					if (Core::$resultOp->error == 1) break;
					}
				}
			}
		}
		
	public static function saveOrderDetails() {
		if (Core::$resultOp->error == 0) {		
			//self::$orderId = time(); /* RIMUOVERE IN PRODUZIONE */
			/* controlla se esiste un'altro ordine con lo stesso id */
			if (Sql::countRecordQry(DB_TABLE_PREFIX.'ec_orders','id','id LIKE ?',[self::$orderId]) == 0) {
				self::setOrderBasket();
							
				self::$orderData = date('Y-m-d H:i:s');
				self::$orderTotal = self::$basket->total;
				$fielsValue = [self::$orderId,self::$orderTotal,self::$orderData,$_POST['note'],0,0,self::$sessionValues['front-end']['lang']];
				Sql::initQuery(DB_TABLE_PREFIX.'ec_orders',self::$order_fields,$fielsValue,'');
				Sql::insertRecord();
				} else {
					Core::$resultOp->error = 1;
					Core::$resultOp->messages[] = 'Ordine con lo stesso id già presente!';
					}	
			}
		}
	
	public static function setOrderId($id_order) {
		if (isset(self::$sessionValues['ecomm']['orderid']) && self::$sessionValues['ecomm']['orderid'] != '') {
			self::$orderId = self::$sessionValues['ecomm']['orderid'];
			} else {				
				self::$orderId = $id_order;
				if (self::$orderId == '') {
					/* genera un id ordine custom */
					self::$orderId = time();
					//self::$orderId = "testordine";
					} else if (isset(self::$sessionValues['ecomm']['basketid'])) {
						self::$orderId = self::$sessionValues['ecomm']['basketid'];
						} else {
							self::$orderId = self::$orderId = time();
							}			
				/* memorizza in sessione database */
				$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
				$my_session->my_session_start();
				self::$sessionValues = [];
				self::$sessionValues = $my_session->my_session_read();
				$my_session->addSessionsModuleSingleVar(self::$sessionValues,'ecomm','orderid',self::$orderId);
				//echo 'action setOrderId<br>orderId: '.self::$orderId;
				//print_r(self::$sessionValues);
				}	
		}
	
	public static function resetOrderSession() {
		if (isset(self::$sessionValues['ecomm']['orderid'])) {
			/* memorizza in sessione database */
			$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
			$my_session->my_session_start();
			self::$sessionValues = [];
			self::$sessionValues = $my_session->my_session_read();
			$my_session->addSessionsModuleSingleVar(self::$sessionValues,'ecomm','orderid','');		
			//echo 'action resetOrderSession';
			//print_r(self::$sessionValues);
			}	
		}

	public static function setOrderBasket() {
		if (Core::$resultOp->error == 0) {
			self::$basket = EcomBasket::getBasketDetails();	
			}		
		}	
		
	public static function sendOrderEmailConfirmToCustomer($globalSettings,$lang,$opt=[]) {
		if (Core::$resultOp->error == 0 && self::$orderId != '') {
			$opt = [];
			/* manda la email di conferma ordine al cliente */								
			$subject = $lang['ecom - cliente - soggetto email conferma ordine'];			
			$opt['subject'] = Mails::parseEmailContent($subject,$_POST,$opt1=[]);				
			$content = $lang['ecom - cliente - contenuto email conferma ordine'];
			$opt['content'] = Mails::parseEmailContent($content,$_POST,$opzz=[]);	
			//FIXED - DKIM requirements							
			$opt['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['ecom - from email'];
			$opt['from label'] = $globalSettings['ecom - from email label'];
			$opt['replyTo'] = [$globalSettings['ecom - from email'] => $globalSettings['ecom - from email label']];
			$opt['to address'] = $_POST['email'];				
			$opt['send copy'] = $globalSettings['ecom - send email debug'];
			$opt['copy email'] = $globalSettings['ecom - email debug'] 	;	
			//echo '<br>subject: '.$opt['subject'];
			//echo '<br>content: '.$opt['content'];			
			//$opt['smtp server'] = '172.16.31.89';	
			//$opt['smtp server port'] = '25';	
			//$opt['smtp server user'] = 'smtpauth@hibot.co.jp';
			//$opt['smtp server password'] = '*BuildRobotMakeMoney*';							
			Mails::sendEmail($opt);
			if (Core::$resultOp->error == 1) Core::$resultOp->messages[] = $lang['La tua email di conferma ordine NON è stata spedita! Riprova.'];
			}		
		}	
		
	public static function sendOrderEmailToShop($globalSettings,$lang,$opt=[]) {
		
		//echo 'orderId: '.self::$orderId;
		//echo 'orderData: '.self::$orderData;
		//echo 'orderTotal: '.self::$orderTotal;
		//print_r(self::$sessionValues);
		
		if (Core::$resultOp->error == 0 && self::$orderId != '') {
			$optDef = ['orderid'=>'','orderdata'=>'','ordertotal'=>''];
			$opt = array_merge($optDef,$opt);
			/* manda la email di conferma ordine al cliente */						
			$subject = $lang['ecom - staff - soggetto email conferma ordine'];			
			$opt['subject'] = Mails::parseEmailContent($subject,$_POST,$opt1=[]);				
			$content = $lang['ecom - staff - contenuto email conferma ordine'];
			$ulradminorder = '<a href="'.URL_SITE_ADMIN.'ecommerce/viewOrdi/'.self::$orderId.'" target="_blank">'.self::$orderId.'</a>';			
			$opt1 = [];
			$opt1['others']['%URLADMINORDER%'] = $ulradminorder;
			$opt1['others']['%ORDERDATA%'] = self::$orderData;
			$opt1['others']['%ORDERTOTAL%'] = number_format(self::$orderTotal,0,',',',');
			$opt['content'] = Mails::parseEmailContent($content,$_POST,$opt1);
			//FIXED - DKIM requirements				
			$opt['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['ecom - from email'];
			$opt['from label'] = $globalSettings['ecom - from email label'];
			$opt['replyTo'] = [$globalSettings['ecom - from email'] => $globalSettings['ecom - from email label']];
			$opt['to address'] = $globalSettings['ecom - from email'];
			$opt['send copy'] = $globalSettings['ecom - send email debug'];
			$opt['copy email'] = $globalSettings['ecom - email debug'];
			//echo '<br>subject: '.$opt['subject'];
			//echo '<br>content: '.$opt['content'];			
			//$opt['smtp server'] = '172.16.31.89';	
			//$opt['smtp server port'] = '25';	
			//$opt['smtp server user'] = 'smtpauth@hibot.co.jp';
			//$opt['smtp server password'] = '*BuildRobotMakeMoney*';	
			Mails::sendEmail($opt);
			if (Core::$resultOp->error == 1) Core::$resultOp->messages[] = $lang['La email di conferma ordine allo staff del sito NON è stata spedita! Riprova.'];									
			}		
		}	
		
	public static function getOrderData() {	
		return self::$orderData;
		}	
	
	public static function getOrderTotal() {	
		return self::$orderTotal;
		}
			
	public static function getOrderId() {	
		return self::$orderId;
		}		
	/* DA VERIFICARE POSIZIONE TEMPORANEA */
	
	public function getSiteStates() {
		/* trova gli stati */
		Sql::initQuery(DB_TABLE_PREFIX.'site_states',['*'],[],'');
		Sql::setOptions(['fieldTokeyObj'=>'id']);
		$obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = [];
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {	
				$field = 'title_'.$_lang['user'];	
				$value->title = $value->$field;
				$arr[$key] = $value;
				}
			}
		self::$site_states = $arr;
		}


	}
?>