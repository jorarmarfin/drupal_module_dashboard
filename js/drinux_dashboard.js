console.log('Version 1.0.10');
(function ($, Drupal, drupalSettings) {
    $(document).ready(function() {
        // Función para cargar y renderizar el gráfico.
        function cargarGrafico(sectorId, chartId) {
            $.ajax({
                url: '/dashboard/json/sector/' + sectorId, // Usa el sectorId en la URL.
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var actividades = response.map(function(item) {
                        return item.actividad;
                    });
                    var cantidades = response.map(function(item) {
                        return item.cantidad;
                    });

                    var options = {
                        chart: {
                            type: 'bar',
                            height: 'auto'
                        },
                        series: [{
                            name: 'Cantidad',
                            data: cantidades
                        }],
                        xaxis: {
                            categories: actividades
                        }
                    };

                    var chart = new ApexCharts(document.querySelector(chartId), options); // Usa chartId para seleccionar el div.
                    chart.render();
                },
                error: function(error) {
                    console.error("Error al obtener los datos para " + chartId + ": ", error);
                }
            });
        }
        function pieChart1(){
            $.ajax({
                url: '/dashboard/json/statistic/1', // URL de los datos
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Procesa la respuesta para adecuarla a los requerimientos de ApexCharts
                    let labels = response.map(function (item) {
                        return item.sexo || 'No especificado'; // Usar 'No especificado' si el sexo es null
                    });
                    let series = response.map(function (item) {
                        return parseInt(item.cantidad, 10); // Asegúrate de convertir la cantidad a un número
                    });

                    // Opciones para el gráfico de pastel
                    var options = {
                        chart: {
                            type: 'donut',
                            height: 'auto'
                        },
                        series: series,
                        labels: labels,
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };

                    // Inicializar el gráfico de pastel
                    let chart = new ApexCharts(document.querySelector("#pieChart1"), options);

                    // Renderizar el gráfico
                    chart.render();
                },
                error: function(error) {
                    console.error("Error al obtener los datos: ", error);
                }
            });

        }
        function barChartGroupBySexo(){
            $.ajax({
                url: '/dashboard/json/statistic/2', // URL de los datos
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let labels = response.map(function (item) {
                        return item.grupo || 'No especificado'; // Usar 'No especificado' si el sexo es null
                    });
                    var options = {
                        series: [
                            {
                            data: [44, 55, 41, 64, 22, 43, 21]
                        }, {
                            data: [53, 32, 33, 52, 13, 44, 32]
                        }
                            , {
                                data: [53, 32, 33, 52, 13, 44, 32]
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 430
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                dataLabels: {
                                    position: 'top',
                                },
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetX: -6,
                            style: {
                                fontSize: '12px',
                                colors: ['#fff']
                            }
                        },
                        stroke: {
                            show: true,
                            width: 1,
                            colors: ['#fff']
                        },
                        tooltip: {
                            shared: true,
                            intersect: false
                        },
                        xaxis: {
                            categories: labels,
                        },
                    };

                    var chart = new ApexCharts(document.querySelector("#barChartGroup1"), options);
                    chart.render();
                },
                error: function(error) {
                    console.error("Error al obtener los datos: ", error);
                }
            });

        }

        // Llamadas a la función para cada combinación de sector y div.
        cargarGrafico(641, '#chart1');
        cargarGrafico(642, '#chart2');
        cargarGrafico(643, '#chart3');
        pieChart1();
        barChartGroupBySexo();

    });
})(jQuery, Drupal, drupalSettings);
