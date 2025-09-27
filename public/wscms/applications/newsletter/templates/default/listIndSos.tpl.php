<!-- wscms/newsletter/listIndSos.tpl.php v.1.0.0. 18/07/2016 -->
<div class="row">
	<div class="col-md-12 new">
 		<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/deleteOldIndSos" title="Cancella gli indirizzi più vecchi di un mese" class="btn btn-primary">Cancella gli indirizzi più vecchi di un mese</a>
 	</div>
	<div class="col-md-7 help-small-list">
		<?php if (isset($this->App->params->help_small) && $this->App->params->help_small != '') {
		    echo SanitizeStrings::xss($this->App->params->help_small);
		} ?>
	</div>
	<div class="col-md-2">
	</div>
</div>
<hr class="divider-top-module">
<form role="form" action="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/listIndSos" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">			
			<div class="form-inline" role="grid">								
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>
								<select class="form-control input-md" name="itemsforpage" onchange="this.form.submit();" >
									<option value="5"<?php if ($this->App->itemsForPage == 5) {
									    echo ' selected="selected"';
									} ?>>5</option>
									<option value="10"<?php if ($this->App->itemsForPage == 10) {
									    echo ' selected="selected"';
									} ?>>10</option>
									<option value="25"<?php if ($this->App->itemsForPage == 25) {
									    echo ' selected="selected"';
									} ?>>25</option>
									<option value="50"<?php if ($this->App->itemsForPage == 50) {
									    echo ' selected="selected"';
									} ?>>50</option>
									<option value="100"<?php if ($this->App->itemsForPage == 100) {
									    echo ' selected="selected"';
									} ?>>100</option>
								</select>
								Voci per pagina
							</label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group pull-right">
							<label>
								Search:
								<input name="searchFromTable" value="<?php if (isset($this->mySessionVars[$this->App->sessionName]['srcTab']) && $this->mySessionVars[$this->App->sessionName]['srcTab'] != '') {
								    echo SanitizeStrings::htmlout($this->mySessionVars[$this->App->sessionName]['srcTab']);
								} ?>" class="form-control input-sm" type="search" onchange="this.form.submit();">
							</label>
						</div>
					</div>
				</div>
					
				<div class="table-responsive">				
					<table class="table table-striped table-bordered table-hover listData">
						<thead>
							<tr>
								<?php if ($this->mySessionVars['usr']['root'] === 1): ?>
									<th>ID</th>								
								<?php endif; ?>
								<th>Data</th>	
								<th>Nome</th>
								<th>Cognome</th>
								<th>Email</th>										
								<th></th>
							</tr>
						</thead>
						<tbody>				
							<?php if (is_array($this->App->items) && count($this->App->items) > 0): ?>
								<?php foreach ($this->App->items as $key => $value):
								    $data = DateTime::createFromFormat('Y-m-d H:i:s', $value->created);
								    $errors = DateTime::getLastErrors();
								    ?>
									<tr>
										<?php if ($this->mySessionVars['usr']['root'] === 1): ?>
											<td><?php echo $value->id; ?></td>
										<?php endif; ?>
										<td><?php echo $data->format('d/m/Y H:i:s'); ?></td>
										<td><?php echo $value->name; ?></td>
										<td><?php echo $value->surname; ?></td>
										<td><?php echo $value->email; ?></td>																
										<td class="actions">
											<a onclick="bootbox.confirm();" class="btn btn-default btn-circle confirm" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/confirmIndSos/<?php echo $value->id; ?>" title="Conferma"><i class="fa fa-thumbs-up"> </i></a>
											<a class="btn btn-default btn-circle" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/modifyIndSos/<?php echo $value->id; ?>" title="Modifica"><i class="fa fa-edit"> </i> </a>
											<a onclick="bootbox.confirm();" class="btn btn-default btn-circle confirm" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/deleteIndSos/<?php echo $value->id; ?>" title="Cancella"><i class="fa fa-cut"> </i></a>
										</td>							
									</tr>	
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<?php if ($this->mySessionVars['usr']['root'] === 1): ?><td></td><?php endif; ?>
									<td colspan="5">Nessuna voce trovata!</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<!-- /.table-responsive -->
					
				<?php if ($this->App->pagination->itemsTotal > 0): ?>
				<div class="row">
					<div class="col-md-6">
						<div class="dataTables_info" id="dataTables_info" role="alert" aria-live="polite" aria-relevant="all">
							Mostra da <?php echo $this->App->pagination->firstPartItem ?> a <?php echo $this->App->pagination->lastPartItem; ?> di <?php echo $this->App->pagination->itemsTotal; ?> elementi
						</div>	
					</div>
					<div class="col-md-6">
						<div class="dataTables_paginate paging_simple_numbers" id="dataTables_paginate">
							<ul class="pagination">
								<li class="paginate_button previous<?php if ($this->App->pagination->page == 1) {
								    echo ' disabled';
								} ?>">
									<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIndSos/<?php echo $this->App->pagination->itemPrevious; ?>">Precedente</a>
								</li>
								
								<?php if (is_array($this->App->pagination->pagePrevious)): ?>
									<?php foreach ($this->App->pagination->pagePrevious as $key => $value): ?>
										<li><a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIndSos/<?php echo $value; ?>"><?php echo $value; ?></a></li>
									<?php endforeach; ?>
								<?php endif; ?>
									
								<li class="active"><a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIndSos/<?php echo $this->App->pagination->page; ?>"><?php echo $this->App->pagination->page; ?></a></li>
									
								<?php if (is_array($this->App->pagination->pageNext)): ?>
									<?php foreach ($this->App->pagination->pageNext as $key => $value): ?>
										<li><a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIndSos/<?php echo $value; ?>"><?php echo $value; ?></a></li>
									<?php endforeach; ?>
								<?php endif; ?>
								
								
								<li class="paginate_button next <?php if ($this->App->pagination->page >= $this->App->pagination->totalpage) {
								    echo ' disabled';
								} ?>">
									<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIndSos/<?php echo $this->App->pagination->itemNext; ?>">Prossima</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>	
			<!-- /.form-inline wrapper -->
		</div>
		<!-- /.col-md-12 -->
	</div>
</form>