<!-- site-galleries/formCate.tpl.php v.2.6.3. 12/04/2016 -->
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
			<li class="nav-item">
				<a class="nav-link active" href="#datibase" data-toggle="tab" role="tab" aria-controls="help" aria-selected="false">Dati Base</a>
			</li>
		</ul>

		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}" enctype="multipart/form-data" method="post">

			<!-- Tab panes -->
			<div class="tab-content">

				<div class="tab-pane active" id="datibase-tab">
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
								<input type="text" name="title_{{ lang }}" id="title_{{ lang }}ID" value="{{ attribute(App.item, titleField) }}" class="form-control" placeholder="{{ LocalStrings['inserisci un %ITEM%']|replace({ '%ITEM%': App.params.fields['item'][titleField]['label'] })|capitalize }}" {% if App.params.fieldsItem[titleField]['required'] == true %} {% set label = LocalStrings['Devi inserire un %ITEM%!']|replace({'%ITEM%': App.params.fields['item'][titleField]['label'] })  %} required="required" {% endif %} oninvalid="this.setCustomValidity('{{ label }}')" oninput="setCustomValidity('')" data-errormessage="{{ App.params.fields['item'][titleField]['label']|capitalize }}: {{ App.params.fields['item'][titleField]['error message'] }}">
							</div>
						</div>
						{% endfor %}
						<!-- /sezione dati base dinamica lingue -->
						<hr>
						<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
						{% if App.userLoggedData.is_root == 1 %}		
							<div class="form-group row">
								<label for="orderingID" class="col-md-2 control-label">{{ Lang['ordinamento']|capitalize }}</label>
								<div class="col-md-1">
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
								<input type="checkbox" name="active" id="activeID" class="form-check-input" {% if App.item.active == 1 %} checked="checked" {% endif %} value="1">
							</div>
						</div>
					</fieldset>

				</div>

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
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listCate" title="{{ Lang['torna alla %ITEM%']|replace({'%ITEM%': Lang['lista']})|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>