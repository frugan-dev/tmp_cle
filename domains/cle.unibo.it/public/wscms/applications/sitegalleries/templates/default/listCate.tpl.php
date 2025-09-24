<!-- wscms/site-galleries/listCate.tpl.php 09/06/2016 -->
<div class="row">
	<div class="col-md-3 new">
		<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/newCate" title="{{ Lang['inserisci una nuova %ITEM%']|replace({'%ITEM%':Lang['galleria'] })|capitalize }}" class="btn btn-sm btn-primary">{{ Lang['nuova %ITEM%']|replace({'%ITEM%':Lang['galleria'] })|capitalize }}</a>
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
		<form role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listCate" method="post" enctype="multipart/form-data">
			
		<div class="form-group row">
				<div class="col-md-1">
					<select name="itemsforpage" id="itemsforpageID" class="custom-select custom-select-sm" onchange="this.form.submit();">
						<option value="5" {% if App.itemsForPage == 5 %} selected="selected" {% endif %}>5</option>
						<option value="10" {% if App.itemsForPage == 10 %} selected="selected" {% endif %}>10</option>
						<option value="25" {% if App.itemsForPage == 25 %} selected="selected" {% endif %}>25</option>
						<option value="50" {% if App.itemsForPage == 50 %} selected="selected" {% endif %}>50</option>
						<option value="100" {% if App.itemsForPage == 100 %} selected="selected" {% endif %}>100</option>
					</select>
				</div>
				<label for="itemsforpageID" class="col-md-2 col-form-label form-control-sm">{{ Lang['voci per pagina']| capitalize }}</label>

				<label for="searchFromTableID" class="offset-md-6 col-md-1 col-form-label form-control-sm" style="text-align:right;">{{ Lang['cerca']|capitalize }}</label>
				<div class="col-md-2">
					<input type="search" name="searchFromTable" id="searchFromTableID" class="form-control form-control-sm" value="{% if MySessionVars[App.sessionName]['srcTab'] is defined and  MySessionVars[App.sessionName]['srcTab'] != '' %}{{  MySessionVars[App.sessionName]['srcTab'] }}{% endif %}" onchange="this.form.submit();">
				</div>
			</div>

			<!-- table-responsive -->		
			<div class="table-responsive">			
					<table class="table table-striped table-bordered table-hover listData">
						<thead>
							<tr>
								{% if (App.userLoggedData.is_root is defined) and (App.userLoggedData.is_root is same as(1)) %}	
									<td class="id">{{ value.id }}</td>
								{% endif %}
								<th>Titolo</th>
								<th>Cartella</th>		
								<th>Immagini</th>						
								<th></th>
							</tr>
						</thead>
						<tbody>				
							{% set colspan = 4 %}
							{% if (App.userLoggedData.is_root is defined) and (App.userLoggedData.is_root is same as(1)) %}
								{% set colspan = colspan + 1 %}
							{% endif %}	
							{% if App.params.moduleAccessWrite is defined and App.params.moduleAccessWrite == 1 %}
								{% set colspan = colspan + 1 %}
							{% endif %}	
							{% if App.items is iterable and App.items|length > 0 %}
								{% for key,value in App.items %}
									<tr>										
									{% if (App.userLoggedData.is_root is defined) and (App.userLoggedData.is_root is same as(1)) %}	
											<td class="id">{{ value.id }}</td>
										{% endif %}
										<td>{{ value.title_it }}</td>
										<td>{{ value.Cateer_name }}</td>
  										<td><a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listItem/{{ value.id }}" title="Vai al contenuto della cartella"><i class="fas fa-images"></i> - {{ value.numitems }}</a></td>
									
										<td class="actions">
											<a class="btn btn-default btn-sm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ value.active == 1 ? 'disactive' : 'active' }}Cate/{{ value.id  }}" title="{{ value.active == 1 ? Lang['disattiva']|capitalize : Lang['attiva']|capitalize }} {{ Lang['la voce'] }}"><i class="fa fa-{{ value.active == 1 ? 'unlock' : 'lock' }}"></i></a>			 
											<a class="btn btn-default btn-sm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/modifyCate/{{ value.id }}" title="{{ Lang['modifica']|capitalize }} {{ Lang['la voce'] }}"><i class="far fa-edit"></i></a>
											<a class="btn btn-default btn-sm confirm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/deleteCate/{{ value.id }}" title="{{ Lang['cancella']|capitalize }} {{ Lang['la voce'] }}"><i class="far fa-trash-alt"></i></a>
										</td>						
										{% endfor %}
						{% else %}
							<tr>
 								<td colspan="{{ colspan }}">Nessuna voce trovata!</td>
							</tr>
						{% endif %}	

						</tbody>
					</table>
									</div>
			<!-- /.table-responsive -->							
			{% if App.pagination.itemsTotal > 0 %}
			<div class="row">
				<div class="col-md-6">
					<div class="pagination-info">
						{{ App.paginationTitle }}
					</div>	
				</div>
				<div class="col-md-6">
					<nav aria-label="Page navigation example">
						<ul class="pagination pagination-sm">
							<li class="page-item previous{% if App.pagination.page == 1 %} disabled{% endif %}">
								<a class="page-link" title="{{ Lang['pagina']|capitalize }} {{ Lang['precedente'] }}" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageCate/{{ App.pagination.itemPrevious }}">{{ Lang['precedente']|capitalize }}</a>
							</li>
							
							{% if App.pagination.pagePrevious is iterable %}
								{% for key,value in App.pagination.pagePrevious %}
									<li class="page-item"><a class="page-link" title="{{ Lang['vai alla pagina %ITEM%']|replace({'%ITEM%':value })|capitalize }}" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageCate/{{ value }}">{{ value }}</a></li>
								{% endfor %}
							{% endif %}
								
							<li class="page-item active"><a class="page-link active" title="{{ Lang['pagina corrente']|capitalize }}" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageCate/{{ App.pagination.page }}">{{ App.pagination.page }}</a></li>
								
							{% if App.pagination.pageNext is iterable %}
								{% for key,value in App.pagination.pageNext %}
									<li class="page-item"><a class="page-link" title="{{ Lang['vai alla pagina %ITEM%']|replace({'%ITEM%':value })|capitalize }}" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageCate/{{ value }}">{{ value }}</a></li>
								{% endfor %}
							{% endif %}
							
							
							<li class="page-item next{% if App.pagination.page >= App.pagination.totalpage %} disabled{% endif %}">
								<a class="page-link" title="{{ Lang['pagina']|capitalize }} {{ Lang['prossima'] }}" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageCate/{{ App.pagination.itemNext }}">{{ Lang['prossima']|capitalize }}</a>
							</li>
						</ul>
					</nav>
				</div>
			</div>
			{% endif %}	
		</form>	
	</div>
</div>