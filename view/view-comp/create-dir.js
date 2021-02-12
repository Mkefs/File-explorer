Vue.component("new-dir", {
	template: 
	`<div id="new_dir" class="modal">
		<form @submit.prevent="upload_dir">
			<div class="modal-content">
				<h4>Crear carpeta</h4>
				<div class="input-field">
					<input type="text" id="dir" name="dirname" required>
					<label class="active" for="dir">Nombre</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="waves-effect btn btn-flat">
					Crear
				</button>
				<a class="modal-close waves-effect btn btn-flat">Cerrar</a>
			</div>
		</form>
	</div>`,
	props: ["current_dir"],
	methods: {
		upload_dir(e) {
			let dirname = e.target.elements['dirname'].value;
			// Upload 
			axios({
				url: "create_dir",
				params: { 
					dir: this.current_dir,
					name: dirname
				},
				methods: "POST"
			})
				.then(response => {
					console.log(response.data);
				})
				.catch(err => {
					M.toast({ html: "Error creando la carpeta, vuelvalo a intentar" });
				});
			// Close modal and update
			this.$emit("update");
			let el = e.target.parentElement;
			let inst = M.Modal.getInstance(el);
			inst.close();
		}
	}
});
