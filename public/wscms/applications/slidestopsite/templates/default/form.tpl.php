<!-- slides-topsite/form.tpl.php v.2.6.3. 02/05/2016 -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		<?php if (isset($this->App->params->help_small) && $this->App->params->help_small != '') echo SanitizeStrings::xss($this->App->params->help_small); ?>
	</div>
	<div class="col-md-2 help">
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#datibase-tab" data-toggle="tab">Dati Base <i class="fa"></i></a></li>	
			<?php foreach($this->globalSettings['languages'] AS $lang): ?>
			<li><a href="#contents-<?php echo $lang; ?>-tab" data-toggle="tab">Contenuti <?php echo $lang; ?> <i class="fa"></i></a></li>	
			<?php endforeach; ?>	
		</ul>
		
		<form id="applicationForm" class="form-horizontal" role="form" action="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/<?php echo $this->App->methodForm; ?>"  enctype="multipart/form-data" method="post">

			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane active" id="datibase-tab">
<!-- sezione image --> 					
					<fieldset>
						<div class="form-group">
							<label for="filenameID" class="col-md-2 control-label">File</label>
							<div class="col-md-4">
								<input<?php if ($this->App->item->filenameRequired == true) echo ' required'; ?> type="file" name="filename" id="filenameID"  placeholder="Indica un file da caricare">
								
							</div>
						</div>
						<div class="form-group">
							<label for="filenameID" class="col-md-2 control-label">Anteprima</label>
							<div class="col-md-7">
								<?php if (isset($this->App->item->filename) && $this->App->item->filename != ''): ?>
								<a class="" href="<?php echo $this->App->uploadDir; ?><?php echo $this->App->item->filename; ?>" rel="prettyPhoto[]" title="Zoom immagine">
									<img  class="img-thumbnail"  src="<?php echo $this->App->uploadDir; ?><?php echo $this->App->item->filename; ?>" alt="<?php echo $this->App->item->filename; ?>">
								</a>							
								<?php endif; ?>
							</div>			
						</div>
					</fieldset>
	<!-- /sezione image --> 
					<hr>
	<!-- sezione modulo -->	
					<fieldset>
							<div class="form-group">
								<label for="moduloID" class="col-md-2 control-label">Sezione sito associata<br>(alias modulo)</label>
								<div class="col-md-7">
								<select name="modulo" id="moduloID">
									<option></option>

									
											<?php if(is_array($this->App->modules) && count($this->App->modules) > 0): ?>
												<?php foreach ($this->App->modules AS $value): ?>
													<?php //if ($value->alias != ''): ?>
														<option value="<?php echo $value->alias; ?>"<?php if (isset($this->App->item->modulo) && $this->App->item->modulo == $value->alias) echo ' selected="selected"'; ?>><?php echo $value->label; ?></option>
													<?php //endif; ?>									
												<?php endforeach; ?>										
											<?php endif; ?>
									
								</select>																	
						    	</div>
							</div>
						</fieldset>	
	<!-- sezione modulo -->			

						<hr>
	<!-- sezione opzioni --> 	
	
					<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
					<?php if($this->mySessionVars['usr']['root'] === 1): ?>			
					<fieldset>
						<div class="form-group">
							<label for="orderingID" class="col-md-2 control-label">Ordine</label>
							<div class="col-md-1">
								<input type="text" name="ordering" placeholder="Inserisci un ordine" class="form-control" id="orderingID" value="<?php if(isset($this->App->item->ordering)) echo $this->App->item->ordering; ?>">
					    	</div>
						</div>
					</fieldset>
					<?php else: ?>
						<input type="hidden" name="ordering" value="<?php if(isset($this->App->item->ordering)) echo $this->App->item->ordering; ?>">		
					<?php endif; ?>
					<!-- fine se root -->
		
					<fieldset>
						<div class="form-group">
							<label for="activeID" class="col-md-2 control-label">Attiva</label>
							<div class="col-md-7">
								<input type="checkbox" name="active" id="activeID" <?php if(isset($this->App->item->active) && $this->App->item->active == 1) echo 'checked="checked"'; ?> value="1">
				    		</div>
				  		</div>
					</fieldset>	
	<!-- sezione opzioni -->									
				</div>
				
				<?php foreach($this->globalSettings['languages'] AS $lang): 
					$titleField = 'title_'.$lang;
					$titleValue = (isset($this->App->item->$titleField) ? htmlspecialchars($this->App->item->$titleField,ENT_QUOTES,'UTF-8') : '');
					$content1Field = 'content_'.$lang;
					$content1Value = ($this->App->item->$content1Field ?? '');
				?>		
				<div class="tab-pane" id="contents-<?php echo $lang; ?>-tab">
					<fieldset>
						<div class="form-group">
							<label for="title_<?php echo $lang; ?>ID" class="col-md-2 control-label">Titolo <?php echo ucfirst((string) $lang); ?> </label>
							<div class="col-md-7">
								<input<?php if ($lang == 'it') echo ' required'; ?> type="text" class="form-control" name="title_<?php echo $lang; ?>" placeholder="Inserisci un titolo <?php echo ucfirst((string) $lang); ?>" id="title_<?php echo $lang; ?>ID" rows="3" value="<?php echo $titleValue; ?>">
							</div>
						</div>
					</fieldset>	
					
					<fieldset>
						<div class="form-group">
							<label for="content1_<?php echo $lang; ?>ID" class="col-md-2 control-label">Contenuto <?php echo ucfirst((string) $lang); ?> </label>
							<div class="col-md-8">
								<textarea name="content_<?php echo $lang; ?>" class="form-control editorHTML" id="content_<?php echo $lang; ?>ID" rows="5"><?php echo $content1Value; ?></textarea>
							</div>
						</div>
					</fieldset>			
				</div>
				<?php endforeach; ?>
						 
			</div>
			<!--/Tab panes -->	
			<hr>
			
			<div class="form-group">
				<div class="col-md-offset-2 col-md-7">
					<input type="hidden" name="created" id="createdID" value="<?php if(isset($this->App->item->created)) echo $this->App->item->created; ?>">
					<input type="hidden" name="id" value="<?php if(isset($this->App->id)) echo $this->App->id; ?>">
					<input type="hidden" name="method" value="<?php echo $this->App->methodForm; ?>">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary">Invia</button>
					<?php if ($this->App->id > 0): ?>
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary">Applica</button>
					<?php endif; ?>
				</div>	
				<div class="col-md-2">				
					<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/list" title="Torna alla lista" class="btn btn-success">Indietro</a>
				</div>
			</div>
		
		</form>
	</div>
 </div>