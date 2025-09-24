<!-- wscms/sponsor/formItem.tpl.php v.1.0.0. 28/06/2016 -->
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
			{% for lang in GlobalSettings['languages'] %}			
				<li class="nav-item">
					<a class="nav-link{% if lang == Lang['user'] %} active{% endif %}" href="#datibase{{ lang }}" data-toggle="tab" role="tab" aria-controls="help" aria-selected="false">Dati Base {{ lang }}</a>
				</li>
			{% endfor %}
			<li class="nav-item">
				<a class="nav-link" href="#image" data-toggle="tab" role="tab" aria-controls="image" aria-selected="false">Immagine</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#options" data-toggle="tab" role="tab" aria-controls="options" aria-selected="false">Opzioni</a>
			</li>
  		</ul>

		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">
			<!-- Tab panes -->
			<div class="tab-content" id="formTabContent">

				<!-- sezione dati base dinamica lingue -->
				{% for lang in GlobalSettings['languages'] %}

					{% set titleField = "title_#{lang}" %}
					{% set contentField = "content_#{lang}" %}

					<div class="tab-pane fade show {% if lang == Lang['user'] %} active{% endif %}" id="datibase{{ lang }}" role="tabpanel" aria-labelledby="datibase{{ lang }}">
						<fieldset>
							<div class="form-group row">
								<label for="title_{{ lang }}ID" class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">
									Titolo {{ lang }}
									{% if App.params.fieldsItem[titleField]['required'] == true %}
										<span class="required-sign">*</span>
									{% endif %}	
								</label>
								<div class="col-sm-12 col-md-6 col-lg-8 col-xl-8">
									<input 
									type="text" 
									name="title_{{ lang }}" 
									id="title_{{ lang }}ID" 
									value="{{ attribute(App.item, titleField) }}" 
									class="form-control"  
									placeholder="{{ LocalStrings['inserisci un %ITEM%']|replace({ '%ITEM%': App.params.fields['item'][titleField]['label'] })|capitalize }}" 
									{% if App.params.fieldsItem[titleField]['required'] == true %} 
									{% set label = LocalStrings['Devi inserire un %ITEM%!']|replace({'%ITEM%': App.params.fields['item'][titleField]['label'] })  %}
										required="required"
									{% endif %} 
									oninvalid="this.setCustomValidity('{{ label }}')" 
									oninput="setCustomValidity('')"
									data-errormessage = "{{ App.params.fields['item'][titleField]['label']|capitalize }}: {{ App.params.fields['item'][titleField]['error message'] }}"
									>								
								</div>
							</div>			
							
							<!-- <div class="form-group row">
								<label for="content_{{ lang }}ID" class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">Contenuto {{ lang }} </label>
								<div class="col-sm-12 col-md-6 col-lg-8 col-xl-8">
									<textarea 
									name="content_{{ lang }}" 
									class="form-control editorHTML" 
									id="content_{{ lang }}ID" 
									rows="5"
									>{{ attribute(App.item, contentField) }}</textarea>
								</div>
							</div> -->
							<input 
								type="hidden" 
								name="content_{{ lang }}" 
								id="content_{{ lang }}ID" 
								value="{{ attribute(App.item, contentField) }}" 
								class="form-control"  
							>			

						</fieldset>				
					</div>
				{% endfor %}
				<!-- sezione dati base dinamica lingue -->

				<!-- sezione image -->	
				<div class="tab-pane fade" id="image" role="tabpanel" aria-labelledby="image">		
					<fieldset>
						<div class="form-group row">
							<label for="filenameID" class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">{{ Lang['immagine']|capitalize }}</label>	
							<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">	
								<div class="custom-file">
									<input{% if App.item.filenameRequired == true %} required{% endif %} 
									type="file" 
									name="filename" 
									id="filenameID" 
									class="custom-file-input"{% if App.item.filenameRequired == true %} required{% endif %}
									>
									<label class="custom-file-label" for="filenameID">{{ Lang['indica un file da caricare']|capitalize }}</label>    							
								</div>
							</div>
  						</div>
  						
						<div class="form-group row">
							<label class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">{{ Lang['anteprima']|capitalize }}</label>
							<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
								{% if App.item.filename is defined and App.item.filename != '' %}
								<a class="" href="{{ App.params.itemUploadDir }}{{ App.item.filename }}" data-lightbox="image-1" data-title="{{ value.org_filename }}" title="{{ App.item.org_filename }}">
									<img  class="img-miniature"  src="{{ App.params.itemUploadDir }}{{ App.item.filename }}" alt="{{ App.item.org_filename }}">
								</a>							
								{% else %}
									<img class="img-miniature"  src="{{ UPLOADDIR }}default/image.png" alt="{{ LocalStrings['immagine di default']|capitalize }}">	
								{% endif %}
							</div>			
						</div>
						{% if App.item.filename is defined and App.item.filename != '' %}
						<div class="form-group row">
							<label for="deleteFilenameID" class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label col-form-label-custom-checkbox">{{ Lang['cancella file']|capitalize }}</label>						
							<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">													
								<div class="custom-control custom-checkbox">
									<input 
									name="deleteFilename" 
									id="deleteFilenameID"
									value="1" 
									type="checkbox" 
									class="custom-control-input"
									>
									<label class="custom-control-label" for="deleteFilenameID"></label>
								</div>
											
							</div>					
						</div>
						{% endif %}
					</fieldset>
				</div>
				<!-- /sezione image -->		

				<!-- sezione opzioni --> 
				<div class="tab-pane fade" id="options" role="tabpanel" aria-labelledby="image">	
					<fieldset>
						<div class="form-group row">
								<label for="urlID" class="col-md-2 control-label">URL<br>
								<small>%URLSITE% per url dinamico</small></label>
								<div class="col-md-6">
									<input 
									type="text" 
									class="form-control" 
									name="url" 
									placeholder="Inserisci un URL" 
									id="urlID" 
									rows="3" 
 									value="{{ App.item.url }}"
									>
								</div>								
						
								<label for="targetID" class="col-md-1 control-label">Target</label>
								<div class="col-md-2">							
									<select class="form-control input-sm" name="target">	
									<option></option>			
										{% if App.targets is iterable and App.targets|length > 0 %}			
											{% for value in App.targets %}												
												<option 
 												value="{{ value }}"
												 {% if App.item.target is defined and App.item.target == value %} selected="selected"{% endif %}
												>{{ value }}</option>														
											{% endfor %}
										{% endif %}
									</select>							
								</div>
						</div>
						<hr>
							
						<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
						{% if App.userLoggedData.is_root == 1 %}		
							<div class="form-group row">
								<label for="orderingID" class="col-md-2 control-label">{{ Lang['ordinamento']|capitalize }}</label>
								<div class="col-md-3">
									<input type="text" name="ordering" placeholder="" class="form-control" id="orderingID" value="{{ App.item.ordering }}">
								</div>
							</div>
							<hr>
						{% else %}
							<input type="hidden" name="ordering" value="{{ App.item.ordering }}">		
						{% endif %}
						<!-- fine se root -->

						<div class="form-group row">
							<label for="activeID" class="col-md-2 control-label">{{ Lang['attiva']|capitalize }}</label>
							<div class="col-md-7">
								<input type="checkbox" name="active" id="activeID" class="form-check-input"{% if App.item.active == 1 %} checked="checked"{% endif %} value="1">     					
							</div>
						</div>

					</fieldset>				
				</div>
				<!-- sezione opzioni -->

			</div>
			<!--Tab panes -->	

			<hr>
						
			<div class="form-group row">
				<div class="col-md-6 col-xs-12 text-center">
					<input type="hidden" name="id" id="idID" value="{{ App.id }}">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary submittheform">{{ Lang['invia']|capitalize }}</button>
					{% if App.id > 0 %}
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform ml-5">{{ Lang['applica']|capitalize }}</button>
					{% endif %}
				</div>
				<div class="col-md-6 col-xs-12 text-right">				
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listItem" title="{{ Lang['torna alla %ITEM%']|replace({'%ITEM%': Lang['lista']})|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>