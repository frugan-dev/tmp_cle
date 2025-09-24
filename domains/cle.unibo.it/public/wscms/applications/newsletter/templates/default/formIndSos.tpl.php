<!-- wscms/newsletter/formIndSos.tpl.php v.1.0.0. 27/06/2016 -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		<?php if (isset($this->App->params->help_small) && $this->App->params->help_small != '') echo SanitizeStrings::xss($this->App->params->help_small); ?>
	</div>
	<div class="col-md-2">
	</div>
</div>

<div class="row">	
	<form id="applicationForm" class="form-horizontal" role="form" action="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/<?php echo $this->App->methodForm; ?>"  enctype="multipart/form-data" method="post">
		
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#datibase" data-toggle="tab">Dati Base</a></li>
		</ul>
		
				<!-- Tab panes -->
		<div class="tab-content">
		
			<div class="tab-pane active" id="datibase">
				<fieldset>
					<div class="form-group">
						<label for="nameID" class="col-sm-3 control-label">Nome</label>
						<div class="col-sm-7">
							<input required type="text" name="name" class="form-control" id="nameID" placeholder="Inserisci un nome" value="<?php if(isset( $this->App->item->name)) echo SanitizeStrings::cleanForFormInput( $this->App->item->name); ?>">
				    	</div>
					</div>
					<div class="form-group">
						<label for="surnameID" class="col-sm-3 control-label">Cognome</label>
						<div class="col-sm-7">
							<input required type="text" name="surname" class="form-control" id="surnameID" placeholder="Inserisci un cognome" value="<?php if(isset( $this->App->item->surname)) echo SanitizeStrings::cleanForFormInput($this->App->item->surname); ?>">
				    	</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label for="emailID" class="col-sm-3 control-label">Email</label>
						<div class="col-sm-3">
							<input required type="email" name="email" class="form-control" id="emailID" placeholder="Inserisci un indirizzo email"  value="<?php if(isset( $this->App->item->email)) echo SanitizeStrings::cleanForFormInput($this->App->item->email); ?>">
				    	</div>

					</div>
				</fieldset>
				
				
				<?php if ($this->App->params->categories == 1) : ?>
					<hr>
					<fieldset>
						<div class="form-group">
							<label for="id_catsID" class="col-md-3 control-label">Id Categorie associate</label>
							<div class="col-md-7">
							<?php
							$itemCats = [];
							if ($this->App->item->id_cats != '') $itemCats = explode(',',(string) $this->App->item->id_cats);				
							?>
								<?php if(is_array($this->App->item_cats) && count($this->App->item_cats) > 0): ?>
								<select required name="id_cats[]" size="15" multiple id="id_catsID">
									<?php foreach ($this->App->item_cats AS $value): ?>
									<option value="<?php echo $value->id; ?>"<?php if (is_array($itemCats) && in_array($value->id,$itemCats)) echo ' selected="selected"'; ?>><?php echo $value->id.' - '.htmlspecialchars((string) $value->title_it,ENT_QUOTES,'UTF-8'); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
								</select>								
					    	</div>
						</div>						
					</fieldset>	
				<?php endif; ?>
			
				
			</div>
<!-- sezione datibase -->	  
		 
		</div>
		<!--/Tab panes -->	
		<hr>
		
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-7">
				<input type="hidden" name="active" id="activeID" value="<?php if(isset($this->App->item->active)) echo $this->App->item->active; ?>">
				<input type="hidden" name="created" id="createdID" value="<?php if(isset($this->App->item->created)) echo $this->App->item->created; ?>">
				<input type="hidden" name="id" id="idID" value="<?php if(isset($this->App->id)) echo $this->App->id; ?>">
				<input type="hidden" name="hash" id="hashID" value="<?php if(isset($this->App->item->hash)) echo $this->App->item->hash; ?>">
				<input type="hidden" name="dateconfirmed" id="dateconfirmedID" value="<?php if(isset($this->App->item->dateconfirmed)) echo $this->App->item->dateconfirmed; ?>">
				<input type="hidden" name="confirmed" id="confirmedID" value="<?php if(isset($this->App->item->confirmed)) echo $this->App->item->confirmed; ?>">					
				<input type="hidden" name="method" value="<?php echo $this->App->methodForm; ?>">
				<button type="submit" name="submitForm" value="submit" class="btn btn-primary">Invia</button>
				<?php if ($this->App->id > 0): ?>
					<button type="submit" name="applyForm" value="apply" class="btn btn-primary">Applica</button>
				<?php endif; ?>
			</div>
			<div class="col-sm-2">				
				<a href="<?php echo URL_SITE_ADMIN; ?><?php echo Core::$request->action; ?>/listIndSos" title="Torna alla lista" class="btn btn-success">Indietro</a>
			</div>
		</div>
		
	</form>
 </div>