<!--  wscms/site-pages/formIfil.tpl.php v.1.0.1. 07/09/2016  -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		<?php if (isset($this->App->params->help_small) && $this->App->params->help_small != '') echo SanitizeStrings::xss($this->App->params->help_small); ?>
	</div>
	<div class="col-md-2 help">
	</div>
</div>
<div class="row well well-sm">	
	<div class="col-md-2"> 
		Dettagli <?php echo $this->App->labels['ifil']['owner']; ?>:
	</div>
	<div class="col-md-10"> 
		<big><?php echo htmlspecialchars((string) $this->App->ownerData->title_it,ENT_QUOTES,'UTF-8'); ?></big>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
			
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#datibase-tab" data-toggle="tab">Dati Base</a></li>
		</ul>
		
		<form id="applicationForm" class="form-horizontal" role="form" action="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/<?php echo $this->App->methodForm; ?>"  enctype="multipart/form-data" method="post">

			<!-- Tab panes -->
			<div class="tab-content">
			
				<div class="tab-pane active" id="datibase-tab">
					
					<fieldset>
					<!-- sezione dati base dinamica lingue -->
								<?php foreach($this->globalSettings['languages'] AS $lang): 
									$titleField = 'title_'.$lang;
									$titleValue = ($this->App->item->$titleField ?? '');			?>		
									<div class="form-group">
										<label for="title_<?php echo $lang; ?>ID" class="col-md-2 control-label">Titolo <?php echo ucfirst((string) $lang); ?> </label>
										<div class="col-md-7">
											<input<?php if ($lang == 'it') echo ' required'; ?> type="text" class="form-control" name="title_<?php echo $lang; ?>" placeholder="Inserisci un titolo <?php echo ucfirst((string) $lang); ?>" id="title_<?php echo $lang; ?>ID" rows="3" value="<?php echo htmlspecialchars($titleValue,ENT_QUOTES,'UTF-8'); ?>">
										</div>
									</div>
								<?php endforeach; ?>
					<!-- /sezione dati base dinamica lingue -->
					</fieldset>	
					<hr>	
					<fieldset>
						<div class="form-group">
							<label for="filenameID" class="col-md-2 control-label">File</label>
							<div class="col-md-4">
								<input<?php if ($this->App->item->filenameRequired == true) echo ' required'; ?> type="file" name="filename" id="filenameID"  placeholder="Indica un file da caricare">
								
							</div>
						</div>
						<div class="form-group">
							<label for="filenameID" class="col-md-2 control-label">Nome File</label>
							<div class="col-md-7">
								<?php if(isset($this->App->item->filename) && $this->App->item->filename != ''): ?>
									<?php echo $this->App->item->filename; ?>
								<?php endif; ?>					
							</div>			
						</div>
					</fieldset>
					
					<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->		
					<fieldset>
						<div class="form-group">
							<label for="orderingID" class="col-md-2 control-label">Ordine</label>
							<div class="col-md-1">
								<input type="text" name="ordering" placeholder="Inserisci un ordine" class="form-control" id="orderingID" value="<?php if(isset($this->App->item->ordering)) echo $this->App->item->ordering; ?>">
					    	</div>
						</div>
					</fieldset>
					<!-- fine se root -->	

		
					<fieldset>
						<div class="form-group">
							<label for="activeID" class="col-md-3 control-label">Attiva</label>
							<div class="col-md-7">
								<input type="checkbox" name="active" id="activeID" <?php if(isset($this->App->item->active) && $this->App->item->active == 1) echo 'checked="checked"'; ?> value="1">
				    		</div>
				  		</div>
					</fieldset>
		
				</div>
		
			</div>
			<!--/Tab panes -->	
			<hr>
		
			<div class="form-group">
				<div class="col-md-offset-2 col-md-7">
					<input type="hidden" name="created" id="createdID" value="<?php if(isset($this->App->item->created)) echo $this->App->item->created; ?>">
					<input type="hidden" name="id" value="<?php if(isset($this->App->id)) echo $this->App->id ?>">
					<?php if(isset($this->App->id_owner)): ?>
						<input type="hidden" name="id_owner" value="<?php echo $this->App->id_owner; ?>">
					<?php endif; ?>
					<input type="hidden" name="method" value="<?php echo $this->App->methodForm; ?>">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary">Invia</button>
					<?php if ($this->App->id > 0): ?>
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary">Applica</button>
					<?php endif; ?>
				</div>	
				<div class="col-md-2">				
					<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/listIfil" title="Torna alla lista" class="btn btn-success">Indietro</a>
				</div>
			</div>

		</form>
	</div>
</div>