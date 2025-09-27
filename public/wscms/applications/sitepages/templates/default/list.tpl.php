<!-- wscms/site-pages/list.tpl.php v.1.0.1. 07/09/2016 -->
<div class="row">
	<div class="col-md-3 new">
		<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/new" title="{{ Lang['inserisci una nuova %ITEM%']|replace({'%ITEM%':Lang['voce'] })|capitalize }}" class="btn btn-sm btn-primary">{{ Lang['nuova %ITEM%']|replace({'%ITEM%':Lang['voce'] })|capitalize }}</a>
	</div>
	<div class="col-md-7 help-small-list">{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}</div>
	<div class="col-md-2 help text-right">
		{% if (App.params.help is defined) and (App.params.help != '') %}
			<button class="btn btn-warning btn-sm" type="button" data-target="#helpModal" data-toggle="modal">Come funziona?</button>
		{% endif %}
	</div>
</div>

<div class="card shadow mt-3 mb-4">
	<div class="card-body">
		
		<!-- table-responsive -->	
		<div class="table-responsive">									
			<table class="table table-striped table-bordered table-hover listData tree">
				<thead>
					<tr>		
						<th>Titolo</th>				
						{% if App.userLoggedData.is_root is defined and App.userLoggedData.is_root == 1 %}
							<th class="id">ID</th>								
						{% endif %}						
						
						<th>Ord</th>									
						<th>Template</th>
						<th>Tipo</th>
						<th>Alias</th>
						<th>Menu</th>
						<th>Anteprima</th>
						{% if App.params.item_images == 1 %}
							<th>Immagini</th>
						{% endif %}	

						{% if App.params.item_files == 1 %}
							<th>Files</th>
						{% endif %}																		
						<th></th>						
					</tr>
				</thead>
				<tbody>
					{% set colspan = 7 %}
					{% if (App.userLoggedData.is_root is defined) and (App.userLoggedData.is_root is same as(1)) %}
						{% set colspan = colspan + 1 %}
					{% endif %}	
					{% if App.params.moduleAccessWrite is defined and App.params.moduleAccessWrite == 1 %}
						{% set colspan = colspan + 1 %}
					{% endif %}				
					{% if App.items is iterable and App.items|length > 0 %}
						{% for key,value in App.items %}
			
							<tr class="treegrid-{{ value.id }}{% if value.parent > 0 %} treegrid-parent-{{ value.parent }}{% endif %}" valign="top">
								<td>{{ value.title_it }}</td>
							
								{% if App.userLoggedData.is_root is defined and App.userLoggedData.is_root == 1 %}
									<td>
									{{ value.id }}-{{ value.parent }}
									</td>
								{% endif %}		
								
								<td class="ordering">
									{% if App.userLoggedData.is_root is defined and App.userLoggedData.is_root == 1 %}
										<small>{{ value.ordering }}&nbsp;</small>
									{% endif %}							
									<a class="" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.params.orderingType == 'DESC' ? 'less' : 'more' }}Ordering/{{ value.id }}" title="{{ Lang['sposta']|capitalize }} {{ App.params.orderingType == 'DESC' ? Lang['su'] : Lang['giu'] }}"><i class="fas fa-long-arrow-alt-down"></i></a>
									<a class="" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.params.orderingType == 'DESC' ? 'more' : 'less' }}Ordering/{{ value.id }}" title="{{ Lang['sposta']|capitalize }} {{ App.params.orderingType == 'DESC' ? Lang['giu'] : Lang['su'] }}"><i class="fas fa-long-arrow-alt-up"></i></a>								
								</td>		

							
								<td>{{ value.template_name }}</td>	
								<td>{{ value.type }}</td>
								<td><small>{{ value.alias }}</small></td>
								<td>
									<a title=" <?php echo($value->menu == 1 ? 'Visibile nel menu' : 'Nascosta dal menu'); ?>" class="btn btn-default btn-circle" href="javascript:void(0);"><i class="far {{ value.menu == 1 ? 'fa-eye' : 'fa-eye-slash' }}"> </i></a>
								</td>		

								<td>
									<a class="btn btn-default btn-circle" href="{{ URLSITEADMIN }}page-admin-preview/{{ value.id }}" target="_blank"><i class="far fa-eye"> </i></a>
								</td>		
								
								{% if App.params.images == 1 %}
									<td>											
										<a class="btn btn-default btn-circle" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listIfil/{{ value.id }}" title="File associati"><i class="fas fa-file"> </i> </a>
										&nbsp;(<?php echo $value->files; ?>)										
									</td>	
								{% endif %}										

								{% if App.params.item_files == 1 %}
									<td>											
										<a class="btn btn-default btn-circle" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listIfil/{{ value.id }}" title="File associati"><i class="fas fa-file"> </i> </a>
										&nbsp;({{ value.files }})										
									</td>	
								{% endif %}												
							
								<td class="actions text-right">									
									<a class="btn btn-default btn-sm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ value.active == 1 ? 'disactive' : 'active' }}/{{ value.id  }}" title="{{ value.active == 1 ? Lang['disattiva']|capitalize : Lang['attiva']|capitalize }} {{ Lang['la voce'] }}"><i class="fa fa-{{ value.active == 1 ? 'unlock' : 'lock' }}"></i></a>			 
									<a class="btn btn-default btn-sm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/modify/{{ value.id }}" title="{{ Lang['modifica']|capitalize }} {{ Lang['la voce'] }}"><i class="far fa-edit"></i></a>
									<a class="btn btn-default btn-sm confirm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/delete/{{ value.id }}" title="{{ Lang['cancella']|capitalize }} {{ Lang['la voce'] }}"><i class="far fa-trash-alt"></i></a>
								</td>		
     						</tr> 

						{% endfor %}
					{% else %}
						<tr>
							{% if App.userLoggedData.is_root is defined and App.userLoggedData.is_root == 1 %}<td></td>{% endif %}
							<td colspan="{{ colspan }}">{{ Lang['Nessuna voce trovata!'] }}</td>
						</tr>
					{% endif %}				
				</tbody>       
			</table>
		</div>
		<!-- /.table-responsive -->
		
	</div>
</div>