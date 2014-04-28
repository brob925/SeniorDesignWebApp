google.load('visualization', '1.1', {'packages':['annotationchart']}); // load google chart API
google.setOnLoadCallback(drawGraphic); // draw chart when page is loaded

// variables for keeping track of what options are currently selected
var nodeActive = 0;
var tempActive = true;
var locationActive = false;
var radActive = false;
var co2Active = false;
var graphicActive = true;
var tableActive = false;
var startDate = 'null';
var endDate = 'null';

// funciton to draw chart
var chart;
function drawGraphic() {
	clearChart();
	$('#chart').css('overflow-y', 'hidden');
	$('#chart').css('overflow-x', 'hidden'); // prevent scrolling
	
	if (locationActive) { // if user has location selected then draw a map not a chart
		drawMap();
		return;
	}
	
	// fit chart to height of the window
	var chartHeight = $(window).height();
	var chartHeight = chartHeight-50;
	$('#chart').css('height', chartHeight+'px');
	$('#dateSelectWrapper').css('display', 'none'); // hide bottom bar
	
	// pull info from database and draw chart
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			// process results from server
			var responseArray = xmlhttp.responseText.split(';');
			var dataType = responseArray[0];
			var datetimes = responseArray[1].split(',');
			var data = responseArray[2].split(',');
			
			// create new chart data
			var chartData = new google.visualization.DataTable();
			chartData.addColumn('datetime', 'Date');
			chartData.addColumn('number', dataType);
			// create rows from response from server
			var rows = [];
			for (var i=0; i<data.length; i++) {
				rows.push([new Date(datetimes[i]), Number(data[i])]);
			}
			chartData.addRows(rows);
			// create a new chart from google api
			chart = new google.visualization.AnnotationChart(document.getElementById('chart'));
			if (data.length > 25) { // default zoom to one day
				var options = {
					fill:20,
					zoomEndTime:new Date(datetimes[data.length-1]),
					zoomStartTime:new Date(datetimes[data.length-25]),
				};
			} else {
				var options = {
					fill:20,
				};
			}
			chart.draw(chartData, options);
		}
	}
	// which option is selected detirmines what data we what pulled from server
	var getURL = "";
	if (tempActive) {
		getURL = "getTemps.php";
	} else if (radActive) {
		getURL = "getRad.php";
	} else if (co2Active) {
		getURL = "getCo2.php";
	}
	xmlhttp.open("GET",getURL+"?nodeid="+nodeActive+'&startdate=null&enddate=null',true);
	xmlhttp.send();
}

// if the user has selected location then we have to draw a map
var map;
function drawMap() {
	// make map the height of the window
	var chartHeight = $(window).height();
	var chartHeight = chartHeight-90;
	$('#chart').css('height', chartHeight+'px');
	$('#dateSelectWrapper').css('display', 'block'); // display bottom bar
	
	// pull info from database and draw map
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			// process data recieved from server
			var responseArray = xmlhttp.responseText.split(';');
			var dataType = responseArray[0];
			var datetimes = responseArray[1].split(',');
			var latitudes = responseArray[2].split(',');
			var longitudes = responseArray[3].split(',');
			var maxIndex = datetimes.length-1;
			
			// set map options for new google map
			var mapOptions = {
				zoom: 14,
				center: new google.maps.LatLng(latitudes[maxIndex], longitudes[maxIndex])
			};
			map = new google.maps.Map(document.getElementById('chart'),
				mapOptions);
			
			// draw markers and info boxes for each data point
			for (var i=maxIndex; i>=0; i--) {
				createMarker(i, latitudes, longitudes, datetimes, map)
			}
		}
	}
	// send request to server (notice start and end date)
	xmlhttp.open("GET","getLocation.php?nodeid="+nodeActive+'&startdate='+startDate+'&enddate='+endDate,true);
	xmlhttp.send();
}

function drawTable() {
	clearChart();
	$('#chart').css('overflow-y', 'auto'); // no scrolling
	// set table to windows height
	var chartHeight = $(window).height();
	var chartHeight = chartHeight-90;
	$('#chart').css('height', chartHeight+'px');
	$('#dateSelectWrapper').css('display', 'block'); // display bottom bar
	
	// pull info from database and put it in a table
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			var tableHtml = '<table>';
			// process data from server
			var responseArray = xmlhttp.responseText.split(';');
			var dataType = responseArray[0];
			var datetimes = responseArray[1].split(',');
			var maxIndex = datetimes.length-1;
			
			if (locationActive) { // location data has two parameters
				tableHtml += '<tr><th>Latitude</th><th>Longitude</th><th>Date</th></tr>';
				var latitudes = responseArray[2].split(',');
				var longitudes = responseArray[3].split(',');
				// add rows to table
				for (var i=maxIndex; i>=0; i--) {
					var dateVar = new Date(datetimes[i]); // put date into nice format using moment API
					tableHtml += '<tr><td>'+latitudes[i]+'</td><td>'+longitudes[i]+'</td><td>'+moment(dateVar).format('h:mm A MMMM Do, YYYY')+'</td></tr>';
				}
			} else {
				tableHtml += '<tr><th>'+dataType+'</th><th>Date</th></tr>';
				var data = responseArray[2].split(',');
				for (var i=maxIndex; i>=0; i--) {
					var dateVar = new Date(datetimes[i]);
					tableHtml += '<tr><td>'+data[i]+'</td><td>'+moment(dateVar).format('h:mm A MMMM Do, YYYY')+'</td></tr>';
				}
			}
			tableHtml += '</table>';
			$('#chart').html(tableHtml); // draw table
			
		}
	}
	var getURL = "";
	if (tempActive) {
		getURL = "getTemps.php";
	} else if (locationActive) {
		getURL = "getLocation.php";
	}else if (radActive) {
		getURL = "getRad.php";
	} else if (co2Active) {
		getURL = "getCo2.php";
	}
	xmlhttp.open("GET",getURL+"?nodeid="+nodeActive+'&startdate='+startDate+'&enddate='+endDate,true);
	xmlhttp.send();
}

// set temperatute to active and then draw chart/table
function tempSelected() {
	tempActive = true;
	locationActive = false;
	radActive = false;
	co2Active = false;
	if (graphicActive) {
		drawGraphic();
	} else if (tableActive) {
		drawTable();
	}
}

// set location to active and then draw map/table
function locationSelected() {
	tempActive = false;
	locationActive = true;
	radActive = false;
	co2Active = false;
	if (graphicActive) {
		drawGraphic();
	} else if (tableActive) {
		drawTable();
	}
}

// set radiation to active and then draw chart/table
function radSelected() {
	tempActive = false;
	locationActive = false;
	radActive = true;
	co2Active = false;
	if (graphicActive) {
		drawGraphic();
	} else if (tableActive) {
		drawTable();
	}
}

// set carbon dioxide to active and then draw chart/table
function co2Selected() {
	tempActive = false;
	locationActive = false;
	radActive = false;
	co2Active = true;
	if (graphicActive) {
		drawGraphic();
	} else if (tableActive) {
		drawTable();
	}
}

function clearChart() {
	$('#chart').empty();
}

// function for putting markers on the map
function createMarker(i, lats, longs, dates, map_) {
	var dateVar = new Date(dates[i]);
	var marker = new google.maps.Marker({ // add marker
		position: new google.maps.LatLng(lats[i], longs[i]),
		map: map_,
		title: moment(dateVar).format('h:mm A MMMM Do, YYYY')
	});
	
	var contentString = 'Latitude: ' + lats[i] + '<br>' +
		'Longitude: ' + longs[i] + '<br>' +
		moment(dateVar).format('h:mm A MMMM Do, YYYY');

	var infowindow = new google.maps.InfoWindow({ // add info box popup
		content: contentString,
		maxWidth: 200
	});

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map_,marker);
	});
}

// funcitons to be run/set when the document is done loading
$(document).ready(function () {
	$('select').selectBoxIt(); // make nice selection boxs
	// set calendars
	$('#startDate').datepicker(); 
	$('#endDate').datepicker();
	$('button').button(); // make nice submit button
	
	// set default active node
	nodeActive = $($('.nodeSelect')[0]).val();
	
	// everytime someone chooses a new node draw graph/map/table
	$(document).on('change', '.nodeSelect', function() {
		nodeActive = $(this[this.selectedIndex]).val();
		if (graphicActive) {
			drawGraphic();
		} else if (tableActive) {
			drawTable();
		}
	});
	
	// everytime someone chooses a new sensor draw graph/map/table
	$(document).on('change', '.sensorSelect', function() {
		var selection = $(this[this.selectedIndex]).val();
		if (selection == 'temperature') {
			tempSelected();
		} else if (selection == 'location') {
			locationSelected();
		} else if (selection == 'radiation') {
			radSelected();
		} else if (selection == 'carbonDioxide') {
			co2Selected();
		}
	});
	
	// everytime someone chooses a display method, draw graph/map/table
	$(document).on('change', '.displaySelect', function() {
		var selection = $(this[this.selectedIndex]).val();
		if (selection == 'graphic') {
			graphicActive = true;
			tableActive = false;
			drawGraphic();
		} else if (selection == 'table') {
			graphicActive = false;
			tableActive = true;
			drawTable();
		}
	});
	
	// change start date variable when new start date is choosen
	$(document).on('change', '#startDate', function() {
		startDate = $('#startDate').datepicker('getDate');
	});
	
	// change end date variable when new start date is choosen
	$(document).on('change', '#endDate', function() {
		endDate = $('#endDate').datepicker('getDate');
	});
	
	// when submit button is cliced draw new table/map
	$('#submitDate').click(function() {
		if (graphicActive) {
			drawGraphic();
		} else if (tableActive) {
			drawTable();
		}
	});
});