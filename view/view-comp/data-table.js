Vue.component("data-table", {
	template: 
	`<table class="files card highlight responsive-table">
		<thead>
			<tr class="teal lighten-5">
				<th>Nombre</th>
				<th>Tamaño</th>
				<th>Fecha</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="dir in data[0]" @click="$emit('change_dir', dir)">
				<td><span class="typcn typcn-folder"></span> {{dir.dirname}}</td>
				<td></td>
				<td>{{parse_date(dir.creation_date)}}</td>
			</tr>
			<tr v-for="file in data[1]">
				<td>{{file}}</td>
			</tr>
			<tr v-show="data.lenght < 1">
				<td class="center-align grey-text" colspan="3">Carpeta vacia</td>
			</tr>
		</tbody>
	</table>`,
	props: ["data"],
	methods: {
		parse_date(date) {
			let fecha = new Date(date);
			return new Intl.DateTimeFormat().format(fecha);
		}
	}
});
