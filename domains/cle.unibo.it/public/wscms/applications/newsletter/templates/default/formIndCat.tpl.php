<!-- newsletter/formIndcAT.tpl.php v.2.6.2.1 02/03/2016 -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}
	</div>
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
			<li class="nav-item">	
				<a class="nav-link active" href="#datibase" id="datibase-tab" data-toggle="tab" role="tab" aria-controls="datibase" aria-selected="true">{{ Lang['dati base']|capitalize }}</a>
			</li>
		</ul>
		<!-- Nav tabs -->
		
		<form id="applicationForm" method="post" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">

			<div class="tab-content" id="formTabContent">			
				<div class="tab-pane fade show active" id="datibase" role="tabpanel" aria-labelledby="datibase-tab">		
					<fieldset>

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
						<!-- sezione dati base dinamica lingue -->
					
						<hr>
						
						  
						<div class="form-group row">
							<label for="publicID" class="col-md-2 control-label">Pubblica</label>
							<div class="col-md-7">
								<input type="checkbox" name="public" id="publicID" class="form-check-input"{% if App.item.public == 1 %} checked="checked"{% endif %} value="1">     					
							</div>
						</div>
						
						<div class="form-group row">
							<label for="activeID" class="col-md-2 control-label">{{ LangVars['attiva']|capitalize }}</label>
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
					<input type="hidden" name="hash" id="hashID" value="{{ App.item.hash }}">
					<input type="hidden" name="id" id="idID" value="{{ App.id }}">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary submittheform">{{ Lang['invia']|capitalize }}</button>
					{% if App.id > 0 %}
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform ml-5">{{ Lang['applica']|capitalize }}</button>
					{% endif %}
				</div>
				<div class="col-md-6 col-xs-12 text-right">				
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listIndCat" title="{{ Lang['torna alla %ITEM%']|replace({'%ITEM%': Lang['lista']})|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>	
		</form>
	</div>
</div>