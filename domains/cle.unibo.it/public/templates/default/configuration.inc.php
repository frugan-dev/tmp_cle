<?php
/* template default configuration.php v.3.5.4. 26/06/2019 */
$optMainMenu = array(
	'divIsMain'=>0,
	'ulMain'=>'<div class="nav-item">',
	'ulSubMenu'=>'<ul class="dropdown-menu" aria-labelledby="%LEVEL%-dropdown">',
	'ulSubSubMenu'=>'<ul id="L%LEVEL%-S%SONS%">',
	'ulDefault'=>'<div id="L%LEVEL%-S%SONS%">',	
	'liMain'=>'<div class="nav-item" id="L%LEVEL%-S%SONS%">',
	'liSubMenu'=>'<li class="nav-item dropdown %CLASSACTIVE%">',
	'liSubSubMenu'=>'<li id="L%LEVEL%-S%SONS%">',
	'liDefault'=>'<div>',
	'hrefMain'=>'<a class="nav-link %CLASSACTIVE%" data-info="L%LEVEL%-S%SONS%" href="%URL%" title="%URLTITLE%" target="%TARGET%">%TEXTACTIVE%%TITLE%</a>',
	'hrefSubMenu'=>'<a class="nav-link dropdown-toggle" id="%LEVEL%-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="%URL%" title="%URLTITLE%" target="%TARGET%">%TITLE%</a>',
	'hrefdefault'=>'<a class="" href="%URL%" title="%URLTITLE%" target="%TARGET%">%TITLE%</a>',
	'urldefault'=>'#!',
	'valueUrlDefault'=>'%SEOCLEAN%',
	'pagesModule'=>'',
	'classactive'=> ' active',
	'textactive' => ' <span class="sr-only">(current)</span>'
);


$optMainMenuDiv = array(
	'ulIsMain'=>0,

	'ulDefault' => '<ul aria-labelledby="navbarDropdownMenuLink%PARENT%" class="dropdown-menu">',
	'ulDefaultClose' => '</ul>',
	'ulMain'=>'',
	'ulMainClose'=>'',
	'ulSubMenu'=>'<ul aria-labelledby="navbarDropdownMenuLink%PARENT%" class="dropdown-menu">',
	'ulSubMenuClose'=>'</ul>',

	'ulSubSubMenuClose'=>'</li>',

	'liDefault'=>'<li>',
	'liDefaultClose'=>'</li>',
	'liMain'=>'<div class="nav-item">',
	'liMainClose'=>'</div>',
	'liSubMenu'=>'<div class="nav-item dropdown">',
	'liSubMenuClose'=>'</div>',
	'liSubSubMenu'=>'<li class="dropdown-submenu">',
	'liSubSubMenuClose'=>'</li>',

	'hrefDefault' => '<a href="%URL%" class="nav-link%CLASSACTIVE%">%URLTITLE%%TEXTACTIVE%',
	'hrefDefaultClose' => '</a>',

	'hrefMain' => '<a href="%URL%" class="dropdown-item nav-link%CLASSACTIVE%">%URLTITLE%%TEXTACTIVE%',
	'hrefMainClose' => '</a>',
	

	'hrefSubMenu' => '<a id="navbarDropdownMenuLink%ID%" href="%URL%" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">%URLTITLE% <i class="fa fa-angle-down"></i>',
	'hrefSubMenuClose' => '</a>',


	'urlDefault'=>'#!',

	'classactive'=> ' active',
	'textactive' => ' <span class="sr-only">(current)</span>'
);

$optMenuPagesDiv = array(
	'ulIsMain'=>0,

	'ulDefault' => '<ul aria-labelledby="navbarDropdownMenuLink%PARENT%" class="dropdown-menu">',
	'ulDefaultClose' => '</ul>',
	'ulMain'=>'',
	'ulMainClose'=>'',
	'ulSubMenu'=>'<ul aria-labelledby="navbarDropdownMenuLink%PARENT%" class="dropdown-menu">',
	'ulSubMenuClose'=>'</ul>',

	'ulSubSubMenuClose'=>'</li>',

	'liDefault'=>'<li>',
	'liDefaultClose'=>'</li>',
	'liMain'=>'<div class="nav-item">',
	'liMainClose'=>'</div>',
	'liSubMenu'=>'<div class="nav-item dropdown">',
	'liSubMenuClose'=>'</div>',
	'liSubSubMenu'=>'<li class="dropdown-submenu">',
	'liSubSubMenuClose'=>'</li>',

	'hrefDefault' => '<a href="%URL%" class="nav-link%CLASSACTIVE%">%URLTITLE%%TEXTACTIVE%',
	'hrefDefaultClose' => '</a>',

	'hrefMain' => '<a href="%URL%" class="dropdown-item nav-link%CLASSACTIVE%">%URLTITLE%%TEXTACTIVE%',
	'hrefMainClose' => '</a>',
	

	'hrefSubMenu' => '<a id="navbarDropdownMenuLink%ID%" href="%URL%" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">%URLTITLE% <i class="fa fa-angle-down"></i>',
	'hrefSubMenuClose' => '</a>',


	'urlDefault'=>'#!',

	'classactive'=> ' active',
	'textactive' => ' <span class="sr-only">(current)</span>'
);





$optMenuPages = array(
	'ulIsMain'=>0,
	'ulMain'=>'<ul id="L%LEVEL%-S%SONS%">',
	'ulSubMenu'=>'<ul class="dropdown-menu" aria-labelledby="%LEVEL%-dropdown">',
	'ulSubSubMenu'=>'<ul id="L%LEVEL%-S%SONS%">',
	'ulDefault'=>'<ul id="L%LEVEL%-S%SONS%">',	
	'liMain'=>'<li id="L%LEVEL%-S%SONS%">',
	'liSubMenu'=>'<li class="nav-item dropdown %CLASSACTIVE%">',
	'liSubSubMenu'=>'<li id="L%LEVEL%-S%SONS%">',
	'liDefault'=>'<li>',
	'hrefMain'=>'<a class="nav-link %CLASSACTIVE%" data-info="L%LEVEL%-S%SONS%" href="%URL%" title="%URLTITLE%">%TITLE%</a>',
	'hrefSubMenu'=>'<a class="nav-link dropdown-toggle" id="%LEVEL%-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="%URL%" title="%URLTITLE%">%TITLE%</a>',
	'hrefdefault'=>'<a class="" href="%URL%" title="%URLTITLE%">%TITLE%</a>',
	'urldefault'=>'#!',
	'valueUrlDefault'=>'%ID%/%SEOCLEAN%',
	'pagesModule'=>''
	);
	
$optMenuCategories = array(
	'ulIsMain'=>0,
	'ulMain'=>'<ul id="L%LEVEL%-S%SONS%">',
	'ulSubMenu'=>'<ul class="dropdown-menu" aria-labelledby="%LEVEL%-dropdown">',
	'ulSubSubMenu'=>'<ul id="L%LEVEL%-S%SONS%">',
	'ulDefault'=>'<ul id="L%LEVEL%-S%SONS%">',	
	'liMain'=>'<li id="L%LEVEL%-S%SONS%">',
	'liSubMenu'=>'<li class="nav-item dropdown %CLASSACTIVE%">',
	'liSubSubMenu'=>'<li id="L%LEVEL%-S%SONS%">',
	'liDefault'=>'<li>',
	'hrefMain'=>'<a class="nav-link %CLASSACTIVE%" data-info="L%LEVEL%-S%SONS%" href="%URL%" title="%URLTITLE%">%TITLE%</a>',
	'hrefSubMenu'=>'<a class="nav-link dropdown-toggle" id="%LEVEL%-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="%URL%" title="%URLTITLE%">%TITLE%</a>',
	'hrefdefault'=>'<a class="" href="%URL%" title="%URLTITLE%">%TITLE%</a>',
	'urldefault'=>'#!',
	'valueUrlDefault'=>'products/%ID%/%SEOCLEAN%',
	'pagesModule'=>''
	);
	
$templateLanguagesBar = array(
	'container'=>'<ul class="dropdown-menu mb-0 dropdown-lingue" role="menu">%LINKS%</ul>',
	'links'=>'<li class=""><a title="%TITLE%" href="%URL%">%TITLE%</a></li>',
	'links active'=>'<li class=""><a class="active" title="%TITLE%" href="%URL%">%TITLE%</a></li>',
	);
		
$templateBreadcrumbsBar = array(
	'container'=>'<ul class="dropdown-menu mb-0 dropdown-lingue" role="menu">%LINKS%</ul>',
	'links home'=>'<li class="breadcrumb-item"><i class="fa fa-home pr-2"></i><a class="link-dark" href="%URL%" title="%TITLE%">%TITLE%</a></li>',
	'nolinks'=>'<li class="breadcrumb-item">%TITLE%</li>',
	'links'=>'<li class="breadcrumb-item"><a class="link-dark" href="%URL%" title="%TITLE%">%TITLE%</a></li>',
	'links active'=>'<li class="breadcrumb-item active">%TITLE%</li>'
	);
	
	
$templateMessagesBar = '<div class="container"><div class="row"><div class="col-12"><div class="alert%CLASS%">%CONTENT%</div></div></div></div>';
?>