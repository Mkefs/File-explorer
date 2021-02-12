<div id="app">
	<navbar  @change="change_tab"></navbar>
	<!-- Table and action buttons -->
	<div id="files" class="container">
		<h5 style="font-weight: bold; margin: 2rem 0;">~/{{dirsnames.join("/")}}</h5>
		<div class="container-flex card" style="display: flex;">
			<span class="btn btn-flat typcn typcn-arrow-up-thick" @click="updir" 
				:class="{disabled: !dirs_sum.length}"></span>
			<span class="typcn" style="flex: 1 1 auto;"></span>
			<span class="btn btn-flat typcn typcn-download" 
				:class="{disabled: !(selected.length > 0)}"></span>
			<span class="btn btn-flat typcn typcn-sort-alphabetically  
				dropdown-trigger" data-target="sort-list" href="#"></span>
			<span class="btn btn-flat typcn typcn-trash"
				:class="{disabled: shared || !(selected.length > 0)}"></span>
		</div>
		<ul class="dropdown-content" id="sort-list">
			<li><a>Nombre</a></li>
			<li><a>Fecha</a></li>
			<li><a>Tamaño</a></li>
		</ul>
		<!-- Tabla de informacion -->
		<data-table @change_dir="change_dir" :data="dirdata"></data-table>
	</div>
	<!-- Floating button -->
	<div class="fixed-action-btn">
		<a class="btn-floating waves-effect btn-large scale-transition"
		:class="{'scale-out': shared}">
			<span class="typcn typcn-plus"></span>
		</a>
		<ul v-show="!shared">
			<li>
				<a data-target="upload" class="btn-floating blue modal-trigger 
				typcn typcn-cloud-storage"></a>
			</li>
			<li>
				<a data-target="new_dir" class="btn-floating green modal-trigger 
				typcn typcn-folder-add"></a>
			</li>
		</ul>
	</div>
	<!-- Modals -->
	<new-dir :current_dir="currentDir" @update="get_dir"></new-dir>
	<upload></upload>
</div>

<script src="view/view-comp/upload-file.js"></script>
<script src="view/view-comp/create-dir.js"></script>
<script src="view/view-comp/data-table.js"></script>
<script src="view/view-comp/navbar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	M.AutoInit();
});

let app = new Vue({
	el: "#app",
	mounted() {
		this.get_dir();
	},
	data() {
		return {
			currentDir: undefined,
			dirsnames: [],
			dirs_sum: [],
			dirdata: [],
			shared: false,
			selected: []
		};
	},
	methods: {
		get_dir(dir) {
			axios({
				url: "get_dir",
				params: { dir: this.currentDir },
				method: "POST"
			})
				.then(response => {
					console.log(response.data);
					this.dirdata = response.data;
				})
				.catch(err => {
					console.log(err.response.data);
					M.toast({ html: "Error conectandose con el servidor" });
				});
		},
		change_dir(dir) {
			console.log(dir);
			this.dirdata = [];
			this.dirsnames.push(dir.dirname);
			this.dirs_sum.push(dir._sum);
			this.currentDir = dir._sum;
			this.get_dir();
		},
		updir() {
			this.dirdata = [];
			this.dirsnames.pop();
			this.dirs_sum.pop();
			this.currentDir = this.dirs_sum[this.dirs_sum.length - 1];
			this.get_dir();
		},
		change_tab() {
			this.shared = !this.shared;
		}
	}
});
</script>

<style>
.pding10, .files th, .files tr, .files td {
	padding: 10px;
}

.typcn {
	font-size: 20px;
	display: inline-block;
}

.nav-custom {
	margin-bottom: 1rem;
}
.upload_file {
	text-align: center;
	cursor: pointer;
	border: 2px dashed lightgray;
	padding: 100px 10px;
}
</style>
