<!-- wscms/site-galleries/formItem.tpl.php v.1.0.0. 26/06/2016 -->
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
				<a class="nav-link active" href="#datibase" data-toggle="tab" role="tab" aria-controls="help" aria-selected="false">Dati Base</a>
			</li>
		</ul>
		
		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">

			<!-- Tab panes -->
			<div class="tab-content" id="formTabContent">
			
				<div class="tab-pane active" id="datibase-tab">
				
					<fieldset>

						<div class="form-group row">
							<label for="id_catID" class="col-md-2 control-label">Galleria</label>
							<div class="col-md-7">								
								<div class="col-md-7">								
									<select class="form-control input-md" name="id_cat">
										{% if App.categoriesData is iterable and App.categoriesData|length > 0 %}	
											{% for key,value in App.categoriesData %}											
												<option value="{{ value.id }}" 
												{% if App.id_cat is defined and App.id_cat == value.id  %}selected="selected" {% endif %}
												>{{ value.title_it|e('html') }}
												</option>
											{% endfor %}
										{% endif %}
									
									</select>							
								</div>
							</div>
						</div>
						
						<!-- sezione dati base dinamica lingue -->
						{% for lang in GlobalSettings['languages'] %}
							{% set titleField = "title_#{lang}" %}		
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
						{% endfor %}
						<!-- /sezione dati base dinamica lingue -->
						<hr>
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
								<a class="" href="{{ App.params.itemUploadDir }}{{ App.item.folder_name }}{{ App.item.filename }}" data-lightbox="image-1" data-title="{{ value.org_filename }}" title="{{ App.item.org_filename }}">
									<img  class="img-miniature"  src="{{ App.params.itemUploadDir }}{{ App.item.folder_name }}{{ App.item.filename }}" alt="{{ App.item.org_filename }}">
								</a>							
								{% else %}
									<img class="img-miniature"  src="{{ UPLOADDIR }}default/image.png" alt="{{ LocalStrings['immagine di default']|capitalize }}">	
								{% endif %}
							</div>			
						</div>
  						
						<hr>
								
						<div class="form-group row">
							<label for="activeID" class="col-md-2 control-label">{{ Lang['attiva']|capitalize }}</label>
							<div class="col-md-7">
								<input type="checkbox" name="active" id="activeID" class="form-check-input"{% if App.item.active == 1 %} checked="checked"{% endif %} value="1">     					
							</div>
						</div>

					</fieldset>
		
				</div>
		
			</div>
			<!--/Tab panes -->	
			<hr>
			<div class="form-group row">
				<div class="col-md-6 col-xs-12 text-center">
 					<input type="hidden" name="folder_name" value="{{ App.item.folder_name }}">
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