<!-- newsletter/formConf.tpl.php v.2.6.1. 21/12/2015 -->
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
		<form class="form-horizontal" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/updateConfig"  enctype="multipart/form-data" method="post">

			{% if App.items is iterable and App.items|length > 0 %}
				{% for key,value in App.items %}

			

					{% if value.type == 'label' %}
						<h3>{{ value.value_it }}</h3>
						<hr>		
					{% elseif value.type == 'input' %}

						{% if value.multilanguage == 0 %}
							<div class="form-group row">
								<div class="col-md-5">
									{% if App.userLoggedData.is_root is defined and App.userLoggedData.is_root == 1 %}
										<em>{{ value.name }}</em><br>	
									{% endif %}
									{{ value.content_it }}
								</div>

								<div class="col-md-5">		
									{% if value.length == 'text' %}						
										<textarea  
										name="value_it[{{ value.name }}]" 
										class="form-control" 
										id="{{ value.value_name }}ID" 
										rows="3">{{ value.value_it }}

									</textarea>
									{% endif %}

									{% if value.length == 'varchar' %}	
										<input 
										type="text" 
										name="value_it[{{ value.name }}]" 
										class="form-control" 
										id="{{ value.name }}ID" 
										rows="3" 
										value="{{ value.value_it }}"
										>   		
									{% endif %}

									{% if value.length == 'flag' %}	

										<input 
										type="text" 
										name="value_it[{{ value.name }}]" 
										class="form-control" 
										id="{{ value.name }}ID" 
										rows="3" 
										value="{{ value.value_it }}"
										>  

									{% endif %}


								</div>


							</div>
							
						{% endif %}	

						{% if value.multilanguage == 1 %}

							


							{% for keyl,lang in GlobalSettings['languages'] %}
								{% set name = "name_#{lang}" %}
								{% set field = "value_#{lang}" %}

						

								<div class="form-group row">

									<div class="col-md-5">
										{% if keyl == 0 %}
											<em>{{ value.name }}</em><br>
											{{ value.content_it }}
										{% endif %}								
									</div>
									
									<div class="col-md-5">
										<span style="">{{ lang }}</span>&nbsp;	

										{% if value.length == 'text' %}						
											<textarea  
											name="value_{{ lang }}[{{ value.name }}]" 
											class="form-control" 
											id="{{ value.name }}_{{ lang }}ID" 
											rows="3">{{ attribute(value, field) }}</textarea>
										{% endif %}

										{% if value.length == 'varchar' %}	
											<input 
											type="text" 
											name="value_{{ lang }}[{{ value.name }}]" 
											class="form-control" 
											id="{{ value.name }}_{{ lang }}ID" 
											rows="3" 
											value="{{ attribute(value, field) }}"
											>    					
											<textarea  
											name="value_{{ lang }}[{{ value.name }}]" 
											class="form-control" 
											id="{{ value.name }}_{{ lang }}ID" 
											rows="3">{{ attribute(value, field) }}</textarea>
										{% endif %}

										{% if value.length == 'flag' %}	
											<input 
											type="text" 
											name="value_{{ lang }}[{{ value.name }}]" 
											class="form-control" 
											id="{{ value.name }}_{{ lang }}ID" 
											rows="3" 
											value="{{ attribute(value, field) }}"
											>    					
											<textarea  
											name="value_{{ lang }}[{{ value.name }}]" 
											class="form-control" 
											id="{{ value.name }}_{{ lang }}ID" 
											rows="3">{{ attribute(value, field) }}</textarea>
										{% endif %}


									</div>
									
									



								</div>

							{% endfor %}

						{% endif %}	





					{% endif %}	
					
					

				{% endfor %}
			{% endif %}
			
			<div class="form-group">
				<div class="col-md-offset-2 col-md-7">
					<button type="submit" name="applyForm" value="apply" class="btn btn-primary">Applica</button>
				</div>
			</div>
			
		</form>
		
	</div>
</div>
