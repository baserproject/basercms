document.addEventListener("DOMContentLoaded", function(event) {
	const vm = new Vue({
		el: '#app',
		data: {
			results: []
		},
		mounted() {
			axios.get("http://localhost/baser/api/users/index")
				.then(response => {this.results = response.data.users})
		}
	});
});