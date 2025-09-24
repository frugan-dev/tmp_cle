<!-- wscms/pages/formBvid.tpl.php v.3.5.2. 14/02/2018 -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}
	</div>
	<div class="col-md-2 help">
	</div>
</div>
<div class="row well well-sm">	
	<div class="col-md-2"> 
		{{ Lang['dettagli voce']|capitalize }}
	</div>
	<div class="col-md-2"> 
		{% if App.ownerData.filename != '' %}
		<a class="" href="{{ App.params.uploadDirs['iblo'] }}{{ App.ownerData.filename }}" data-lightbox="image-1" data-title="{{ App.ownerData.org_filename }}" title="{{ App.ownerData.org_filename }}">
			<img  class="img-thumbnail"  src="{{ App.params.uploadDirs['iblo'] }}{{ App.ownerData.filename }}" alt="{{ App.ownerData.org_filename }}">
		</a>
		{% else %}
		<img  class="img-thumbnail"  src="{{ App.params.uploadDirs['item'] }}default/image.png" alt="{{ Lang['immagine di default']|capitalize }}">
		{% endif %}
	</div>
	<div class="col-md-8"> 
		<big>{{ App.ownerData.title }}</big>
	</div>
</div>

<div class="row">
	<div class="col-md-12">	
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			{% for lang in GlobalSettings['languages'] %}		
				<li class="{% if lang == Lang['user'] %}active{% endif %}"><a href="#datibase{{ lang }}-tab" data-toggle="tab">{{ Lang['dati base']|capitalize }} {{ lang }} <i class="fa"></i></a></li>
			{% endfor %}
			<li><a href="#code-tab" data-toggle="tab">{{ Lang['codice']|capitalize }} <i class="fa"></i></a></li>
			<li><a href="#options-tab" data-toggle="tab">{{ Lang['opzioni']|capitalize }} <i class="fa"></i></a></li>
		</ul>	
		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">
			<!-- Tab panes -->
			<div class="tab-content">
					
<!-- sezione dati base dinamica lingue -->
			{% for lang in GlobalSettings['languages'] %} 
				{% set titleField = "title_#{lang}" %}
				{% set contentField = "content_#{lang}" %}	
				<div class="tab-pane{% if lang == Lang['user'] %} active{% endif %}" id="datibase{{ lang }}-tab">
					<fieldset class="form-group">
						<div class="form-group">
							<label for="title_{{ lang }}ID" class="col-md-2 control-label">{{ Lang['titolo']|capitalize }} {{ lang }} </label>
							<div class="col-md-7">
								<input{% if lang == Lang['user'] %} required{% endif %} type="text" class="form-control" name="title_{{ lang }}" placeholder="{{ Lang['inserisci un titolo']|capitalize }} {{ lang }}" id="title_{{ lang }}ID" rows="3" value="{{ attribute(App.item, titleField)|e('html') }}">
							</div>
						</div>
						<hr>
						<div class="form-group">
							<label for="content_{{ lang }}ID" class="col-md-2 control-label">{{ Lang['contenuto']|capitalize }} {{ lang }} </label>
							<div class="col-md-8">
								<textarea name="content_{{ lang }}" class="form-control editorHTML" id="content_{{ lang }}ID" rows="5">{{ attribute(App.item, contentField) }}</textarea>
							</div>
						</div>
					</fieldset>			
				
				</div>
			{% endfor %}
<!-- /sezione dati base dinamica lingue -->
<!-- sezione file -->	
				<div class="tab-pane" id="code-tab">		
					<fieldset class="form-group">
						<div class="form-group">
							<label for="codeID" class="col-md-2 control-label">{{ Lang['codice video embedded']|capitalize }}</label>
							<div class="col-md-8">
								<textarea required name="code" class="form-control" id="codeID" rows="5">{{ App.item.code }}</textarea>
							</div>
						</div>
					</fieldset>
				</div>
<!-- /sezione image --> 
<!-- sezione opzioni --> 
				<div class="tab-pane" id="options-tab">
					<fieldset class="form-group">						
						<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
						{% if App.userLoggedData.is_root == 1 %}		
							<div class="form-group">
								<label for="orderingID" class="col-md-2 control-label">{{ Lang['ordine']|capitalize }}</label>
								<div class="col-md-1">
									<input type="text" name="ordering" placeholder="{{ Lang['inserisci un ordine']|capitalize }}" class="form-control" id="orderingID" value="{{ App.item.ordering }}">
						    	</div>
							</div>	
						<hr>
						{% else %}
							<input type="hidden" name="ordering" value="{{ App.item.ordering }}">		
						{% endif %}
						<!-- fine se root -->	
						<div class="form-group">
							<label for="activeID" class="col-md-2 control-label">{{ Lang['attiva']|capitalize }}</label>
							<div class="col-md-7">
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" name="active" id="activeID"{% if App.item.active == 1 %} checked="checked"{% endif %} value="1">
									</label>
	     						</div>
	   					</div>
	   				</div>
					</fieldset>		
				</div>		
			</div>
			<!--/Tab panes -->	
			<hr>		
			<div class="form-group">
				<div class="col-md-offset-2 col-md-7">
					<input type="hidden" name="created" id="createdID" value="{{ App.item.created }}">
					<input type="hidden" name="id" value="{{ App.id }}">
					<input type="hidden" name="id_owner" value="{{ App.id_owner }}">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary">{{ Lang['invia']|capitalize }}</button>
					{% if App.id > 0 %}
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary">{{ Lang['applica']|capitalize }}</button>
					{% endif %}
				</div>	
				<div class="col-md-2">				
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listBvid" title="{{ Lang['torna alla lista']|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>