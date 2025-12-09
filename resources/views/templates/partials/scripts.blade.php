	<script src="{{ asset('master/js/jquery-1.11.1.min.js') }}"></script>
	<script src="{{ asset('master/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('master/js/chart.min.js') }}"></script>
	<script src="{{ asset('master/js/chart-data.js') }}"></script>
	<script src="{{ asset('master/js/easypiechart.js') }}"></script>
	<script src="{{ asset('master/js/easypiechart-data.js') }}"></script>
	<script src="{{ asset('master/js/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset('master/js/custom.js') }}"></script>
	 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="{{ asset('master/js/jquery-1.11.1.min.js') }}"></script>
	<script src="{{ asset('master/js/bootstrap.min.js') }}"></script>

	<!-- yajra tabel --> 
	<script type="text/javascript" src="{{ asset('DataTables/datatables.min.js') }}"></script>
	@stack('scripts')

	<script>		
		window.onload = function () {
	var chart1 = document.getElementById("line-chart").getContext("2d");
	window.myLine = new Chart(chart1).Line(lineChartData, {
	responsive: true,
	scaleLineColor: "rgba(0,0,0,.2)",
	scaleGridLineColor: "rgba(0,0,0,.05)",
	scaleFontColor: "#c5c7cc"
	});
};
	</script>

		