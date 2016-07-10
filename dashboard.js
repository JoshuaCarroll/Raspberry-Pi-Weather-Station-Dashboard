var NumberOfSecondsBetweenReloadingData = 120;

// ============================

var intTemperature = 0;
var intHumidity = 0;
var intPressure = 0;
var intPrCh1h = 0;
var intPrCh6h = 0;
var intPrCh12h = 0;
var intPrCh24h = 0;
var intPrCh48h = 0;

var chtPC1h = new chartSet();
var chtPC6h = new chartSet();
var chtPC12h = new chartSet();
var chtPC24h = new chartSet();
var chtPC48h = new chartSet();
var chtTemp = new chartSet();
var chtHumidity = new chartSet();
var chtPressure = new chartSet();

// Load the gauge from the Google Chart API. Details at
// https://developers.google.com/chart/interactive/docs/gallery/gauge#configuration-options
google.charts.load('current', {
    'packages': ['gauge']
});
google.charts.setOnLoadCallback(loadData(setupDataAndCharts));

function loadData(callback) {
    console.log("loadData");
    var jsonPath = "current.php";
    $.getJSON(jsonPath, callback);
}

function setupDataAndCharts(result) {
    console.log("setupDataAndCharts");
    setupData(result);
    setupCharts();
    setTimeout(loadData, NumberOfSecondsBetweenReloadingData * 1000, updateDataAndCharts);
}

function setupData(result) {
    console.log("setupData");
    var obj = result.WeatherObservations.Observation1;
    intTemperature = obj.AMBIENT_TEMPERATURE;
    intHumidity = obj.HUMIDITY;
    intPressure = obj.AIR_PRESSURE;
    intPrCh1h = obj.AIR_PRESSURE - result.WeatherObservations.Observation2.AIR_PRESSURE;
    intPrCh6h = obj.AIR_PRESSURE - result.WeatherObservations.Observation3.AIR_PRESSURE;
    intPrCh12h = obj.AIR_PRESSURE - result.WeatherObservations.Observation4.AIR_PRESSURE;
    intPrCh24h = obj.AIR_PRESSURE - result.WeatherObservations.Observation5.AIR_PRESSURE;
    intPrCh48h = obj.AIR_PRESSURE - result.WeatherObservations.Observation6.AIR_PRESSURE;
    
    $("#rawData").empty();
    $("#rawData").append("<ul>");
    [result.WeatherObservations.Observation1,result.DailyStats].forEach(function (obj) { 
        for (var property in obj) {
            if (obj.hasOwnProperty(property)) {
                $("#rawData").append("<li>" + property + ": " + obj[property] + "</li>");
            }
        }
    });
    $("#rawData").append("</ul>");
}

function setupCharts() {
    console.log("setupCharts");
    chtTemp.options = new chartOptions();
    drawChart(chtTemp, "chart_temp", "Temperature", intTemperature);

    chtHumidity.options = chartOptions();
    chtHumidity.options.redFrom = 85;
    chtHumidity.options.redTo = 100;
    chtHumidity.options.yellowFrom = 75;
    chtHumidity.options.yellowTo = 85;
    chtHumidity.options.max = 100;
    drawChart(chtHumidity, "chart_hum", "Humidity", intHumidity);

    chtPressure.options = chartOptions();
    chtPressure.options.redFrom = 960;
    chtPressure.options.redTo = 990;
    chtPressure.options.yellowFrom = 990;
    chtPressure.options.yellowTo = 1015;
    chtPressure.options.greenFrom = 1015;
    chtPressure.options.greenTo = 1040;
    chtPressure.options.max = 1060;
    chtPressure.options.min = 920
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
    console.log("updateDataAndCharts");
    setupData(result);
    updateCharts();
    setTimeout(loadData, NumberOfSecondsBetweenReloadingData * 1000, updateDataAndCharts);
}

function updateCharts() {
    console.log("updateCharts");
    chtTemp.update(intTemperature);
    chtHumidity.update(intHumidity);
    chtPressure.update(intPressure);
    chtPC1h.update(intPrCh1h);
    chtPC6h.update(intPrCh6h);
    chtPC12h.update(intPrCh12h);
    chtPC24h.update(intPrCh24h);
    chtPC48h.update(intPrCh48h);
}

function drawChart(chartSetObj, strChartDiv, strLabel, intValue) {
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