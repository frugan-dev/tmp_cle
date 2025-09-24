<!-- wscms/site-core/nopasssword.tpl.php v.3.5.1. 16/01/2018 -->
<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="login-panel panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ Lang['titolo sezione richiesta password 1'] }}</h3>
			</div>
			<div class="panel-body">
				<form class="form-signin" role="form" action="{{ URLSITEADMIN }}nopassword" method="post">
					<fieldset>
						<div class="form-group">
							<input required class="form-control" placeholder="{{ Lang['nome utente']|capitalize }}" name="username" type="text" oninvalid="this.setCustomValidity('Devi inserire un nome utente!')" oninput="setCustomValidity('')" autofocus>
						</div>
						<!-- Change this to a button or input when using this as a form -->
						<input type="hidden" name="method" value="check" />
						<input type="submit" name="submit" value="{{ Lang['invia']|capitalize }} {{ Lang['email']|capitalize }}" class="btn btn-lg btn-success btn-block">
					</fieldset>
				</form>
				
				
			</div>
			<div class="panel-footer">
					<p>{{ Lang['testo sezione richiesta password']|capitalize }}</p>  
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-body">
				{{ App.returnlink }}
			</div>			
		</div>
	</div>
</div>
