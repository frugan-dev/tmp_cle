<!-- wscms/pages/listPvid.tpl.php v.3.5.2. 19/02/2018 -->
<div class="row">
	<div class="col-md-3 new">
 		<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/newBfil" title="{{ Lang['inserisci un nuovo file']|capitalize }}" class="btn btn-primary">{{ Lang['nuovo file']|capitalize }}</a>
	</div>
	<div class="col-md-7 help-small-list">
		{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}
	</div>
	<div class="col-md-2">
 		<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listIblo" title="{{ Lang['torna alla lista blocchi']|capitalize }}" class="btn btn-primary">{{ Lang['lista blocchi']|capitalize }}</a>
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
		<img  class="img-thumbnail"  src="{{ App.params.uploadDirs['iblo'] }}default/image.png" alt="{{ Lang['immagine di default']|capitalize }}">									
		{% endif %}
	</div>
	<div class="col-md-8"> 
		<big>{{ App.ownerData.title }}</big>
	</div>
</div>
<hr class="divider-top-module">
<form role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/listBfil" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
				<div class="form-inline" role="grid">						
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>
									<select class="form-control input-sm" name="itemsforpage" onchange="this.form.submit();" >
										<option value="5"{% if App.itemsForPage == 5 %} selected="selected"{% endif %}>5</option>
										<option value="10"{% if App.itemsForPage == 10 %} selected="selected"{% endif %}>10</option>
										<option value="25"{% if App.itemsForPage == 25 %} selected="selected"{% endif %}>25</option>
										<option value="50"{% if App.itemsForPage == 50 %} selected="selected"{% endif %}>50</option>
										<option value="100"{% if App.itemsForPage == 100 %} selected="selected"{% endif %}>100</option>
									</select>
									{{ Lang['voci per pagina']| capitalize }}
								</label>								
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group pull-right">
								<label>
									{{ Lang['cerca']|capitalize }}:
								<input name="searchFromTable" value="{% if MySessionVars[App.sessionName]['srcTab'] is defined and  MySessionVars[App.sessionName]['srcTab'] != '' %}{{ MySessionVars[App.sessionName]['srcTab']|e('html') }}{% endif %}" class="form-control input-sm" type="search" onchange="this.form.submit();">
								</label>
							</div>
						</div>
					</div>
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover listData">
						<thead>
							<tr>
								{% if App.userLoggedData.is_root == 1 %}
									<th class="id">ID</th>								
								{% endif %}
								<th class="ordering">{{ Lang['ordine abb']|capitalize }}</th>
								<th>{{ Lang['titolo']|capitalize }}</th>					
								<th>{{ Lang['file']|capitalize }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>				
							{% if App.items is iterable and App.items|length > 0 %}
								{% for key,value in App.items %}
									<tr>
										{% if App.userLoggedData.is_root == 1 %}
											<td class="id">{{ value.id }}</td>
										{% endif %}
										<td class="ordering">
											{% if App.userLoggedData.is_root == 1 %}	
												<small>{{ value.ordering }}&nbsp;</small>
											{% endif %}
											<a class="" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.params.ordersType['bfil'] == 'ASC' ? 'less' : 'more' }}OrderingBfil/{{ value.id }}" title="{{ Lang['sposta']|capitalize }} {{ App.params.ordersType['bfil'] == 'DESC' ? Lang['giu'] : Lang['su'] }}"><i class="fa fa-long-arrow-up"> </i></a>
											<a class="" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.params.ordersType['bfil'] == 'ASC' ? 'more' : 'less' }}OrderingBfil/{{ value.id }}" title="{{ Lang['sposta']|capitalize }}  {{ App.params.ordersType['bfil'] == 'DESC' ? Lang['su'] : Lang['giu'] }}"><i class="fa fa-long-arrow-down"> </i></a>
										</td>								
										<td>{{ value.title }}</a></td>
										<td>	
											<a class="" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/downloadBfil/{{ value.id }}" title="<?php echo ucfirst((string) $App->lang['scarica il file']); ?> {{ value.org_filename }}">
												{{ value.filename }}
											</a>
										</td>	
										<td class="actions">
											<a class="btn btn-default btn-circle" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ value.active == 1 ? 'disactive' : 'active' }}Bfil/{{ value.id }}" title="{{ value.active == 1 ? Lang['disattiva']|capitalize : Lang['attiva']|capitalize }} {{ Lang['file'] }}"><i class="fa fa-{{ value.active == 1 ? 'unlock' : 'lock' }}"> </i></a>			 
											<a class="btn btn-default btn-circle" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/modifyBfil/{{ value.id }}" title="{{ Lang['modifica']|capitalize }} {{ Lang['file'] }}"><i class="fa fa-edit"> </i></a>
											<a class="btn btn-default btn-circle confirm" href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/deleteBfil/{{ value.id }}" title="{{ Lang['cancella']|capitalize }} {{ Lang['file'] }}"><i class="fa fa-cut"> </i></a>
										</td>							
									</tr>	
								{% endfor %}
							{% else %}
								<tr>
									{% if App.userLoggedData.is_root == 1 %}<td colspan="2"></td>{% endif %}
									<td colspan="4">{{ Lang['Nessuna voce trovata!'] }}</td>
								</tr>
							{% endif %}
						</tbody>
					</table>
				</div>
				<!-- /.table-responsive -->

				{% if App.pagination.itemsTotal > 0 %}
				<div class="row">
					<div class="col-md-6">
						<div class="dataTables_info" id="dataTables_info" role="alert" aria-live="polite" aria-relevant="all">
							{{ App.paginationTitle }}
						</div>	
					</div>
					<div class="col-md-6">
						<div class="dataTables_paginate paging_simple_numbers" id="dataTables_paginate">
							<ul class="pagination">
								<li class="paginate_button previous{% if App.pagination.page == 1 %} disabled{% endif %}">
									<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageBfil/{{ App.pagination.itemPrevious }}">{{ Lang['precedente']|capitalize }}</a>
								</li>									
								{% if App.pagination.pagePrevious is iterable %}
									{% for key,value in App.pagination.pagePrevious %}
										<li><a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageBfil/{{ value }}">{{ value }}</a></li>
									{% endfor %}
								{% endif %}										
								<li class="active"><a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageBfil/{{ App.pagination.page }}">{{ App.pagination.page }}</a></li>										
								{% if App.pagination.pageNext is iterable %}
									{% for key,value in App.pagination.pageNext %}
										<li><a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageBfil/{{ value }}">{{ value }}</a></li>
									{% endfor %}
								{% endif %}									
								<li class="paginate_button next {% if App.pagination.page >= App.pagination.totalpage %} disabled{% endif %}">
									<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/pageBfil/{{ App.pagination.itemNext }}">{{ Lang['prossima']|capitalize }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				{% endif %}
			</div>	
			<!-- /.form-inline wrapper -->
		</div>
		<!-- /.col-md-12 -->
	</div>
</form>