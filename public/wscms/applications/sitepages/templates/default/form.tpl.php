<!-- site-pages/form.tpl.php v.1.0.1. 08/09/2016 -->
<div class="row">
	<div class="col-md-3 new"></div>
	<div class="col-md-7 help-small-form">{% if App.params.help_small is defined and App.params.help_small != '' %}{{ App.params.help_small }}{% endif %}</div>
	<div class="col-md-2 help text-right">
		{% if (App.params.help is defined) and (App.params.help != '') %}
			<button class="btn btn-warning btn-sm" type="button" data-target="#helpModal" data-toggle="modal">Come funziona?</button>
		{% endif %}
	</div>
</div>

<div class="card shadow mt-3 mb-4">
	<div class="card-body">

    	<!-- Nav tabs -->
		<ul class="nav nav-tabs" id="formTab" role="tablist">

            <li class="nav-item">
                <a class="nav-link active" href="#template" id="template-tab" data-toggle="tab" role="tab" aria-controls="tab" aria-selected="true">Template</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#options" id="options-tab" data-toggle="tab" role="tab" aria-controls="options" aria-selected="true">{{ Lang['opzioni']|capitalize }}</a>
            </li>
            {% for lang in GlobalSettings['languages'] %}
            <li class="nav-item">
                <a class="nav-link" href="#datibase{{ lang }}" id="datibase{{ lang }}-tab" data-toggle="tab" role="tab" aria-controls="datibase{{ lang }}" aria-selected="true">{{ Lang['dati base']|capitalize }} {{ lang }}</a>
            </li>
            {% endfor %}

            {% if App.templateItem.images > 0 %}
            <li class="nav-item">
                <a class="nav-link" href="#pageImages"  id="pageImages-tab" data-toggle="tab" role="tab" aria-controls="pageImages" aria-selected="true">Immagini</a>
            </li>
            {% endif %}		

            {% if App.templateItem.files > 0 %}
            <li class="nav-item">
                <a class="nav-link" href="#pageFile" id="pageFile-tab" data-toggle="tab" role="tab" aria-controls="pageFile" aria-selected="true">File</a>
            </li>
            {% endif %}		

            {% if App.templateItem.galleries > 0 %}
            <li class="nav-item">
                <a class="nav-link" href="#pageGalleries" id="#pageGalleries-tab" data-toggle="tab" role="tab" aria-controls="pageGalleries" aria-selected="true">Gallerie</a>
            </li>
            {% endif %}

            {% if App.templateItem.blocks > 0 %}
            <li class="nav-item">
                <a class="nav-link" href="#pageBlocks" ref="pageBlocks-tab" data-toggle="tab" role="tab" aria-controls="pageBlocks" aria-selected="true">Blocchi Contenuto</a>
            </li>
            {% endif %}

            <li class="nav-item">
                <a class="nav-link" href="#advanced" id="advanced-tab" data-toggle="tab" role="tab" aria-controls="advanced" aria-selected="true">{{ Lang['avanzate']|capitalize }}</a>
            </li>

        </ul>
        <!--/Nav tabs -->

        <form id="applicationForm" class="form-horizontal" id="pagesFormID" role="form" action="{{ URLSITEADMIN }}{{ CoreRequest.action }}/{{ App.methodForm }}" enctype="multipart/form-data" method="post">
		
            <!-- Tab panes -->
            <div class="tab-content">

                <!--  template --> 
                <div class="tab-pane fade show active" id="template" role="tabpanel" aria-labelledby="template">	

					<fieldset>

                        {% if App.userLoggedData.is_root == 1 %}

                            <div class="form-group row">
                                <label class="col-md-2 control-label">ID pagina</label>
                                <div class="col-md-1">
                                    {{ App.item.id }}
                                </div>
                            </div>
                        
                        {% endif %}
					
					
						<div class="form-group row">
							<label for="id_templateID" class="col-md-2 control-label">Template</label>
							<div class="col-md-7">							
                                <select class="form-control input-md" name="id_template" id="id_templateID">								
									{% if App.templatesItem is iterable and App.templatesItem|length > 0 %}
										{% for key,value in App.templatesItem %}		
											<option value="{{ value.id }}"{% if App.templateItem.id == value.id %} selected="selected"{% endif %}>&nbsp;{{ value.title_it }}&nbsp;</option>													
										{% endfor %}
									{% endif %}	
								</select>
										
					    	</div>
						</div>
					
						
						<div id="templateDataID">			
							<div class="form-group row">
								<div class="col-md-6">
									{{ App.templateItem.comment_it }}
								</div>
								<div class="col-md-6">
									{% if App.templateItem.filename != '' %}
									<a href="{{ App.templateUploadDir }}{{ App.templateItem.filename }}" title="{{ App.templateItem.filename }}">
										<img src="{{ App.templateUploadDir }}{{ App.templateItem.filename }}" class="img-miniature" alt="{{ App.templateItem.filename }}">
									</a>
									{% else %}	
										<img src="{{ App.templateUploadDirDef }}default/image.png" class="img-miniature" alt="Immagine di default">
									{% endif %}	
								</div>
							</div>
						</div>

					</fieldset>
											
				</div>
				<!--/template -->	

                <!-- sezione opzioni --> 
                <div class="tab-pane fade show" id="options" role="tabpanel" aria-labelledby="options">
                    <fieldset>

                        <div class="form-group row">
							<label for="parentID" class="col-md-2 control-label">Genitore</label>
							<div class="col-md-7">							
								<select class="form-control input-md" name="parent" id="parentID">
									<option value="0"></option>
									{% if App.subCategories is iterable and App.subCategories|length > 0 %}
										{% for value in App.subCategories %}		
											{% set title = '' %}								
											{% for key1, value1 in value.breadcrumbs %}
                                                
												{% if value1['title_it'] != '' and loop.index < loop.length %}
													{% set title = title ~ value1['title_it'] ~ '->' %}	
												{% endif %}														
											{% endfor %}						
											{% set title = title ~ value.title_it %}
											<option value="{{ value.id }}"{% if App.item.parent == value.id %} selected="selected"{% endif %}>{{ title }}</option>														
										{% endfor %}
									{% endif %}	
								</select>									
					    	</div>
						</div>

                        <div class="form-group row">
							<label for="aliasID" class="col-md-2 control-label">Alias</label>
							<div class="col-md-7">
								<input type="text" class="form-control" name="alias" placeholder="Inserisci un titolo alias" id="aliasID" rows="3" value="{{ App.item.alias }}">
							</div>
						</div>

                        <hr>

                        <div class="form-group row">
							<label for="type" class="col-md-2 control-label">Tipo</label>
							<div class="col-md-7">							
								<select class="form-control input-md" name="type">								
									{% if App.typePage is iterable and App.typePage|length > 0 %}
										{% for key,value in App.typePage %}		
											<option value="{{ key }}"{% if App.item.type == key %} selected="selected"{% endif %}>{{ value }}</option>													
										{% endfor %}
									{% endif %}	
								</select>									
					    	</div>
						</div>	

                        <div class="form-group row">
                            <label for="urlID" class="col-md-2 control-label">URL<br>
                            <small>%URLSITE% per url dinamico</small></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="url" placeholder="Inserisci un URL %URLSITE% per url dinamico" id="urlID" rows="3" value="{{ App.item.url }}">
                            </div>								
                    
                            <label for="targetID" class="col-md-1 control-label">Target</label>
                            <div class="col-md-2">							
                                <select class="form-control input-sm" name="target">	
                                <option></option>
                                    {% if App.targets is iterable and App.targets|length > 0 %}
                                        {% for key,value in App.targets %}													
                                        <option value="{{ key }}"{% if App.item.target == key %} selected="selected"{% endif %}>{{ value }}</option>													
                                        {% endfor %}
                                    {% endif %}	
                                </select>							
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="moduleID" class="col-md-2 control-label">Link a modulo</label>
                            <div class="col-md-7">							
                                <select class="form-control input-sm col-md-3" name="module">	
                                <option></option>
                                    {% if App.modules is iterable and App.modules|length > 0 %}	
                                        {% for key,value in App.modules %}					                                 						
                                            <option value="{{ value.alias }}"{% if App.item.url == value.alias %} selected="selected"{% endif %} >{{ value.alias }}</option>																	
                                        {% endfor %}
                                    {% endif %}	
                                </select>							
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
							<label for="menuID" class="col-md-2 control-label">{{ Lang['menu']|capitalize }}</label>
							<div class="col-md-7">
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" name="menu" id="menuID" {% if App.item.menu==1 %}
											checked="checked" {% endif %} value="1">
									</label>
								</div>
							</div>
						</div>

                        <!-- se e un utente root visualizza l'input altrimenti lo genera o mantiene automaticamente -->
						{% if App.userLoggedData.is_root == 1 %}
						<div class="form-group row">
							<label for="orderingID" class="col-md-2 control-label">{{ Lang['ordine']|capitalize
								}}</label>
							<div class="col-md-1">
								<input type="text" name="ordering"
									placeholder="{{ Lang['inserisci un ordine']| capitalize }}" class="form-control"
									id="orderingID" value="{{ App.item.ordering }}">
							</div>
						</div>
						<hr>
						{% else %}
						<input type="hidden" name="ordering" value="{{ App.item.ordering }}">
						{% endif %}
						<!-- fine se root -->

                        <div class="row">
							<label for="updatedID" class="col-md-2 control-label">Aggiornata</label>
							<div class="col-sm-3">
								<div class="form-group">
									<div class="input-group date" id="updatedID" data-target-input="nearest">
										<input type="text" class="form-control datetimepicker-input" data-target="#updatedID" name="updated" />
										<div class="input-group-append" data-target="#updatedID" data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fa fa-calendar"></i></div>
										</div>
									</div>
								</div>
							</div>
						</div>  
                                
                        <div class="form-group row">
                            <label for="activeID" class="col-md-2 control-label">{{ Lang['attiva']|capitalize }}</label>
                            <div class="col-md-7">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="active" id="activeID" {% if App.item.active==1 %}
                                            checked="checked" {% endif %} value="1">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                                
                </div>
                <!--/sezione opzioni -->

				<!--sezione generazione automatica tab dati base e contenuti in base alla lingua -->
				{% for lang in GlobalSettings['languages'] %}
					{% set metaTitleField = "title_meta_#{lang}" %}
					{% set titleSeoField = "title_seo_#{lang}" %}
					{% set titleField = "title_#{lang}" %}
					{% set subtitleField = "subtitle_#{lang}" %}
					<div class="tab-pane tab-pane fade show" id="datibase{{ lang }}" role="tabpanel" aria-labelledby="datibase{{ lang }}">	
						<fieldset>

							<div class="form-group row">
								<label for="title_meta_{{ lang }}ID" class="col-md-2 control-label">Titolo META {{ lang }} </label>
								<div class="col-md-7">
									<input type="text" class="form-control" name="title_meta_{{ lang }}" placeholder="Inserisci un titolo META {{ lang }}" id="title_meta_{{ lang }}ID" rows="3" 
									value="{{ attribute(App.item, metaTitleField)|e('html') }}">
								</div>
							</div>

							<div class="form-group row">
								<label for="title_seo_{{ lang }}ID" class="col-md-2 control-label">Titolo SEO {{ lang }} </label>
								<div class="col-md-7">
									<input type="text" class="form-control" name="title_seo_{{ lang }}" placeholder="Inserisci un titolo SEO {{ lang }}" id="title_seo_{{ lang }}ID" rows="3" 
									value="{{ attribute(App.item, titleSeoField)|e('html') }}">
								</div>
							</div>

							<div class="form-group row">
								<label for="title_{{ lang }}ID" class="col-md-2 control-label">Titolo {{ lang }} </label>
								<div class="col-md-7">
									<input type="text"{% if lang == 'it' %} required{% endif %} class="form-control" name="title_{{ lang }}" 
									placeholder="Inserisci un titolo {{ lang }}" id="title_{{ lang }}ID" rows="3" 
									value="{{ attribute(App.item, titleField)|e('html') }}">
								</div>
							</div>

							<div class="form-group row">
								<label for="subtitle_{{ lang }}ID" class="col-md-2 control-label">Subtitolo {{ lang }} </label>
								<div class="col-md-7">
									<input type="text" class="form-control" name="subtitle_{{ lang }}" placeholder="Inserisci un subtitolo {{ lang }}" id="subtitle_{{ lang }}ID" 
									rows="3" 
									value="{{ attribute(App.item, subtitleField)|e('html') }}">
								</div>
							</div>

							<hr>
							{% if App.templateItem.contents_html > 0 %}	
								{% for key in 1..App.templateItem.contents_html|length %}						
									<div class="form-group row">
										<label for="content_html_{{ lang }}_{{ loop.index }}ID" class="col-md-2 control-label">Contenuto HTML {{ loop.index }} {{ lang }}</label>
										<div class="col-md-7">
											
											{% set s = '' %}
											{% set key = 'content_' ~ lang ~ '_' ~ loop.index  %}
											
											{% if key in App.item.pageContents|keys %}
												{% set s = App.item.pageContents[key] %}								
											{% endif %}
											<textarea 
											name="content_html_{{ lang }}_{{ loop.index }}" 
											class="form-control editorHTML" 
											id="content_html_{{ lang }}_{{ loop.index }}ID" 
											rows="15">{{ s }}</textarea>
										</div>
									</div>
									{% endfor %}
								{% endif %}
						</fieldset>
					</div>
				{% endfor %}
				<!--/sezione generazione automatica tab dati base e contenuti in base alla lingua -->

				<!-- images	 -->
				{% if App.templateItem.images > 0 %}	
					<div class="tab-pane fade show" id="pageImages" role="tabpanel" aria-labelledby="pageImages">
							<p>Immagini associate
							<br>
							In base al tipo di template scelto la IMMAGINE 1 sarà sempre riferita all'immagine top della pagina e la IMMAGINE 2 sarà riferita alla immagine a destra nel contenuto 
							</p>
						<fieldset>	
							{% for key in 1..App.templateItem.images %}				
							<div class="form-group row">
								<label for="image_{{ loop.index }}ID" class="col-md-3 control-label">Immagine {{ loop.index }}</label>						
								<select class="form-control input-md col-md-5" name="image[loop.index]" id="image_{{ loop.index }}ID">		
									<option value=""></option>				
									{% if App.selectPageImages is iterable and App.selectPageImages|length > 0 %}
										{% for key,value in App.selectPageImages %}			
											<option value="{{ value.id }}"{% if App.item.pageImages[loop.index].id_image == value.id %} selected="selected"{% endif %}>{{ value.title_it }}</option>
										{% endfor %}
									{% endif %}
								</select>		
							</div>													
							{% endfor %}
							
						</fieldset>			
					</div>
					{% endif %}
				<!--/images -->	

				<!-- file	 -->
				{% if App.templateItem.files > 0 %}	
					<div class="tab-pane fade show" id="pageFile" role="tabpanel" aria-labelledby="pageFile">
						<p>File associati</p>
						<fieldset>				
							{% for key in 1..App.templateItem.files %}	
							<div class="form-group row">		
								<label for="file_{{ loop.index }}ID" class="col-md-3 control-label">File {{ loop.index }}</label>						
								<select class="form-control input-md col-md-5" name="file[loop.index]" id="file_{{ loop.index }}ID">		
									<option value=""></option>
									{% if App.selectPageFiles is iterable and App.selectPageFiles|length > 0 %}
										{% for key,value in App.selectPageFiles %}				
											<option value="{{ value.id }}"{% if App.item.pageImages[loop.index].id_file == value.id %} selected="selected"{% endif %}>{{ value.title_it }}</option>	
										{% endfor %}
									{% endif %}
								</select>		
							</div>													
							{% endfor %}		
						</fieldset>			
					</div>
				{% endif %}
				<!--/file -->

				<!-- gallerie	 -->
				{% if App.templateItem.galleries > 0 %}	
				<div class="tab-pane fade show" id="pageGalleries" role="tabpanel" aria-labelledby="pageGalleries">
					<p>Gallerie associate</p>
					<fieldset>				
					{% for key in 1..App.templateItem.galleries %}
						<div class="form-group row">
							<label for="gallery_{{ loop.index }}ID" class="col-md-3 control-label">Galleria {{ loop.index }}</label>						
							<select class="form-control input-md col-md-5" name="gallery[loop.index]" id="file_{{ loop.index }}ID">		
								<option value=""></option>	
								{% if App.selectPageGalleries is iterable and App.selectPageGalleries|length > 0 %}
									{% for key,value in App.selectPageGalleries %}					
										<option value="{{ value.id }}"{% if App.item.pageImages[loop.index].id_gallery == value.id %} selected="selected"{% endif %}>{{ value.title_it }}</option>
									{% endfor %}
								{% endif %}
							</select>		
						</div>													
						{% endfor %}		
					
					</fieldset>			
				</div>
				{% endif %}
				<!--/gallerie -->
						
				<!-- blocchi	 -->
				{% if App.templateItem.blocks > 0 %}	
				<div class="tab-pane fade show" id="pageBlocks" role="tabpanel" aria-labelledby="pageBlocks">
					<p>Blocchi associati</p>
					<fieldset>				
					{% for key in 1..App.templateItem.blocks %}
						<div class="form-group row">
							<label for="block_{{ loop.index }}ID" class="col-md-3 control-label">Blocco {{ loop.index }}</label>						
							<select class="form-control input-md col-md-5" name="block[loop.index]" id="block_{{ loop.index }}ID">		
								<option value=""></option>
								{% if App.selectPageBloks is iterable and App.selectPageBloks|length > 0 %}
									{% for key,value in App.selectPageBlocks %}					
										<option value="{{ value.id }}"{% if App.item.pageImages[loop.index].id_block == value.id %} selected="selected"{% endif %}>{{ value.title_it }}</option>
									{% endfor %}
								{% endif %}
							</select>		
						</div>													
						{% endfor %}		
					
					</fieldset>			
				</div>
				{% endif %}
				<!--/blocchi -->	

				<!-- sezione avanzate -->	
				<div class="tab-pane fade show" id="advanced" role="tabpanel" aria-labelledby="advanced">
					<fieldset>
						<div class="form-group row">
							<label for="Jscript_init_codeID" class="col-md-2 control-label">Codice Javascript inizio BODY</label>
							<div class="col-md-7">
								<textarea name="jscript_init_code" class="form-control" id="jscript_init_codeID" rows="4">{{ App.item.jscript_init_code|raw }}</textarea>
							</div>
				  		</div>
					</fieldset>				
				</div>
<!--/sezione avanzate -->	

            </div>
			<!--/Tab panes -->	

            <hr>	
			
			<div class="form-group row">
				<div class="col-md-6 col-xs-12 text-center">
					<input type="hidden" name="created" id="createdID" value="{{ App.item.created }}">
			    	<input type="hidden" name="id" value="{{ App.item.id }}">
			    	<input type="hidden" name="method" value="{{ App.methodForm }}">		    	
			    	<input type="hidden" name="bk_parent" value="{{ App.item.parent }}">
			      	<button type="submit" name="submitForm" value="submit" class="btn btn-primary submittheform">Invia</button>
			      	<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform ml-5">Applica</button>
				</div>
				<div class="col-md-6 col-xs-12 text-right">	
					<a href="{{ URLSITEADMIN }}{{ CoreRequest.action }}/list" title="{{ Lang['torna alla lista']|capitalize }}" class="btn btn-success">{{ Lang['indietro']|capitalize }}</a>
				</div>

		</form>
	</div>
</div>
	