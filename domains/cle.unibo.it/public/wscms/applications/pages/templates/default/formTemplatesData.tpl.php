<!-- site-pages/formTemplatesData.tpl.php v.3.5.2. 14/02/2018 -->
<div class="form-group">
	<div class="col-sm-6">
		<?php echo $template->content; ?>
	</div>
	<div class="col-sm-6">
		<?php if($template->filename != ''): ?>
		<a href="<?php echo $App->params->template['defuploaddir']; ?><?php echo $template->filename; ?>" data-lightbox="image-1" data-title="{{ value.org_filename }}" title="<?php echo $template->filename; ?>">
			<img src="<?php echo $App->params->template['defuploaddir']; ?><?php echo $template->filename; ?>" class="img-thumbnail" alt="<?php echo $template->filename; ?>">
		</a>
		<?php else: ?>
			<img src="<?php echo $App->params->uploadDirs['item']; ?>default/image.png" class="img-thumbnail" alt="<?php ucfirst($App->lang['immagine di default']); ?>">
		<?php endif; ?>
	</div>
</div>
					