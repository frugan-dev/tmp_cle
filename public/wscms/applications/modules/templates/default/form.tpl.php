<!-- wscms/modules/form.tpl.php v.3.5.4. 28/03/2019 -->
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
<div class="row">
	<div class="col-md-12">		
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#datibase-tab" data-toggle="tab">{{ Lang['dati base']|capitalize }} <i class="fa"></i></a></li>
			<li><a href="#smallhelp-tab" data-toggle="tab">{{ Lang['aiuto breve']|capitalize }} <i class="fa"></i></a></li>
			<li><a href="#help-tab" data-toggle="tab">{{ Lang['aiuto modulo']|capitalize }} <i class="fa"></i></a></li>
		</ul>		
		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">
			<!-- Tab panes -->
			<div class="tab-content">			
				<div class="tab-pane active" id="datibase-tab">
			
					<fieldset>
						<div class="form-group">
							<label for="nameID" class="col-md-2 control-label">{{ Lang['nome']|capitalize }}</label>
							<div class="col-md-3">
								<input required type="text" name="name" class="form-control" id="nameID" placeholder="{{ Lang['nome']|capitalize }} {{ Lang['voce'] }}" value="{{ App.item.name|e('html') }}" oninvalid="this.setCustomValidity('{{ Lang['Devi inserire un %ITEM%!']|replace({'%ITEM%': Lang['nome']}) }}')" oninput="setCustomValidity('')">
					    	</div>
						</div>
						
						<div class="form-group">
							<label for="nameID" class="col-md-2 control-label">{{ Lang['etichetta']|capitalize }}</label>
							<div class="col-md-3">
								<input required type="text" name="label" class="form-control" id="labelID" placeholder="{{ Lang['etichetta']|capitalize }} {{ Lang['voce'] }}" value="{{ App.item.label|e('html') }}" oninvalid="this.setCustomValidity('{{ Lang['Devi inserire una %ITEM%!']|replace({'%ITEM%': Lang['etichetta']}) }}')" oninput="setCustomValidity('')">
					    	</div>
						</div>
						
						<div class="form-group">
							<label for="aliasID" class="col-md-2 control-label">{{ Lang['alias sito'] }}</label>
							<div class="col-md-3">
								<input type="text" name="alias" class="form-control" id="aliasID" placeholder="{{ Lang['alias sito voce 1'] }}" value="{{ App.item.alias|e('html') }}">
					    	</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">{{ Lang['sezione']|capitalize }}</label>	
							<div class="col-md-7">	
								<select class="form-control input-md" name="section">					
								{% if App.sections is iterable and App.sections|length > 0 %}
									{% for key,value in App.sections %}					
										<option value="{{ key }}"{% if App.item.section is defined and App.item.section == key %} selected="selected"{% endif %}>{{ value|e('html') }}</option>														
									{% endfor %}
								{% endif %}											
								</select>	
							</div>												
						</div>									
						<hr>
						<div class="form-group">
							<label for="commentID" class="col-md-2 control-label">{{ Lang['contenuto']|capitalize }}</label>
							<div class="col-md-7">
								<textarea name="content" class="form-control" id="content" rows="4">{{ App.item.content }}</textarea>
							</div>
						</div>
						<hr>
						<div class="form-group">
							<label for="code_menuID" class="col-md-2 control-label">{{ Lang['codice menu']|capitalize }}</label>
							<div class="col-md-6">
								<textarea name="code_menu" class="form-control" id="code_menuID" rows="4">{{ App.item.code_menu }}</textarea>
							</div>
							<div class="col-md-4">{{ Lang['label url admin dinamico'] }}</div>
						</div>
						<hr>						
	
						<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
						{% if App.userLoggedData.is_root == 1 %}		
							<div class="form-group">
								<label for="orderingID" class="col-md-2 control-label">{{ Lang['ordine']|capitalize }}</label>
								<div class="col-md-3">
									<input type="text" name="ordering" placeholder="" class="form-control" id="orderingID" value="{{ App.item.ordering }}">
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
	<!-- sezione datibase -->	  
				<div class="tab-pane" id="smallhelp-tab">
					<fieldset>
						<div class="form-group">
							<p>{{ Lang['Questo è il contenuto BREVE aiuto del modulo'] }}</p>
							<div class="col-md-12">
								<textarea name="help_small" class="form-control" id="help_smallID" rows="4">{{ App.item.help_small }}</textarea>
							</div>
						</div>
					</fieldset>
				</div>				
				<div class="tab-pane" id="help-tab">
					<fieldset>
						<p>{{ Lang['Questo è il contenuto COMPLETO aiuto del modulo'] }}</p>
							<div class="form-group">							
								<div class="col-md-12">
									<textarea name="help" class="form-control editorHTML" id="helpID" rows="4">{{ App.item.help }}</textarea>
								</div>
							</div>
					</fieldset>
				</div>	 
			</div>
			<!--/Tab panes -->	
			<hr>			
			<div class="form-group">
				<div class="col-md-offset-2 col-md-7 col-xs-offset-0 col-xs-6">
					<input type="hidden" name="id" id="idID" value="{{ App.id }}">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary submittheform">{{ Lang['invia']|capitalize }}</button>
					{% if App.id > 0 %}
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform">{{ Lang['applica']|capitalize }}</button>
					{% endif %}
				</div>
				<div class="col-md-2 col-xs-6">				
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/list" title="{{ Lang['torna alla lista']|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>
			</div>	
		</form>
	</div>
</div>