<!-- wscms/pages/formIimg.tpl.php v.3.5.3. 18/09/2018 -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}
	</div>
	<div class="col-md-2 help">
		{% if (App.params.help is defined) and (App.params.help != '') %}
			<button class="btn btn-warning btn-sm" type="button" data-target="#helpModal" data-toggle="modal">Come funziona?</button>
		{% endif %}
	</div>
</div>
<div class="row well well-sm mt-2">	
	<div class="col-md-2"> 
		{{ Lang['dettagli voce']|capitalize }}
	</div>
	<div class="col-md-2"> 
		{% if App.ownerData.filename != '' %}
		<a class="" href="{{ App.params.uploadDirs['item'] }}{{ App.ownerData.filename }}" data-lightbox="image-1" data-title="{{ App.ownerData.org_filename }}" title="{{ App.ownerData.org_filename }}">
			<img  class="img-thumbnail"  src="{{ App.params.uploadDirs['item'] }}{{ App.ownerData.filename }}" alt="{{ App.ownerData.org_filename }}">
		</a>
		{% else %}
		<img  class="img-thumbnail"  src="{{ App.params.uploadDirs['item'] }}default/image.png" alt="{{ Lang['immagine di default']|capitalize }}">
		{% endif %}
	</div>
	<div class="col-md-8"> 
		<big>{{ App.ownerData.title }}</big>
	</div>
</div>

<div class="card shadow mt-3 mb-4">
	<div class="card-body">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" id="formTab" role="tablist">
			{% for lang in GlobalSettings['languages'] %}
				<li class="nav-item">
					<a class="nav-link{% if lang == Lang['user'] %} active{% endif %}" href="#datibase{{ lang }}" id="datibase{{ lang }}tab" data-toggle="tab" role="tab" aria-controls="datibase{{ lang }}" aria-selected="true">{{ Lang['dati base']|capitalize }} {{ lang }}</a>
				</li>
			{% endfor %}
			<li class="nav-item">
			<a class="nav-link" href="#image" id="image-tab" data-toggle="tab" role="tab" aria-controls="image" aria-selected="true">{{ Lang['immagini']|capitalize }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#options" id="options-tab" data-toggle="tab" role="tab" aria-controls="options" aria-selected="true">{{ Lang['opzioni']|capitalize }}</a>
			</li>
		</ul>	
		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">
			<!-- Tab panes -->
			<div class="tab-content">
					
				<!-- sezione dati base dinamica lingue -->
				{% for lang in GlobalSettings['languages'] %}				
					{% set titleField = "title_#{lang}" %}
					{% set contentField = "content_#{lang}" %}
					<div class="tab-pane{% if lang == Lang['user'] %} active{% endif %}" id="datibase{{ lang }}" role="tabpanel" aria-labelledby="datibase{{ lang }}-tab">
						<fieldset>
							<div class="form-group row">
								<label for="title_{{ lang }}ID" class="col-md-2 control-label">{{ Lang['titolo']|capitalize }} {{ lang }} </label>
								<div class="col-md-7">
									<input{% if lang == Lang['user'] %} required{% endif %} type="text" class="form-control" name="title_{{ lang }}" 
									placeholder="{{ Lang['inserisci un %ITEM%']|replace({ '%ITEM%': Lang['titolo'] })|capitalize }} {{ lang }}" 
									id="title_{{ lang }}ID" value="{{ attribute(App.item, titleField)|e('html') }}">
								</div>
							</div>
							<!-- <hr>
							<div class="form-group">
								<label for="content_{{ lang }}ID" class="col-md-2 control-label">{{ Lang['contenuto']|capitalize }} {{ lang }} </label>
								<div class="col-md-8">
									<textarea name="content_{{ lang }}" class="form-control editorHTML" id="content_{{ lang }}ID" rows="5">{{ attribute(App.item, contentField) }}</textarea>
								</div>
							</div> -->
						</fieldset>				
					</div>
				{% endfor %}
				<!-- /sezione dati base dinamica lingue -->

				<!-- sezione image -->	
				<div class="tab-pane" id="image" role="tabpanel" aria-labelledby="image-tab">
					<fieldset>
						<div class="form-group row">
							<label for="filenameID" class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-form-label">{{ Lang['file']|capitalize }}</label>	
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
								<a class="" href="{{ App.params.uploadDirs['iimg'] }}{{ App.item.filename }}" data-lightbox="image-1" data-title="{{ value.org_filename }}" title="{{ App.item.org_filename }}">
									<img  class="img-miniature"  src="{{ App.params.uploadDirs['iimg'] }}{{ App.item.filename }}" alt="{{ App.item.org_filename }}">
								</a>							
								{% else %}
									<img class="img-miniature"  src="{{ UPLOADDIR }}default/image.png" alt="{{ LocalStrings['immagine di default']|capitalize }}">	
								{% endif %}
							</div>			
						</div>
					</fieldset>
				</div>
				<!-- /sezione image --> 

				<!-- sezione opzioni --> 
				<div class="tab-pane" id="options" role="tabpanel" aria-labelledby="options-tab">	
					<fieldset>						
						<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
						{% if App.userLoggedData.is_root == 1 %}						
						<div class="form-group row">
							<label for="orderingID" class="col-md-2 control-label">{{ Lang['ordine']|capitalize }}</label>
							<div class="col-md-1">
								<input type="text" name="ordering" placeholder="{{ Lang['inserisci un ordine']| capitalize }}" class="form-control" id="orderingID" value="{{ App.item.ordering }}">
							</div>
						</div>
						<hr>
						{% else %}
						<input type="hidden" name="ordering" value="{{ App.item.ordering }}">		
						{% endif %}
						<div class="form-group">
							<label for="activeID" class="col-md-2 control-label">{{ Lang['attiva']|capitalize }}</label>
							<div class="col-md-7">
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" name="active" id="activeID" {% if App.item.active == 1 %} checked="checked"{% endif %} value="1">
									</label>
								</div>
							</div>
						</div>
					</fieldset>		
				</div>		
				<!-- sezione opzioni --> 

			</div>
			<!--/Tab panes -->	
			<hr>		
			<div class="form-group row">
				<div class="col-md-6 col-xs-12 text-center">
					<input type="hidden" name="created" id="createdID" value="{{ App.item.created }}">
					<input type="hidden" name="id" value="{{ App.id }}">
					<input type="hidden" name="id_owner" value="{{ App.id_owner }}">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary submittheform">{{ Lang['invia']|capitalize }}</button>
					{% if App.id > 0 %}
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform">{{ Lang['applica']|capitalize }}</button>
					{% endif %}
				</div>	
				<div class="col-md-6 col-xs-12 text-right">			
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listIimg" title="{{ Lang['torna alla lista']|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>