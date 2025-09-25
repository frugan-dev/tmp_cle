<!-- wscms/contacts/formConf.tpl.php v.3.5.4. 07/05/2019 -->
<div class="row">
	<div class="col-md-3 new"></div>
	<div class="col-md-7 help-small-form">{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}</div>
	<div class="col-md-2 help text-right">
		{% if (App.params.help is defined) and (App.params.help != '') %}
			<button class="btn btn-warning btn-sm" type="button" data-target="#helpModal" data-toggle="modal">Come funziona?</button>
		{% endif %}
	</div>
</div>

<div class="card shadow mt-3 mb-4">
	<div class="card-body">

		<ul class="nav nav-tabs" id="formTab" role="tablist">		

			<li class="nav-item">
				<a class="nav-link active" href="#datibase" data-toggle="tab" role="tab" aria-controls="datibase" aria-selected="false">{{ Lang['dati base']|capitalize }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#admin" data-toggle="tab" role="tab" aria-controls="admin" aria-selected="false">{{ Lang['amministratore']|capitalize }} {{ Lang['sito'] }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#user" data-toggle="tab" role="tab" aria-controls="user" aria-selected="false">{{ Lang['utente']|capitalize }} {{ Lang['sito'] }}</a>
			</li>
			{% for lang in GlobalSettings['languages'] %}		
			<li class="nav-item">
				<a class="nav-link" href="#contents{{ lang }}" data-toggle="tab" role="tab" aria-controls="comtents{{ lang }}" aria-selected="false">{{ Lang['contenuti']|capitalize }} {{ lang }}</a>
			</li>
			{% endfor %}

			{% for lang in GlobalSettings['languages'] %}		
			<li class="nav-item">
				<a class="nav-link" href="#metatags{{ lang }}" data-toggle="tab" role="tab" aria-controls="metatags{{ lang }}" aria-selected="false">Meta Tags {{ lang }}</a>
			</li>
			{% endfor %}

			<li class="nav-item">
				<a class="nav-link" href="#map" data-toggle="tab" role="tab" aria-controls="map" aria-selected="false">Mappa</a>
			</li>

  		</ul>

		<form id="applicationForm" method="post" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">
			
			<!-- Tab panes -->	
			<div class="tab-content" id="formTabContent">

				<div class="tab-pane fade show active" id="datibase" role="tabpanel" aria-labelledby="datibase">
					<fieldset>	
						
					
					<div class="form-group row">
							<label for="image_headerID" class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">{{ Lang['immagine']|capitalize }} Header</label>	
							<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">	
								<div class="custom-file">
									<input
									type="file" 
									name="image_header" 
									id="image_headerID" 
									class="custom-file-input"
									>
									<label class="custom-file-label" for="image_headerID">{{ Lang['indica un file da caricare']|capitalize }}</label>    							
								</div>
							</div>
						</div>
							
						<div class="form-group row">
							<label class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">{{ Lang['anteprima']|capitalize }}</label>
							<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
								{% if App.item.image_header is defined and App.item.image_header != '' %}
								<a class="" href="{{ App.params.uploadDirs['conf'] }}{{ App.item.image_header }}" data-lightbox="image-1" data-title="{{ value.org_image_header }}" title="{{ App.item.org_image_header }}">
									<img  class="img-miniature"  src="{{ App.params.uploadDirs['conf'] }}{{ App.item.image_header }}" alt="{{ App.item.org_image_header }}">
								</a>							
								{% else %}
									<img class="img-miniature"  src="{{ UPLOADDIR }}default/image.png" alt="{{ LocalStrings['immagine di default']|capitalize }}">	
								{% endif %}
							</div>			
						</div>
						{% if App.item.image_header is defined and App.item.image_header != '' %}
						
						<div class="form-group row">
							<label class="col-sm-2 col-md-2 col-lg-2 col-xl-2 col-form-label col-form-label-custom-checkbox">
								{{ Lang['cancella file']|capitalize }}
							</label>	
							<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">		
								<div class="custom-control custom-checkbox">
									<input 
									name="deleteFilenameHeader" 
									id="deleteFilenameHeaderID" 
									value="1" 
									type="checkbox" 
									class="custom-control-input"
									>
									<label class="custom-control-label" for="deleteFilenameHeaderID"></label>
								</div>
							</div>
						</div>
						
						{% endif %}
						<hr>
						<div class="form-group row">
							<label for="email_addressID" class="col-md-5 control-label">
								{{ Lang['indirizzo email']|capitalize }}
								<span>{{ Lang['indirizzo email - titolo']|capitalize }}</span>
							</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="email_address" placeholder="{{ Lang['inserisci un indirizzo email']|capitalize }}" id="email_addressID" value="{{ App.item.email_address|e('html') }}">
							</div>
						</div>
						<div class="form-group row">
							<label for="label_email_addressID" class="col-md-5 control-label">
								{{ Lang['etichetta']|capitalize }} {{ Lang['indirizzo email'] }}
								<span>{{ Lang['label indirizzo email - titolo']|capitalize }}</span>
							</label>				
							<div class="col-md-6">
								<input type="text" class="form-control" name="label_email_address" placeholder="{{ Lang['inserisci una etichetta']|capitalize }}" id="label_email_addressID" value="{{ App.item.label_email_address|e('html') }}">
							</div>
						</div>
						<hr>
						<div class="form-group row">
							<label for="url_privacy_pageID" class="col-md-5 control-label text-left">
								{{ Lang['url privacy page'] }}
								<span>{{ Lang['url privacy page - titolo'] }}</span>
							</label>							
							<div class="col-md-6">
								<input type="text" class="form-control" name="url_privacy_page" placeholder="{{ Lang['inserisci un url']|capitalize }}" id="url_privacy_pageID" value="{{ App.item.url_privacy_page|e('html') }}">
							</div>
						</div>
						
						<hr>
						<div class="form-group row">
							<label for="email_addressID" class="col-md-5 control-label">
								{{ Lang['invia email debug']|capitalize }}
								<span>{{ Lang['email debug - titolo']|capitalize }}</span>
							</label>
							<div class="col-md-7">
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" name="send_email_debug" id="send_email_debugID"{% if App.item.send_email_debug == 1 %} checked="checked"{% endif %} value="1">
									</label>
	     						</div>
	   					</div>
						</div>
						<div class="form-group row">
							<label for="email_debugID" class="col-md-5 control-label text-left">
								{{ Lang['indirizzo email'] }} {{ Lang['debug'] }}
								<span>{{ Lang['indirizzo email debug - titolo'] }}</span>
							</label>
							
							<div class="col-md-6">
								<input type="text" class="form-control" name="email_debug" placeholder="{{ Lang['inserisci un indirizzo email']|capitalize }}" id="email_for_debugID" value="{{ App.item.email_debug|e('html') }}">
							</div>
						</div>
						
					</fieldset>
				</div>
				
				<div class="tab-pane fade show" id="admin" role="tabpanel" aria-labelledby="admin">
					<fieldset>
					<div class="form-group row">
							<label for="admin_email_subjectID" class="col-md-5 control-label">
								{{ Lang['soggetto email admin'] }}
								<span>{{ Lang['soggetto email admin - titolo'] }}</span>
							</label>
							<div class="col-md-6">
								<textarea  rows="2" class="form-control" name="admin_email_subject" id="admin_email_subjectID">{{ App.item.admin_email_subject|e('html') }}</textarea>
							</div>
						</div>	
						<div class="form-group row">
							<label for="user_email_contentID" class="col-md-5 control-label">
								{{ Lang['contenuto email admin'] }}
								<span>{{ Lang['contenuto email admin - titolo'] }}</span>
							</label>
							<div class="col-md-6">
								<textarea rows="8" class="form-control" name="admin_email_content" id="admin_email_contentID">{{ App.item.admin_email_content|e('html') }}</textarea>
							</div> 
						</div>							
					</fieldset>
				</div>

					
				<div class="tab-pane fade show" id="user" role="tabpanel" aria-labelledby="user">
					<fieldset>

						{% for lang in GlobalSettings['languages'] %}	
							{% set userEmailSubject = "user_email_subject_#{lang}" %}
							{% set userEmailContent = "user_email_content_#{lang}" %}
							
							<div class="form-group row">
							<label for="user_email_subject_{{ lang }}ID" class="col-md-5 control-label">
								{{ Lang['soggetto email utente'] }} {{ lang }}
								<span>{{ Lang['soggetto email utente - titolo'] }}</span>
							</label>
							<div class="col-md-6">
								<textarea  rows="2" class="form-control" name="user_email_subject_{{ lang }}" id="user_email_subject_{{ lang }}ID">{{ attribute(App.item,userEmailSubject)|e('html') }}</textarea>
							</div>
							</div>	
							<div class="form-group row">
								<label for="user_email_content_{{ lang }}ID" class="col-md-5 control-label">
									{{ Lang['contenuto email utente'] }} {{ lang }}
									<span>{{ Lang['contenuto email utente - titolo'] }}</span>
								</label>
								<div class="col-md-6">
									<textarea rows="8" class="form-control" name="user_email_content_{{ lang }}" id="user_email_content_{{ lang }}ID">{{attribute(App.item,userEmailContent)|e('html') }}</textarea>
								</div> 
							</div>
							
							{% if loop.last == false %}<hr>{% endif %}					
						{% endfor %}						
					</fieldset>
				</div>
				
						
				{% for lang in GlobalSettings['languages'] %}	
					{% set title = "title_#{lang}" %}
					{% set textIntro = "text_intro_#{lang}" %}
					{% set pageContent = "page_content_#{lang}" %}
					<div class="tab-pane fade show" id="contents{{ lang }}" role="tabpanel" aria-labelledby="contents{{ lang }}">
						<fieldset>					

							<div class="form-group row">
								<label for="title_{{ lang }}ID" class="col-md-4 control-label">
									{{ LangVars['configurazione modulo testo titolo'] }} {{ lang }}
									<br><span class="font-italic">{{ LangVars['configurazione modulo testo titolo - testo'] }}</span>
								</label>
								<div class="col-md-7">
									<input 
									class="form-control" 
									name="title_{{ lang }}" 
									id="title_{{ lang }}ID"
									value="{{attribute(App.item,title)|e('html') }}"
									>
								</div> 
							</div>

		
							<div class="form-group row">
								<label for="text_intro_{{ lang }}ID" class="col-md-4 control-label">
									{{ Lang['testo intro'] }} {{ lang }}
									<span>{{ Lang['testo intro - titolo'] }}</span>
								</label>
								<div class="col-md-7">
									<textarea rows="4" class="form-control editorHTML" name="text_intro_{{ lang }}" id="text_intro_{{ lang }}ID">{{attribute(App.item,textIntro)|e('html') }}</textarea>
								</div> 
							</div>	

							<div class="form-group row">
								<label for="text_intro_{{ lang }}ID" class="col-md-4 control-label">
									{{ Lang['contenuto pagina'] }} {{ lang }}
									<span>{{ Lang['contenuto pagina - titolo'] }}</span>
								</label>
								<div class="col-md-7">
									<textarea rows="8" class="form-control editorHTML" name="page_content_{{ lang }}" id="page_content_{{ lang }}ID">{{attribute(App.item,pageContent)|e('html') }}</textarea>
								</div> 
							</div>	
						
						
						</fieldset>
					</div>
				{% endfor %}

					<!-- sezione meta tags -->
					{% for lang in GlobalSettings['languages'] %}	
				{% set metaTitle = "meta_title_#{lang}" %}
				{% set metaDescription = "meta_description_#{lang}" %}
				{% set metaKeywords = "meta_keywords_#{lang}" %}
				<div class="tab-pane fade show" id="metatags{{ lang }}" role="tabpanel" aria-labelledby="metatags{{ lang }}">
					<fieldset>				
						
						<div class="form-group row">
							<label for="meta_title_{{ lang }}ID" class="col-md-4 control-label">
								{{ LangVars['configurazione modulo meta title'] }} {{ lang }}
								<br><span class="font-italic">{{ LangVars['configurazione modulo meta title - testo'] }}</span>
							</label>				
							<div class="col-md-6">
								<input 
									type="text" 
									class="form-control" 
									name="meta_title_{{ lang }}" 
									placeholder="" 
									id="meta_title_{{ lang }}ID" 
									value="{{ attribute(App.item,metaTitle)|e('html') }}"
								>
							</div>
						</div>

						<div class="form-group row">
							<label for="meta_description_{{ lang }}ID" class="col-md-4 control-label">
								{{ LangVars['configurazione modulo meta description'] }} {{ lang }}
								<br><span class="font-italic">{{ LangVars['configurazione modulo meta description - testo'] }}</span>
							</label>
							<div class="col-md-7">
								<textarea rows="4" 
									class="form-control" 
									name="meta_description_{{ lang }}" 
									id="meta_description_{{ lang }}ID"
								>{{attribute(App.item,metaDescription)|e('html') }}</textarea>
							</div> 
						</div>

						<div class="form-group row">
							<label for="meta_keywords_{{ lang }}ID" class="col-md-4 control-label">
								{{ LangVars['configurazione modulo meta keywords'] }} {{ lang }}
								<br><span class="font-italic">{{ LangVars['configurazione modulo meta keywords - testo'] }}</span>
							</label>
							<div class="col-md-7">
								<textarea rows="4" 
									class="form-control" 
									name="meta_keywords_{{ lang }}" 
									id="meta_keywords_{{ lang }}ID"
								>{{attribute(App.item,metaKeywords)|e('html') }}</textarea>
							</div> 
						</div>	
						
						
						
						
					</fieldset>
				</div>
				{% endfor %}	
				<!-- sezione meta tags -->

			
				
				<div class="tab-pane fade show" id="map" role="tabpanel" aria-labelledby="map">
					<fieldset>					
						<div class="form-group row">
							<label for="latitudeID" class="col-md-3 control-label">
								{{ Lang['latitudine']|capitalize }}
								<span>{{ Lang['latitudine - titolo']|capitalize }}</span>
							</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="map_latitude" placeholder="{{ Lang['inserisci una latitudine']|capitalize }}" id="latitudeID" value="{{ App.item.map_latitude|e('html') }}">
							</div>
						</div>
						<div class="form-group row">
							<label for="label_email_addressID" class="col-md-3 control-label">
								{{ Lang['longitudine']|capitalize }}
								<span>{{ Lang['longitudine - titolo']|capitalize }}</span>
							</label>				
							<div class="col-md-6">
								<input type="text" class="form-control" name="map_longitude" placeholder="{{ Lang['inserisci una longitudine']|capitalize }}" id="longitudeID" value="{{ App.item.map_longitude|e('html') }}">
							</div>
						</div>						
					</fieldset>
				</div>

			</div>
			<!--/Tab panes -->			
			<hr>

			<div class="form-group row">
				<div class="col-md-6 col-xs-12 text-center">
					<input type="hidden" name="id" id="idID" value="1">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform ml-5">{{ Lang['applica']|capitalize }}</button>
				</div>
				<div class="col-md-6 col-xs-12 text-right">				
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listItem" title="{{ Lang['torna alla %ITEM%']|replace({'%ITEM%': Lang['lista']})|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>