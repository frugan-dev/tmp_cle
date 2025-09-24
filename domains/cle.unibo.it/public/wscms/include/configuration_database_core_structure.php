<?php

/* RESOURCES */
$DatabaseTables['resources for item'] = '';
$DatabaseTablesFields['resources for item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_owner'=>array('label'=>'IDOwner','required'=>true,'searchTable'=>false,'type'=>'int'),
	'resource_type'=>array('label'=>'Type resource','required'=>true,'searchTable'=>false,'type'=>'int'),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>true,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'extension'=>array('label'=>'Ext','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'code'                                      => array(
        'label'                                 => 'Code',
        'searchTable'                           => false,
        'required'                              => false,
        'type'                                  => 'varchar',
        'defValue'                              => '',
    ),
	'size_file'=>array('label'=>'Dimensione','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'size_image'                                => array('label'=>'Dimensione','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'type'=>array('label'=>'Tipo','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>self::$langVars['ordinamento'],'required'=>false,'type'=>'int|8','validate'=>'int','defValue'=>1),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => self::$nowDateTimeIso,
		'forcedValue'                           => self::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	)
);
foreach(self::$globalSettings['languages'] AS $lang) {
	$searchTable = true;
	$required = ($lang == self::$langVars['user'] ? true : false);
	$DatabaseTablesFields['resources for item']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>$searchTable,'required'=>$required,'type'=>'varchar');
	$DatabaseTablesFields['resources for item']['content_'.$lang] = array('label'=>self::$langVars['contenuto'].'  '.$lang,'searchTable'=>true,'required'=>false,
        'type'                                  => 'text',
        'defValue'                              => ''
    );	
}


/*
// settings
$DatabaseTables['settings']  = self::$dbTablePrefix . 'settings';
$DatabaseTablesFields['settings'] = array(
    'id'                                                => array(
        'label'                                         => 'ID',
        'required'                                      => false,
        'type'                                          => 'autoinc',
        'primary'                                       => true
    ),
    'keyword'                                           => array(
        'label'                                         => 'Keyword',
        'required'                                      => true,
        'searchTable'                                   => true,
        'type'						        	        => 'varchar|255',
        'defValue'                                      => ''
    ),
    'value'                                             => array(
        'label'                                         => 'Value',
        'required'                                      => true,
        'searchTable'                                   => true,
        'type'					        		        => 'varchar|10000',
        'defValue'                                      => ''
    ),
    'comment'                                           => array(
        'label'                                         => 'Commento',
        'required'                                      => true,
        'searchTable'                                   => true,
        'type'					        		        => 'varchar|512',
        'defValue'                                      => ''
    )
);

// users
$DatabaseTables['users']  = self::$dbTablePrefix . 'users';
$DatabaseTablesFields['users'] = array(
    'id'                                                => array(
        'label'                                         => 'ID',
        'required'                                      => false,
        'type'                                          => 'autoinc',
        'primary'                                       => true
    ),
    'username'                                          => array(
        'label'                                         => Config::$langVars['nome utente'],
        'searchTable'                                   => true, 
        'required'                                      => true,
        'type'                                          => 'varchar|255',
        'validate'                                      => 'username',
        'errorMessage'                                  => preg_replace('/%ITEM%/',Config::$langVars['nome utente'],Config::$langVars['Devi inserire una %ITEM%!']),
        'errorValidateMessage'                          => preg_replace('/%ITEM%/',Config::$langVars['nome utente'],Config::$langVars['Il valore per il campo %ITEM% non è stato validato!']),
    ),
    'password'                                          => array(
        'label'                                         => Config::$langVars['password'],
        'searchTable'                                   => false,
        'required'                                      => true,
        'type'                                          => 'password'
    ),
    'name'                                              => array(
        'label'                                         => Config::$langVars['nome'],
        'searchTable'                                   => true,
        'required'                                      => true,
        'type'                                          => 'varchar|255'),
    'surname'                                           => array(
        'label'                                         => Config::$langVars['cognome'],
        'searchTable'                                   => true,
        'required'                                      => true,
        'type'                                          => 'varchar'
    ),
    'street'                                            => array(
        'label'                                         => Config::$langVars['via'],
        'searchTable'                                   => false,
        'required'                                      => true,
        'type'                                          => 'varchar'
    ),
    'location_comuni_id'                                => array(
        'label'                                         => Config::$langVars['comune'],
        'searchTable'                                   => false,
        'required'                                      => true, 
        'type'                                          => 'int|10',
        'defValue'                                      => 0
    ),
    'comune_alt'                                        => array(
        'label'                                         => Config::$langVars['altro comune'],
        'searchTable'                                   => false,
        'required'                                      => false, 
        'type'                                          => 'varchar|150'
    ),
    'zip_code'                                          => array(
        'label'                                         => Config::$langVars['c.a.p.'],
        'searchTable'                                   => false,
        'required'                                      => true,
        'type'                                          => 'varchar'
    ),
    'location_province_id'                              => array(
        'label'                                         => Config::$langVars['provincia'],
        'searchTable'                                   => false,
        'required'                                      => true,
        'type'                                          => 'int|10',
        'defValue'                                      => 0
    ),
    'provincia_alt'                                     => array(
        'label'                                         => Config::$langVars['altra provincia'],
        'searchTable'                                   => true,
        'required'                                      => false,
        'type'                                          => 'varchar|150',
        'defValue'                                      => ''
    ),
    'location_nations_id'                               => array(
        'label'                                         => Config::$langVars['nazione'],
        'searchTable'                                   => false,
        'required'                                      => false,
        'type'                                          => 'int|10',
        'defValue'                                      => 0
    ),
    'telephone'                                         => array(
        'label'                                         => Config::$langVars['telefono'],
        'searchTable'                                   => false, 
        'required'                                      => true, 
        'type'                                          => 'varchar|20',
        'validate'                                      => 'telephonenumber',
        'errorValidateMessage'                          => preg_replace('/%ITEM%/',ucfirst(Config::$langVars['numero di telefono']),Config::$langVars['%ITEM% non valido!'])
    ),
    'email'                                             => array(
        'label'                                         => Config::$langVars['email'],
        'searchTable'                                   => true,
        'required'                                      => true,
        'type'                                          => 'varchar|255',
        'defValue'                                      => '',
        'validate'                                      => 'isemail',
        'errorMessage'                                  => preg_replace('/%ITEM%/',Config::$langVars['email'],Config::$langVars['Devi inserire una %ITEM%!']),
        'errorValidateMessage'                          => preg_replace('/%ITEM%/',Config::$langVars['email'],Config::$langVars['Il valore per il campo %ITEM% non è stato validato!']),
    ),
    'mobile'                                            => array(
        'label'                                         => Config::$langVars['mobile'],
        'searchTable'                                   => true,
        'required'                                      => false,
        'type'                                          => 'varchar'
    ),
    'fax'                                               => array(
        'label'                                         => Config::$langVars['fax'],
        'searchTable'                                   => true,
        'required'                                      => false,
        'type'                                          => 'varchar'
    ),
    'skype'                                             => array(
        'label'                                         => Config::$langVars['skype'],
        'searchTable'                                   => true,
        'type'                                          => 'varchar'
    ),
    'avatar'                                            => array(
        'label'                                         => Config::$langVars['avatar'],
        'searchTable'                                   => false,
        'type'                                          => 'blob'
    ),
    'avatar_info'                                       => array(
        'label'                                         => Config::$langVars['avatar info'],
        'searchTable'                                   => false,
        'type'                                          => 'varchar|255'
    ),
    'id_level'                                          => array(
        'label'                                         => Config::$langVars['livello'],
        'searchTable'                                   => false,
        'type'                                          => 'int|1'
    ),
    'is_root'                                           => array(
        'label'                                         => 'Root',
        'searchTable'                                   => false,
        'type'                                          => 'varchar',
        'defValue'                                      => 0
    ),
    'in_admin'                                          => array(
        'label'                                         => Config::$langVars['amministrazione'],
        'searchTable'                                   => false,
        'type'                                          => 'varchar',
        'defValue'                                      => 0
    ),
    'from_site'                                         => array(
        'label'                                         => Config::$langVars['dal sito'],
        'searchTable'                                   => false,
        'type'                                          => 'varchar',
        'defValue'                                      => 0
    ),
    'template'                                          => array(
        'label'                                         => 'template',
        'searchTable'                                   => false,
        'type'                                          => 'varchar|100'
    ),
    'hash'                        	                    => array(
        'label'                                         => 'Hash',
        'searchTable'                                   => false,
        'type'                                          => 'varchar'
    ),
	'created'									        => array(
		'label'									        => Config::$langVars['creazione'],
		'searchTable'							        => false,
		'required'								        => false,
		'type'									        => 'datatime',
		'defValue'								        => Config::$nowDateTimeIso,
		'validate'								        => 'datetimeiso',
		'forcedValue'              				        => self::$nowDateTime
	),
	'active'									        => array(
		'label'									        => Config::$langVars['attiva'],
		'required'								        => false,
		'type'									        => 'int|1',
		'validate'			    				        => 'int',
		'defValue'								        => '0',
		'forcedValue'              				        => 1
	));


*/

    ?>
