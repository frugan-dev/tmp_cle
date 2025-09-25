<!-- pages/form.tpl.php v.1.0.5. MULTI UTF-8 02/04/2015 -->
<div class="row">
	<div class="col-lg-12">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#template-tab" data-toggle="tab">Template</a></li>
			<li><a href="#options-tab" data-toggle="tab">Opzioni</a></li>

			<?php foreach($this->globalSettings['languages'] AS $value): ?>		
				<li><a href="#datibase<?php echo $value; ?>-tab" data-toggle="tab">Dati Base <?php echo ucfirst((string) $value); ?> <i class="fa"></i></a></li>
				<li><a href="#contents<?php echo $value; ?>-tab" data-toggle="tab">Contenuti <?php echo ucfirst((string) $value); ?> <i class="fa"></i></a></li>
			<?php endforeach; ?>
			
			<?php if ($this->appData->templateItem->images > 0 ): ?>
			<li><a href="#pageImages-tab" data-toggle="tab">Immagini</a></li>
			<?php endif; ?>			
			<?php if ($this->appData->templateItem->files > 0 ): ?>
			<li><a href="#pageFile-tab" data-toggle="tab">File</a></li>
			<?php endif; ?>			
			<?php if ($this->appData->templateItem->galleries > 0 ): ?>
			<li><a href="#pageGalleries-tab" data-toggle="tab">Gallerie</a></li>
			<?php endif; ?>
			<?php if ($this->appData->templateItem->blocks > 0 ): ?>
			<li><a href="#pageBlocks-tab" data-toggle="tab">Blocchi Contenuto</a></li>
			<?php endif; ?>			
		</ul>
		<!--/Nav tabs -->
		
		<form id="applicationForm" class="form-horizontal" id="pagesFormID" role="form" action="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/<?php echo $this->App->methodForm; ?>"  enctype="multipart/form-data" method="post">
		
			<!-- Tab panes -->
			<div class="tab-content">
				
<!--  template --> 
				<div class="tab-pane active" id="template-tab">
				
				<?php if ($this->mySessionVars['uLv'] == 'root'): ?>		
					<fieldset>
						<div class="form-group">
							<label class="col-sm-2 control-label">ID pagina</label>
							<div class="col-sm-1">
								<?php if(isset($this->App->item->id)) echo $this->App->item->id; ?>
					    	</div>
						</div>
					</fieldset>
					<?php endif; ?>
					
					<fieldset>
						<div class="form-group">
							<label for="id_templateID" class="col-sm-2 control-label">Template</label>
							<div class="col-sm-7">							
								<select name="id_template" id="id_templateID">								
									<?php if (is_array($this->appData->templatesItem) && count($this->appData->templatesItem) > 0): ?>
										<?php foreach($this->appData->templatesItem AS $key=>$value): ?>		
											<option value="<?php echo $value->id; ?>"<?php if(isset($this->appData->templateItem->id) && $this->appData->templateItem->id == $value->id) echo ' selected="selected"'; ?>>&nbsp;<?php echo $value->title_it; ?>&nbsp;</option>													
										<?php endforeach; ?>
									<?php endif; ?>		
								</select>
										
					    	</div>
						</div>
					</fieldset>	
						
					<fieldset id="templateDataID">			
						<div class="form-group">
							<div class="col-sm-6">
								<?php echo $this->appData->templateItem->comment_it; ?>
							</div>
							<div class="col-sm-6">
								<?php if($this->appData->templateItem->filename != ''): ?>
								<a href="<?php echo URL_SITE; ?>uploads/site_templates/<?php echo $this->appData->templateItem->filename; ?>" rel="prettyPhoto[]" title="Zoom immagine">
									<img src="<?php echo URL_SITE; ?>uploads/site_templates/<?php echo $this->appData->templateItem->filename; ?>" class="img-thumbnail" alt="<?php echo $this->appData->templateItem->title_it; ?>">
								</a>
								<?php else: ?>
									<img src="<?php echo URL_SITE; ?>uploads/site_templates/default/image.png" class="img-thumbnail" alt="Immagine di default">
								<?php endif; ?>
							</div>
						</div>
					</fieldset>
						
				</div>
	<!--/template -->	
	
	<!-- sezione opzioni --> 
				<div class="tab-pane" id="options-tab">
				
					<fieldset>					
						<div class="form-group">
							<label for="type" class="col-sm-2 control-label">Tipo</label>
							<div class="col-sm-7">							
								<select name="type">								
									<?php if (is_array($this->appData->typePage) && count($this->appData->typePage) > 0): ?>
										<?php foreach($this->appData->typePage AS $key=>$value): ?>		
											<option value="<?php echo $value; ?>"<?php if(isset($this->App->item->type) && $this->App->item->type == $value) echo ' selected="selected"'; ?>><?php echo $value; ?></option>													
										<?php endforeach; ?>
									<?php endif; ?>		
								</select>									
					    	</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<label for="parentID" class="col-sm-2 control-label">Genitore</label>
							<div class="col-sm-7">							
								<select name="parent">
									<option value="0"></option>
									<?php if (is_array($this->appData->subCategories) && count($this->appData->subCategories) > 0): ?>
										<?php foreach($this->appData->subCategories AS $value): ?>		
											<?php
											$s = '';												
											foreach ($value->breadcrumbs AS $key1 => $value1) {
												if ( $value1['title_it'] != '') $s .= $value1['title_it'].'->';
												}										
											$s = $s.$value->title_it;									
											?>
											<option value="<?php echo $value->id; ?>"<?php if(isset($this->App->item->parent) && $this->App->item->parent == $value->id) echo ' selected="selected"'; ?>><?php echo $s; ?></option>														
										<?php endforeach; ?>
									<?php endif; ?>		
								</select>									
					    	</div>
						</div>
					</fieldset>				
					<!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->	
					<?php if ($this->mySessionVars['uLv'] == 'root'): ?>		
					<fieldset>
						<div class="form-group">
							<label for="orderingID" class="col-sm-2 control-label">Ordine</label>
							<div class="col-sm-1">
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
							<label for="activeID" class="col-sm-2 control-label">Attiva</label>
							<div class="col-sm-7">
								<input type="checkbox" name="active" id="activeID" <?php if(isset($this->App->item->active) && $this->App->item->active == 1) echo 'checked="checked"'; ?> value="1">
				    		</div>
				  		</div>
					</fieldset>		
				</div>
	<!--/sezione opzioni -->




<!--sezione generazione automatica tab dati base e contenuti in base alla lingua -->


<?php 
foreach($this->globalSettings['languages'] AS $lang): 
	$titleMETAField = 'title_meta_'.$lang;
	$titleMETAValue = (isset($this->App->item->$titleMETAField) ? htmlentities($this->App->item->$titleMETAField) : '');
	$titleSEOField = 'title_seo_'.$lang;
	$titleSEOValue = (isset($this->App->item->$titleSEOField) ? htmlentities($this->App->item->$titleSEOField) : '');
	$titleField = 'title_'.$lang;
	$titleValue = (isset($this->App->item->$titleField) ? htmlentities($this->App->item->$titleField) : '');

?>	

	<div class="tab-pane" id="datibase<?php echo $lang; 1?>-tab">	

		<fieldset>
			<div class="form-group">
				<label for="title_meta_<?php echo $lang; ?>ID" class="col-lg-2 control-label">Titolo META <?php echo ucfirst((string) $lang); ?> </label>
				<div class="col-lg-7">
					<input type="text" class="form-control" name="title_meta_<?php echo $lang; ?>" placeholder="Inserisci un titolo META <?php echo ucfirst((string) $lang); ?>" id="title_meta_<?php echo $lang; ?>ID" rows="3" value="<?php echo $titleMETAValue; ?>">
				</div>
			</div>
		</fieldset>

		<fieldset>
			<div class="form-group">
				<label for="title_seo_<?php echo $lang; ?>ID" class="col-lg-2 control-label">Titolo SEO <?php echo ucfirst((string) $lang); ?> </label>
				<div class="col-lg-7">
					<input type="text" class="form-control" name="title_seo_<?php echo $lang; ?>" placeholder="Inserisci un titolo SEO <?php echo ucfirst((string) $lang); ?>" id="title_seo_<?php echo $lang; ?>ID" rows="3" value="<?php echo $titleSEOValue; ?>">
				</div>
			</div>
		</fieldset>

		<fieldset>
			<div class="form-group">
				<label for="title_<?php echo $lang; ?>ID" class="col-lg-2 control-label">Titolo <?php echo ucfirst((string) $lang); ?> </label>
				<div class="col-lg-7">
					<input type="text"<?php if ($lang == 'it') echo ' required'; ?> class="form-control" name="title_<?php echo $lang; ?>" placeholder="Inserisci un titolo <?php echo ucfirst((string) $lang); ?>" id="title_<?php echo $lang; ?>ID" rows="3" value="<?php echo $titleValue; ?>">
				</div>
			</div>
		</fieldset>



	</div>

	<div class="tab-pane" id="contents<?php echo $lang; ?>-tab">

				<?php if ($this->appData->templateItem->contents_html > 0 ): ?>	
				
				
					<?php for ($x=1;$x<=$this->appData->templateItem->contents_html;$x++){ ?>	
					
						<div class="form-group">
							<label for="content_html_<?php echo $lang; ?>_<?php echo $x; ?>ID" class="col-sm-3 control-label">Contenuto HTML <?php echo $x; ?> <?php echo $lang; ?></label>
							<div class="col-sm-7">
								<?php 
								$s = '';
								$arr = (array)$this->App->item->pageContents;
								if(is_array($arr) && array_key_exists('content_'.$lang.'_'.$x,$arr)){								
									$s = $arr['content_'.$lang.'_'.$x];
									}	
								?>
								<textarea name="content_html_<?php echo $lang; ?>_<?php echo $x; ?>" class="form-control editorHTML" id="content_html_<?php echo $lang; ?>_<?php echo $x; ?>ID" rows="15"><?php echo $s; ?></textarea>
					    	</div>
						</div>
					<?php } ?>
				
			
				<?php endif; ?>







	</div>


<?php 
endforeach; 
?>
<!--/sezione generazione automatica tab dati base e contenuti in base alla lingua -->

	<!-- images	 -->
				<?php if ($this->appData->templateItem->images > 0): ?>
				<div class="tab-pane" id="pageImages-tab">
					<h5>Immagini associate<br>
					In base al tipo di template scelto la IMMAGINE 1 sarà sempre riferita all'immagine top della pagina e la IMMAGINE 2 sarà riferita alla immagine a destra nel contenuto 
					</h5>
					<fieldset>				
						<?php for ($x=1;$x<=$this->appData->templateItem->images;$x++): ?>
						<div class="form-group">
							<label for="image_<?php echo $x; ?>ID" class="col-sm-3 control-label">Immagine <?php echo $x; ?></label>						
							<select name="image[<?php echo $x; ?>]" id="image_<?php echo $x; ?>ID">		
								<option value=""></option>				
								<?php if (is_array($this->appData->selectPageImages) && count($this->appData->selectPageImages) > 0): ?>
									<?php foreach($this->appData->selectPageImages AS $key=>$value): ?>		
										<option value="<?php echo $value->id; ?>"<?php if(isset($this->App->item->pageImages[$x]->id_image) && $this->App->item->pageImages[$x]->id_image == $value->id) echo ' selected="selected"'; ?>><?php echo $value->title_it; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>	
							</select>		
						</div>													
						<?php endfor; ?>	
						
					</fieldset>			
				</div>
				<?php endif; ?>	
	
	<!--/images -->	
	
	<!-- file	 -->
				<?php if($this->appData->templateItem->files > 0): ?>
				<div class="tab-pane" id="pageFile-tab">
					<h5>File associati</h5>
					<fieldset>				
						<?php for ($x=1;$x<=$this->appData->templateItem->files;$x++): ?>
						<div class="form-group">
							<label for="file_<?php echo $x; ?>ID" class="col-sm-3 control-label">File <?php echo $x; ?></label>						
							<select name="file[<?php echo $x; ?>]" id="file_<?php echo $x; ?>ID">		
								<option value=""></option>				
								<?php if (is_array($this->appData->selectPageFiles) && count($this->appData->selectPageFiles) > 0): ?>
									<?php foreach($this->appData->selectPageFiles AS $key=>$value): ?>		
										<option value="<?php echo $value->id; ?>"<?php if(isset($this->App->item->pageFiles[$x]->id_file) && $this->App->item->pageFiles[$x]->id_file == $value->id) echo ' selected="selected"'; ?>><?php echo $value->title_it; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>	
							</select>		
						</div>													
						<?php endfor; ?>		
					
					</fieldset>			
				</div>
				<?php endif; ?>	
	
	<!--/file -->
	
	<!-- gallerie	 -->
				<?php if($this->appData->templateItem->galleries > 0): ?>
				<div class="tab-pane" id="pageGalleries-tab">
					<h5>Gallerie associate</h5>
					<fieldset>				
						<?php for ($x=1;$x<=$this->appData->templateItem->galleries;$x++): ?>
						<div class="form-group">
							<label for="gallery_<?php echo $x; ?>ID" class="col-sm-3 control-label">Galleria <?php echo $x; ?></label>						
							<select name="gallery[<?php echo $x; ?>]" id="file_<?php echo $x; ?>ID">		
								<option value=""></option>				
								<?php if (is_array($this->appData->selectPageGalleries) && count($this->appData->selectPageGalleries) > 0): ?>
									<?php foreach($this->appData->selectPageGalleries AS $key=>$value): ?>		
										<option value="<?php echo $value->id; ?>"<?php if(isset($this->App->item->pageGalleries[$x]->id_gallery) && $this->App->item->pageGalleries[$x]->id_gallery == $value->id) echo ' selected="selected"'; ?>><?php echo $value->title_it; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>	
							</select>		
						</div>													
						<?php endfor; ?>		
					
					</fieldset>			
				</div>
				<?php endif; ?>	
	
	<!--/gallerie -->		
	
	<!-- blocchi	 -->
				<?php if($this->appData->templateItem->blocks > 0): ?>
				<div class="tab-pane" id="pageBlocks-tab">
					<h5>Blocchi associati</h5>
					<fieldset>				
						<?php for ($x=1;$x<=$this->appData->templateItem->blocks;$x++): ?>
						<div class="form-group">
							<label for="block_<?php echo $x; ?>ID" class="col-sm-3 control-label">Blocco <?php echo $x; ?></label>						
							<select name="block[<?php echo $x; ?>]" id="block_<?php echo $x; ?>ID">		
								<option value=""></option>				
								<?php if (is_array($this->appData->selectPageBloks) && count($this->appData->selectPageBloks) > 0): ?>
									<?php foreach($this->appData->selectPageBloks AS $key=>$value): ?>		
										<option value="<?php echo $value->id; ?>"<?php if(isset($this->App->item->pageBlocks[$x]->id_block) && $this->App->item->pageBlocks[$x]->id_block == $value->id) echo ' selected="selected"'; ?>><?php echo $value->title_it; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>	
							</select>		
						</div>													
						<?php endfor; ?>		
					
					</fieldset>			
				</div>
				<?php endif; ?>	
	<!--/blocchi -->	



			</div>
			<!--/Tab panes -->	
				
			<hr>	
			
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-7">
			    	<input type="hidden" name="id" value="<?php if(isset($this->App->item->id)) echo $this->App->item->id; ?>">
			    	<input type="hidden" name="method" value="<?php echo $this->App->methodForm; ?>">		    	
			    	<input type="hidden" name="bk_parent" value="<?php if(isset($this->App->item->parent)) echo $this->App->item->parent; ?>">
			      <button type="submit" name="submitForm" value="submit" class="btn btn-primary">Invia</button>
			      <button type="submit" name="applyForm" value="apply" class="btn btn-primary">Applica</button>
				</div>
	 			<div class="col-sm-2">				
					<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/list" title="Torna alla lista" class="btn btn-success">Indietro</a>
				</div>
			</div>

		</form>
	</div>
</div>
