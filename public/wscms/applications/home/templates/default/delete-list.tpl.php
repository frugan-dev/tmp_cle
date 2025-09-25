<!-- 	wscms/site-home/list.tpl.php v.2.0.0. 15/09/2016 -->

<div class="row">
	<div class="col-lg-4 new">
		<h5>{{ Lang['ultimo accesso']|capitalize }}: {{ App.lastLoginLocale }}</h5>
 	</div>
	<div class="col-md-7 help-small-list">
		{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}
	</div>
	<div class="col-md-1">
	</div>
</div>

<div class="row">
	{% if App.homeBlocks is iterable %}
		{% for key,value in App.homeBlocks %}
			{% if value['items'] > 0 %}
				<div class="col-lg-3 col-md-6">
					<div class="panel {{ value['class'] }}">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa {{ value['icon panel'] }} fa-4x"></i>
									{{ value['sex suffix'] }}
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">{{ value['items'] }}</div>
									<div>{{ value['label'] }}</div>							
								</div>
							</div>
						</div>
						<a href="{{ value['url item']['string'] }}">
							<div class="panel-footer">
								<span class="pull-left">{{ Lang['vedi dettagli']|title }}</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
			{% endif %}
		{% endfor %}
	{% endif %}
</div>
<hr class="divider-top-module">
<div class="row">
	{% if App.homeTables is iterable %}
		{% for key,value in App.homeTables %}
			{% if value['itemdata'] is iterable %}
				<div class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa {{ value['icon panel'] }} fa-fw"></i> {{ value['label'] }}                           
						</div>		
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-bordered table-hover table-striped listDataHome">
											<thead>
												<tr>
													<th><small>Data<small></th>
													<th><small>ID</small></th>
													{% if value['fields'] is iterable %}
														{% for keyF,valueF in value['fields'] %}
															<th>
																{{ valueF['label'] }}
															</th>												
														{% endfor %}
													{% endif %}		
												</tr>
											</thead>
											<tbody>
												{% if value['itemdata'] is iterable %}
													{% for keyItemData,valueItemData in value['itemdata'] %}
														<tr>
															<td class="data" style="width:60px;">
																{{ valueItemData.datacreated|raw }}
															</td>
															<td class="id" style="width:40px;">
																{{ valueItemData.id }}
															</td>
															{% if value['fields'] is iterable %}
																{% for keyF,valueF in value['fields'] %}
																	<td>
																		{% set method %}{{ keyF }}{% endset %}
																		{{ attribute(valueItemData, method)|raw }}
																	</td>
																{% endfor %}
															{% endif %}														
														</tr>
													{% endfor %}
												{% endif %}				
											</tbody>										
										</table>
									</div>
									<!-- /.table-responsive -->
								</div>
							</div>
							<!-- /.row -->
						</div>
						<!-- /.panel-body -->			
					</div>
				</div>
			{% endif %}
		{% endfor %}
	{% endif %}
</div>