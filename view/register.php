<div id="app" class="container">
	<form @submit.prevent="submit_form" ref="reg_form" class="card-panel" 
	enctype="application/x-www-form-urlencoded">
		<div class="row center-align">
			<h4 class="col s11">
				<a class="col s1 typcn waves-effect typcn-arrow-left"
				   href="./" style="border-radius: 20px; color: inherit;"></a>
				<b>Registro</b>
			</h4>
		</div>			
		<div class="input-field">
			<input type="email" name="email" id="mail" required>
			<label for="mail">Email: </label>
		</div>
		<div class="input-field">
			<input type="password" name="password" required id="pass">
			<label for="pass">Contraseña</label>
		</div>
		<div class="input-field">
			<input type="password" id="pass2" required>
			<label for="pass2">Repite tu contraseña: </label>
		</div>
		<div class="input-field">
			<input type="submit" class="btn" value="Registrarse">
		</div>
	</form>
	<form @submit.prevent="verfi_account" ref="ver_form" class="card-panel"
	enctype="application/x-www-form-urlencoded">
		<div class="row center-align">
			<h4><b>Verificar correo</b></h4>
		</div>
		<div class="input-field">
			<input name="email" id="mail2" required type="email">
			<label for="mail2">Correo: </label>
		</div>
		<div class="input-field">
			<input id="code" name="code" required type="text">
			<label for="code">Codigo: </label>
		</div>
		<div class="input-field">
			<input type="submit" value="Verificar correo" class="btn">
		</div>
	</form>
</div>

<script>
	const app = new Vue({
		el: "#app",
		methods: {
			checkPassword() {
				let p1 = this.$refs.reg_form.elements['pass'].value;
				let p2 = this.$refs.reg_form.elements['pass2'].value;
				if(p1 === p2)
					return true;
				else
					return false;
			},
			submit_form() {
				let equals = this.checkPassword();
				let form = new FormData(this.$refs.reg_form);

				if(!equals) {
					M.toast({html: "Las contraseñas no coinciden"});
					return;
				}

				axios({
					url: "register",
					method: "POST",
					data: form
				})
					.then(response => {
						M.toast({
							html: 
							`La cuenta ha sido creada satisfactoriamente,
							verifique su correo para confirmar su cuenta`
						});
					})
					.catch(err => {
						console.log(err.response.data);
						if(err.response.data == 1062)
							M.toast({ html: "Cuenta ya existente, intente otro correo" });
						else
							M.toast({ html: "Ha habido un error" });
					});
			},
			verfi_account() {
				let fdata = new FormData(this.$refs.ver_form);
				axios({
					url: "verif",
					method: "POST",
					data: fdata
				})
					.then(response => {
						console.log(response.data);
						M.toast({
							html: "Cuenta verificada"
						});
					})
					.catch(err => {
						if(err.response.status == 409)
							M.toast({ html: "Error, cuenta ya verificada" });
						else
							M.toast({ html: "Error verificando la cuenta, vuelva a intentar" });
					})
			}
		}
	});
</script>
