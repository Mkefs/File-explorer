Vue.component("navbar", {
	template: 
	`<nav class="nav-extended teal custom-nav">
		<div class="nav-wrapper">
			<a href="#" class="hide-on-small-only brand-logo center">Explorador</a>
			<ul id="nav-mobile" class="right">
				<a>Cerrar sesion</a>
			</ul>
		</div>
		<div class="nav-content">
			<ul class="center-align tabs tabs-transparent">
				<li class="tab">
					<a :class="{active: !FoS}" @click="change_status">Archivos</a>
				</li>
				<li class="tab">
					<a :class="{active: FoS}" @click="change_status">Compartidos</a>
				</li>
			</ul>
		</div>
	</nav>`,
	data() {
		return {
			FoS: false
		};
	},
	methods: {
		change_status(el) {
			let active = el.target.classList.contains("active");
			this.FoS = active? this.FoS : !this.FoS;
			if(!active)
				this.$emit("change");
		}
	}
});
