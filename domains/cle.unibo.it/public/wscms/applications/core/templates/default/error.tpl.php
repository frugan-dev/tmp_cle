
 <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- 404 Error Text -->
          <div class="text-center">
            <div class="error mx-auto" data-text="404">{{ App.error_title }}</div>
            <p class="lead text-gray-800 mb-5">{{ App.error_subtitle }}</p>
            {% if App.error_content is defined and App.error_content != '' %}
            	<p class="text-gray-500 mb-0">{{ App.error_content }}</p>
            	{% if App.error_contentAlt is defined and App.error_contentAlt != '' %}<p class="text-gray-500 mb-0">{{ App.error_contentAlt }}</p>{% endif %}	
            {% endif %}	
            	
            <a href="{{ URLSITEADMIN }}" title="{{ Lang['Torna alla pagina %ITEM%']|replace({'%ITEM%': Lang['home']}) }}">&larr; {{ Lang['Torna alla pagina %ITEM%']|replace({'%ITEM%': Lang['home']}) }}</a>
          </div>

        </div>
        <!-- /.container-fluid -->
