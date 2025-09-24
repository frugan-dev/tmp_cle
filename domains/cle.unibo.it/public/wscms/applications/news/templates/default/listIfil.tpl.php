<!-- news/listIfil.tpl.php v.2.6.3. 07/04/2016 -->
<div class="row">
	<div class="col-md-3 new">
 		<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/newIfil" title="Inserisci un nuov<?php echo $this->App->labels['ifil']['itemSex']; ?> <?php echo $this->App->labels['ifil']['item']; ?>" class="btn btn-primary">Nuov<?php echo $this->App->labels['ifil']['itemSex']; ?> <?php echo $this->App->labels['ifil']['item']; ?></a>
	</div>
	<div class="col-md-7 help-small-list">
		<?php if (isset($this->App->params->help_small) && $this->App->params->help_small != '') echo SanitizeStrings::xss($this->App->params->help_small); ?>
	</div>
	<div class="col-md-2">
 		<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/listItem" title="Torna alla lista <?php echo $this->App->labels['ifil']['owners']; ?>" class="btn btn-primary">Lista <?php echo $this->App->labels['ifil']['owners']; ?></a>
	</div>
</div>
<div class="row well well-sm">	
	<div class="col-md-1"> 
		Dettagli <?php echo $this->App->labels['ifil']['owner']; ?>:
	</div>
	<div class="col-md-1"> 
		<?php if ($this->App->ownerData->filename != ''): ?>
		<a class="" href="<?php echo $this->App->itemUploadDir; ?><?php echo $this->App->ownerData->filename; ?>" rel="prettyPhoto[]" title="<?php echo $this->App->ownerData->org_filename; ?>">
			<img  class="img-thumbnail"  src="<?php echo $this->App->itemUploadDir; ?><?php echo $this->App->ownerData->filename; ?>" alt="<?php echo $this->App->ownerData->org_filename; ?>">
		</a>
		<?php else: ?>
		<a class="" href="<?php echo $this->App->itemUploadDir; ?>default/image.png" rel="prettyPhoto[]" title="Immagine di default">
			<img  class="img-thumbnail"  src="<?php echo $this->App->itemUploadDir; ?>default/image.png" alt="Immagine di default">
		</a>											
		<?php endif; ?>
	</div>
	<div class="col-md-10"> 
		<big><?php echo htmlspecialchars($this->App->ownerData->title_it,ENT_QUOTES,'UTF-8'); ?></big>
	</div>
</div>
<form role="form" action="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/listIfil" method="post" enctype="multipart/form-data">

	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<div class="form-inline" role="grid">						
					<div class="row">
						<div class="col-md-6">
							<div>
								<div class="form-group">
									<label>
										<select class="form-control input-sm" name="itemsforpage" onchange="this.form.submit();" >
											<option value="5"<?php if($this->App->itemsForPage == 5) echo ' selected="selected"'; ?>>5</option>
											<option value="10"<?php if($this->App->itemsForPage == 10) echo ' selected="selected"'; ?>>10</option>
											<option value="25"<?php if($this->App->itemsForPage == 25) echo ' selected="selected"'; ?>>25</option>
											<option value="50"<?php if($this->App->itemsForPage == 50) echo ' selected="selected"'; ?>>50</option>
											<option value="100"<?php if($this->App->itemsForPage == 100) echo ' selected="selected"'; ?>>100</option>
										</select>
										Voci per pagina
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="tables_filter text-right">
								<label>
									Search:
									<input name="searchFromTable" value="<?php if(isset($this->mySessionVars[$this->App->sessionName]['srcTab']) && $this->mySessionVars[$this->App->sessionName]['srcTab'] != '') echo htmlspecialchars($this->mySessionVars[$this->App->sessionName]['srcTab'],ENT_QUOTES,'UTF-8'); ?>" class="form-control input-sm" type="search" onchange="this.form.submit();">
								</label>
							</div>
						</div>
					</div>

					<table class="table table-striped table-bordered table-hover listData">
						<thead>
							<tr>
								<?php if ($this->mySessionVars['usr']['root'] === 1): ?>	
									<th>ID</th>								
								<?php endif; ?>
								<th>Titolo</th>							
								<th>File</th>
								<th></th>
							</tr>
						</thead>
						<tbody>				
							<?php if (is_array($this->App->items) && count($this->App->items) > 0): ?>
								<?php 
								foreach ($this->App->items AS $key => $value):	
								?>
									<tr>
										<?php if ($this->mySessionVars['usr']['root'] === 1): ?>	
											<td><?php echo $value->id; ?></td>
										<?php endif; ?>
										
										<td><?php echo htmlspecialchars($value->title_it,ENT_QUOTES,'UTF-8'); ?></a></td>
										<td>	
											<a class="" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/downloadIfil/<?php echo $value->id; ?>" title="Scarica il file">
												<?php echo $value->filename; ?>
											</a>	
										</td>	
										<td class="actions">
											<a class="btn btn-default btn-circle" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/<?php echo ($value->active == 1 ? 'disactive' : 'active'); ?>Ifil/<?php echo $value->id; ?>" title="<?php echo ($value->active == 1 ? 'Disattiva' : 'Attiva'); ?>"><i class="fa fa-<?php echo ($value->active == 1 ? 'unlock' : 'lock'); ?>"> </i> </a>			 
											<a class="btn btn-default btn-circle" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/modifyIfil/<?php echo $value->id; ?>" title="Modifica"><i class="fa fa-edit"> </i> </a>
											<a onclick="bootbox.confirm();" class="btn btn-default btn-circle confirm" href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/deleteIfil/<?php echo $value->id; ?>" title="Cancella"><i class="fa fa-cut"> </i></a>
										</td>							
									</tr>	
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<?php if ($this->mySessionVars['usr']['root'] === 1): ?><td colspan="2"></td><?php endif; ?>
									<td colspan="3">Nessuna voce trovata!</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>

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
									<li class="paginate_button previous<?php if ($this->App->pagination->page == 1) echo ' disabled'; ?>">
										<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIfil/<?php echo $this->App->pagination->itemPrevious; ?>">Precedente</a>
									</li>
									
									<?php if (is_array($this->App->pagination->pagePrevious)): ?>
										<?php foreach ($this->App->pagination->pagePrevious AS $key => $value): ?>
											<li><a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIfil/<?php echo $value; ?>"><?php echo $value; ?></a></li>
										<?php endforeach; ?>
									<?php endif; ?>
										
									<li class="active"><a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIfil/<?php echo $this->App->pagination->page; ?>"><?php echo $this->App->pagination->page; ?></a></li>
										
									<?php if (is_array($this->App->pagination->pageNext)): ?>
										<?php foreach ($this->App->pagination->pageNext AS $key => $value): ?>
											<li><a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIfil/<?php echo $value; ?>"><?php echo $value; ?></a></li>
										<?php endforeach; ?>
									<?php endif; ?>
									
									
									<li class="paginate_button next <?php if ($this->App->pagination->page >= $this->App->pagination->totalpage) echo ' disabled'; ?>">
										<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/pageIfil/<?php echo $this->App->pagination->itemNext; ?>">Prossima</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<?php endif; ?>

				</div>	
				<!-- /.dataTables-example_wrapper		 -->
			</div>
			<!-- /.table-responsive -->
		</div>
		<!-- /.col-md-12 -->
	</div>
</form>