<div id="app" class="container">
	<div class="card-panel">
		<h2 class="center-align"><b>¡Bienvenido!</b></h2>
		<form enctype="application/x-www-form-urlencoded" 
		@submit.prevent="submit_form" ref="log_form">
			<div class="input-field">
				<input id="mail" type="email" name="email" required>
				<label for="mail">Correo:</label>
			</div>
			<div class="input-field">
				<input id="pass" type="password" name="password" required>
				<label for="pass">Contraseña: </label>
			</div>
			<div class="input-field">
				<input type="submit" value="Iniciar sesion" class="btn">
			</div>
		</form>
		<hr>
		<p>¿No tiene una cuenta? <a href="register">¡Registrare!</a></p>
	</div>
</div>


<script>
	let app = new Vue({
		el: "#app",
		methods: {
			submit_form() {
				let fdata = new FormData(this.$refs.log_form);
				axios({
					url: "login",
					method: "post",
					data: fdata
				})
					.then(result => {
						window.location.replace("files");
					})
					.catch(err => {
						M.toast({ html: "Error de inicio de sesion, vuela a intentarlo" });
					});
			}
		}
	});
</script>
