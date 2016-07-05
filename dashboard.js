var intTemperature = 0;
var intHumidity = 0;
var intPressure = 0;
var intPrCh1h = 0;
var intPrCh6h = 0;
var intPrCh12h = 0;
var intPrCh24h = 0;
var intPrCh48h = 0;

// https://developers.google.com/chart/interactive/docs/gallery/gauge#configuration-options
google.charts.load('current', {
    'packages': ['gauge']
});
google.charts.setOnLoadCallback(loadData);

function loadData() {
    var jsonPath = "current.php";
    
    $.getJSON(jsonPath, function (result) {
        var obj = result.WeatherObservations.Observation1;

        intTemperature = obj.GROUND_TEMPERATURE;
        intHumidity = obj.HUMIDITY;
        intPressure = obj.AIR_PRESSURE;
        intPrCh1h = obj.AIR_PRESSURE - result.WeatherObservations.Observation2.AIR_PRESSURE;
        intPrCh6h = obj.AIR_PRESSURE - result.WeatherObservations.Observation3.AIR_PRESSURE;
        intPrCh12h = obj.AIR_PRESSURE - result.WeatherObservations.Observation4.AIR_PRESSURE;
        intPrCh24h = obj.AIR_PRESSURE - result.WeatherObservations.Observation5.AIR_PRESSURE;
        intPrCh48h =  obj.AIR_PRESSURE - result.WeatherObservations.Observation6.AIR_PRESSURE;

        for (var property in obj) {
            if (obj.hasOwnProperty(property)) {
                $("#rawData").append(property + ": " + obj[property] + "<br>");
            }
        }

        drawCharts();
    });
}

function drawCharts() {
    drawChart("chart_temp", "Temperature", intTemperature, chartOptions());

    var humidityOps = chartOptions();
    humidityOps.redFrom = 85;
    humidityOps.redTo = 100;
    humidityOps.yellowFrom = 75;
    humidityOps.yellowTo = 85;
    humidityOps.max = 100;
    drawChart("chart_hum", "Humidity", intHumidity, humidityOps);
    
    var pressOps = chartOptions();
    pressOps.redFrom = 960;
    pressOps.redTo = 990        ;
    pressOps.yellowFrom = 990;
    pressOps.yellowTo = 1015;
    pressOps.greenFrom = 1015;
    pressOps.greenTo = 1040;
    pressOps.max = 1060;
    pressOps.min = 920
    drawChart("chart_pressure", "Pressure", intPressure, pressOps);
    
    var pressureChOps = chartOptions();
    pressureChOps.height = Math.round(pressureChOps.height * 0.66);
    pressureChOps.width = Math.round(pressureChOps.width * 0.66);
    pressureChOps.max = 10;
    pressureChOps.min = -10
    drawChart("chart_pressure_change1h", "Δ 1 hr", intPrCh1h, pressureChOps);
    drawChart("chart_pressure_change6h", "Δ 6 hr", intPrCh6h, pressureChOps);
    drawChart("chart_pressure_change12h", "Δ 12 hr", intPrCh12h, pressureChOps);
    drawChart("chart_pressure_change24h", "Δ 24 hr", intPrCh24h, pressureChOps);
    drawChart("chart_pressure_change48h", "Δ 48 hr", intPrCh48h, pressureChOps);
}

function drawChart(strChartDiv, strLabel, intValue, objOptions) {
    if (!objOptions) {
        objOptions = chartOptions();
    }

    var data = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        [strLabel, objOptions.min]
    ]);

    var chart = new google.visualization.Gauge(document.getElementById(strChartDiv));

    chart.draw(data, objOptions);

    setTimeout(function() {
        data.setValue(0, 1, intValue);
        chart.draw(data, objOptions);
    }, randomIntFromInterval(500, 2500));
}

function randomIntFromInterval(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

function chartOptions() {
    var defaultChartOptions = {
        width: 300,
        height: 200,
        redFrom: 108,
        redTo: 115,
        yellowFrom: 96,
        yellowTo: 108,
        minorTicks: 5,
        min: -20,
        max: 115
    };
    return defaultChartOptions;
}

window.setTimeout(function () { window.location.reload() }, 300000);