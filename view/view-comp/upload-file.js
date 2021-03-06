Vue.component("upload", {
	template:
	`<div id="upload" class="modal modal-fixed-footer">
		<form enctype="multipart/form-data" ref="f_form" @submit.prevent="upload_file">
			<div class="modal-content">
				<h4>Subir archivos</h4>
				<input required type="file" class="hide" name="files[]" 
				multiple @input="update_files">
				<div class="pding10 upload_file container-flex hoverable 
				grey-text grey lighten-3" @dragover.prevent.stop="drag_f" 
				@drop.prevent.stop="drop_e" @click="click_files" id="dragable">
					<p v-show="files.length < 1">Arrastra los archivos aqui</p>
					<div v-for="file in files">
						<div style="max-width: 100%;" class="btn btn-flat truncate">
							<span class="typcn typcn-document"></span> {{file}}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-flat modal-close">Subir</button>
				<a class="btn btn-flat modal-close">Cerrar</a>
			</div>
		</form>
	</div>`,
	props: ["current_dir"],
	data() {
		return {
			files: []
		};
	},
	methods: {
		upload_file(e) {
			let formdata = new FormData(this.$refs.f_form);
			axios({
				url: "upload_file",
				data: formdata,
				params: { dir: this.current_dir },
				method: "POST"
			})
				.then(resp => {
					let results = resp.data;
					for(let i = 0; i < results.length; i++) {
						if(results[i] != 0) {
							if(results[i] == 1)
								M.toast({ 
									html: `El archivo ${i + 1} exece el limite de subida(2MB)`
								});
							else 
								M.toast({ html: "Error al subir archivo" });
						}
					}
					this.$emit("update");
				})
				.catch(err => {
					M.toast({ html: "Error subiendo archivos" });
				});
		},
		click_files(e) {
			this.$refs.f_form.elements["files[]"].click();
		},
		update_files(e) {
			let archivos = e.target.files;
			this.files = [];
			for(let i = 0; i < archivos.length; i++) {
				this.files.push(archivos[i].name);
			}
		},
		drop_e(e) {
			let archivos = e.dataTransfer.files;
			this.$refs.f_form.elements["files[]"].files = archivos;
			this.files = [];
			for(let i = 0; i < archivos.length; i++) {
				this.files.push(archivos[i].name);
			}
		},
		drag_f(e){
			e.dataTransfer.dropEffect = "copy";
		}
	}
});
