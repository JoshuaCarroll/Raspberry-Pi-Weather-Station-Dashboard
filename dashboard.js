var NumberOfSecondsBetweenReloadingData = 120;

// ============================

var boolShowMetricAndCelsiusMeasurements = true;
var boolShowPressureInMillibars = true;
var intTemperature = 0;
var intGroundTemperature = 0;
var intHumidity = 0;
var intPressure = 0;
var intPrCh1h = 0;
var intPrCh6h = 0;
var intPrCh12h = 0;
var intPrCh24h = 0;
var intPrCh48h = 0;
var intWindDirection = 0;

var chtPC1h = new chartSet();
var chtPC6h = new chartSet();
var chtPC12h = new chartSet();
var chtPC24h = new chartSet();
var chtPC48h = new chartSet();
var chtTemp = new chartSet();
var chtGroundTemp = new chartSet();
var chtHumidity = new chartSet();
var chtPressure = new chartSet();
var compass = null;

// Load the gauge from the Google Chart API. Details at
// https://developers.google.com/chart/interactive/docs/gallery/gauge
google.charts.load('current', {
    'packages': ['gauge']
});
google.charts.setOnLoadCallback(GoogleCharts_onload);

function GoogleCharts_onload() {
    if (window.console) console.log("Google charts loaded.");
    
    $.getScript("http://rawgit.com/JoshuaCarroll/Compass-/master/compass.js", Compass_onload);
}

function Compass_onload() {
    loadData(setupDataAndCharts);
}

function loadData(callback) {
    if (window.console) console.log("Calling loadData");
    var jsonPath = "current.php";
    $.getJSON(jsonPath, callback);
}

function setupDataAndCharts(result) {
    if (window.console) console.log("Calling setupDataAndCharts");
    setupData(result);
    setupCharts();
    setTimeout(loadData, NumberOfSecondsBetweenReloadingData * 1000, updateDataAndCharts);
}

function setupData(result) {
    if (window.console) console.log("Calling setupData");
    var obj = result.WeatherObservations.Observation1;
    intTemperature = obj.AMBIENT_TEMPERATURE;
    intGroundTemperature = obj.GROUND_TEMPERATURE;
    intHumidity = obj.HUMIDITY;
    intWindDirection = obj.WIND_DIRECTION;
    intPressure = obj.AIR_PRESSURE;
    intPrCh1h = obj.AIR_PRESSURE - result.WeatherObservations.Observation2.AIR_PRESSURE;
    intPrCh6h = obj.AIR_PRESSURE - result.WeatherObservations.Observation3.AIR_PRESSURE;
    intPrCh12h = obj.AIR_PRESSURE - result.WeatherObservations.Observation4.AIR_PRESSURE;
    intPrCh24h = obj.AIR_PRESSURE - result.WeatherObservations.Observation5.AIR_PRESSURE;
    intPrCh48h = obj.AIR_PRESSURE - result.WeatherObservations.Observation6.AIR_PRESSURE;
    boolShowMetricAndCelsiusMeasurements = result.Settings.showMetricAndCelsiusMeasurements;
    boolShowPressureInMillibars = result.Settings.showPressureInMillibars ;
    
    $("#rawData").empty();
    var rawList = $("#rawData").append("<ul>");
    [result.WeatherObservations.Observation1,result.DailyStats].forEach(function (obj) { 
        for (var property in obj) {
            if (obj.hasOwnProperty(property)) {
                rawList.append("<li>" + property + ": " + obj[property] + "</li>");
            }
        }
    });
    $("#rawData").append("</ul>");
}

function setupCharts() {
    if (window.console) console.log("Calling setupCharts");
    
    chtTemp.options = new chartOptions();
    if (boolShowMetricAndCelsiusMeasurements) {
        chtTemp.options.yellowFrom = 35;
        chtTemp.options.yellowTo = 42;
        chtTemp.options.redFrom = 42;
        chtTemp.options.redTo = 50;
        chtTemp.options.min = -30;
        chtTemp.options.max = 50;
    }
    drawChart(chtTemp, "chart_temp", "Temperature", intTemperature);
    
    chtGroundTemp.options = chtTemp.options;
    drawChart(chtGroundTemp, "chart_ground_temp", "Ground temp", intGroundTemperature);
    
    chtHumidity.options = chartOptions();
    chtHumidity.options.redFrom = 85;
    chtHumidity.options.redTo = 100;
    chtHumidity.options.yellowFrom = 75;
    chtHumidity.options.yellowTo = 85;
    chtHumidity.options.max = 100;
    drawChart(chtHumidity, "chart_hum", "Humidity", intHumidity);
    
    compass = new Compass("wind_dir");
    compass.animate(intWindDirection);

    chtPressure.options = chartOptions();
    if (boolShowPressureInMillibars) {
        chtPressure.options.redFrom = 960;
        chtPressure.options.redTo = 990;
        chtPressure.options.yellowFrom = 990;
        chtPressure.options.yellowTo = 1015;
        chtPressure.options.greenFrom = 1015;
        chtPressure.options.greenTo = 1060;
        chtPressure.options.max = 1060;
        chtPressure.options.min = 920;
    }
    else {
        chtPressure.options.redFrom = 28;
        chtPressure.options.redTo = 29.2;
        chtPressure.options.yellowFrom = 29.2;
        chtPressure.options.yellowTo = 29.9;
        chtPressure.options.greenFrom = 29.9;
        chtPressure.options.greenTo = 31.3;
        chtPressure.options.max = 31.3;
        chtPressure.options.min = 27.1;
    }
    drawChart(chtPressure, "chart_pressure", "Pressure", intPressure);

    var pressureChOps = chartOptions();
    pressureChOps.height = Math.round(pressureChOps.height * 0.66);
    pressureChOps.width = Math.round(pressureChOps.width * 0.66);
    pressureChOps.max = 10;
    pressureChOps.min = -10
    
    chtPC1h.options = pressureChOps;
    chtPC6h.options = pressureChOps;
    chtPC12h.options = pressureChOps;
    chtPC24h.options = pressureChOps;
    chtPC48h.options = pressureChOps;
    
    drawChart(chtPC1h, "chart_pressure_change1h", "Δ 1 hr", intPrCh1h);
    drawChart(chtPC6h, "chart_pressure_change6h", "Δ 6 hr", intPrCh6h);
    drawChart(chtPC12h, "chart_pressure_change12h", "Δ 12 hr", intPrCh12h);
    drawChart(chtPC24h, "chart_pressure_change24h", "Δ 24 hr", intPrCh24h);
    drawChart(chtPC48h, "chart_pressure_change48h", "Δ 48 hr", intPrCh48h);
}

function updateDataAndCharts(result) {
    if (window.console) console.log("Calling updateDataAndCharts");
    setupData(result);
    updateCharts();
    setTimeout(loadData, NumberOfSecondsBetweenReloadingData * 1000, updateDataAndCharts);
}

function updateCharts() {
    if (window.console) console.log("Calling updateCharts");
    chtTemp.update(intTemperature);
    chtGroundTemp.update(intGroundTemperature);
    chtHumidity.update(intHumidity);
    chtPressure.update(intPressure);
    chtPC1h.update(intPrCh1h);
    chtPC6h.update(intPrCh6h);
    chtPC12h.update(intPrCh12h);
    chtPC24h.update(intPrCh24h);
    chtPC48h.update(intPrCh48h);
    compass.animate(intWindDirection);
}

function drawChart(chartSetObj, strChartDiv, strLabel, intValue) {
    if (window.console) console.log("Calling drawChart(" + chartSetObj + ", " + strChartDiv + ", " + strLabel + ", " + intValue + ")");
    if (!chartSetObj.options) {
        chartSetObj.options = chartOptions();
    }
    
    chartSetObj.data = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        [strLabel, chartSetObj.options.min]
    ]);
    
    chartSetObj.chart = new google.visualization.Gauge(document.getElementById(strChartDiv));
    
    chartSetObj.chart.draw(chartSetObj.data, chartSetObj.options);
    
    setTimeout(function() {
        chartSetObj.data.setValue(0, 1, intValue);
        chartSetObj.chart.draw(chartSetObj.data, chartSetObj.options);
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

function chartSet() {
    this.chart = {};
    this.data = {};
    this.options = {};
    this.update = function (val) {
        this.data.setValue(0, 1, val);
        this.chart.draw(this.data, this.options);
    }
}