<!-- site-pages/formTemplatesData.tpl.php v.2.6.2.1. 04/03/2016 -->
<div class="form-group">
	<div class="col-sm-6">
		<?php echo $template->comment_it; ?>
	</div>
	<div class="col-sm-6">
		<?php if($template->filename != ''): ?>
		<a href="<?php echo $App->templateUploadDir; ?><?php echo $template->filename; ?>" rel="prettyPhoto[]" title="<?php echo $template->filename; ?>">
			<img src="<?php echo $App->templateUploadDir; ?><?php echo $template->filename; ?>" class="img-thumbnail" alt="<?php echo $template->filename; ?>">
		</a>
		<?php else: ?>
			<img src="<?php echo $App->templateUploadDirDef; ?>default/image.png" class="img-thumbnail" alt="Immagine di default">
		<?php endif; ?>
	</div>
</div>
					