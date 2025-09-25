<!-- wscms/products/formSeoItem.tpl.php v.3.5.3. 29/05/2018 -->
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

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" id="formTab" role="tablist">
			{% for lang in GlobalSettings['languages'] %}		
			<li class="nav-item">
				<a class="nav-link{% if lang == Lang['user'] %} active{% endif %}" href="#seo{{ lang }}-tab" id="seo{{ lang }}" aria-controls="seo{{ lang }}" aria-selected="true" data-toggle="tab">{{ Lang['Tag SEO'] }} {{ lang }}</a>
			</li>
			{% endfor %}
		</ul>
		<!-- Nav tabs -->

		<form id="applicationForm" method="post" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">

			<div class="tab-content">
				<!-- sezione dati base dinamica lingue -->
				{% for lang in GlobalSettings['languages'] %} 
					{% set metaTitleField = "meta_title_#{lang}" %}
					{% set metaDescriptionField = "meta_description_#{lang}" %}
					{% set metaKeywordField = "meta_keyword_#{lang}" %}
					{% set titleSeoField = "title_seo_#{lang}" %}
					<div class="tab-pane fade show {% if lang == Lang['user'] %} active{% endif %}" id="seo{{ lang }}-tab" role="tabpanel" aria-labelledby="seo{{ lang }}">	
						<fieldset>
							<div class="form-group row">
								<label for="meta_title_{{ lang }}ID" class="col-md-3 control-label">{{ Lang['titolo']|capitalize }} META {{ lang }} </label>
								<div class="col-md-7">
									<input type="text" class="form-control" name="meta_title_{{ lang }}" placeholder="{{ Lang['inserisci un titolo']|capitalize }} {{ Lang['META'] }} {{ lang }}" id="meta_title_{{ lang }}ID" value="{{ attribute(App.item, metaTitleField)|e('html') }}">
								</div>
							</div>

							<div class="form-group row">
								<label for="description_{{ lang }}ID" class="col-md-3 control-label">{{ Lang['descrizione']|capitalize }} META {{ lang }}
									<br>
									<small>
										Disponibili: <span id="metaDescriptionLength{{ lang|upper }}ID">300</span>
									</small>															
								</label>
								<div class="col-md-7">
									<textarea data-bv-stringlength="true" data-bv-stringlength-min="0" data-bv-stringlength-max="300" data-bv-stringlength-message="Massimo 300 caratteri" data-messagecontainer="metaDescriptionLength{{ lang|upper }}ID" name="meta_description_{{ lang }}" class="form-control checknumchars" id="meta_description_{{ lang }}ID" rows="4">{{ attribute(App.item, metaDescriptionField)|e('html') }}</textarea>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="meta_keyword_{{ lang }}ID" class="col-md-3 control-label">Keyword  META {{ lang }}</label>
								<div class="col-md-7">
									<textarea name="meta_keyword_{{ lang }}" class="form-control" id="meta_keyword_{{ lang }}ID" rows="2">{{ attribute(App.item, metaKeywordField)|e('html') }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label for="title_seo_{{ lang }}ID" class="col-md-3 control-label">{{ Lang['titolo']|capitalize }} {{ Lang['SEO'] }} {{ lang }} </label>
								<div class="col-md-7">
									<input type="text" class="form-control" name="title_seo_{{ lang }}" placeholder="{{ Lang['inserisci un titolo']|capitalize }} {{ Lang['SEO'] }} {{ lang }}" id="title_seo_{{ lang }}ID" value="{{ attribute(App.item, titleSeoField)|e('html') }}">
								</div>
							</div>					
						</fieldset>				
					</div>
				{% endfor %}
				<!-- /sezione dati base dinamica lingue -->
	
			</div>
			<!--/Tab panes -->			
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
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listItem" title="{{ Lang['torna alla lista']|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>