//[Dashboard Javascript]

//Project:	Master Admin - Responsive Admin Template
//Primary use:   Used only for the main dashboard (index.html)


$(function () {

  'use strict';
	
		var customerData = {
		labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov" ],
		datasets: [{
			label: 'New Tickets',
			data: [21, 34, 44, 34, 26, 22, 19, 15],
			backgroundColor: [
				'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#ee1044', '#e4e4e4', '#e4e4e4', '#e4e4e4',
			],
			borderColor: [
				'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#ee1044', '#e4e4e4', '#e4e4e4', '#e4e4e4',
			],
			borderWidth: 1,
			fill: false
		}
		]
	};
	var customerOptions = {
		scales: {
			xAxes: [{
			barPercentage: 1,
			position: 'bottom',
			display: true,
			gridLines: {
				display: false,
				drawBorder: false,
			},
			ticks: {
				display: false, //this will remove only the label
				stepSize: 300,
			}
			}],
			yAxes: [{
				display: false,
				gridLines: {
					drawBorder: false,
					display: true,
					color: "#f0f3f6",
					borderDash: [8, 4],
				},
				ticks: {
					display: false,
					beginAtZero: true,
				},
			}]
		},
		legend: {
			display: false
		},
		tooltips: {
			enabled: false,
			backgroundColor: 'rgba(0, 0, 0, 1)',
		},
		plugins: {
			datalabels: {
				display: false,
				align: 'center',
				anchor: 'center'
			}
		}				
	};
	if ($("#customer").length) {
		var barChartCanvas = $("#customer").get(0).getContext("2d");
		// This will get the first returned node in the jQuery collection.
		if(screen.width>767) {
			var chartHeight = document.getElementById("customer");
			chartHeight.height = 60;
		}
		var barChart = new Chart(barChartCanvas, {
			type: 'bar',
			data: customerData,
			options: customerOptions
		});
	}
	
	
	
	
	
	var ordersData = {
			labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov" ],
			datasets: [{
				label: 'New Tickets',
				data: [19, 18, 17, 14, 43, 24, 18, 17],
				backgroundColor: [
					'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#1976D2', '#e4e4e4', '#e4e4e4', '#e4e4e4',
				],
				borderColor: [
					'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#1976D2', '#e4e4e4', '#e4e4e4', '#e4e4e4',
				],
				borderWidth: 1,
				fill: false
			}
			]
		};
		var ordersOptions = {
			scales: {
				xAxes: [{
				barPercentage: 1,
				position: 'bottom',
				display: true,
				gridLines: {
					display: false,
					drawBorder: false,
				},
				ticks: {
					display: false, //this will remove only the label
					stepSize: 300,
				}
				}],
				yAxes: [{
					display: false,
					gridLines: {
						drawBorder: false,
						display: true,
						color: "#f0f3f6",
						borderDash: [8, 4],
					},
					ticks: {
						display: false,
						beginAtZero: true,
					},
				}]
			},
			legend: {
				display: false
			},
			tooltips: {
				enabled: false,
				backgroundColor: 'rgba(0, 0, 0, 1)',
			},
			plugins: {
				datalabels: {
					display: false,
					align: 'center',
					anchor: 'center'
				}
			}				
		};
		if ($("#orders").length) {
			var barChartCanvas = $("#orders").get(0).getContext("2d");
			// This will get the first returned node in the jQuery collection.
			if(screen.width>767) {
				var chartHeight = document.getElementById("orders");
				chartHeight.height = 60;
			}
			var barChart = new Chart(barChartCanvas, {
				type: 'bar',
				data: ordersData,
				options: ordersOptions
			});
		}
		var growthData = {
			labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov" ],
			datasets: [{
				label: 'New Tickets',
				data: [13, 18, 31, 38, 48, 34, 25, 20],
				backgroundColor: [
					'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#303f9f', '#e4e4e4', '#e4e4e4', '#e4e4e4',
				],
				borderColor: [
					'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#303f9f', '#e4e4e4', '#e4e4e4', '#e4e4e4',
				],
				borderWidth: 1,
				fill: false
			}
			]
		};
		var growthOptions = {
			scales: {
				xAxes: [{
				barPercentage: 1,
				position: 'bottom',
				display: true,
				gridLines: {
					display: false,
					drawBorder: false,
				},
				ticks: {
					display: false, //this will remove only the label
					stepSize: 300,
				}
				}],
				yAxes: [{
					display: false,
					gridLines: {
						drawBorder: false,
						display: true,
						color: "#f0f3f6",
						borderDash: [8, 4],
					},
					ticks: {
						display: false,
						beginAtZero: true,
					},
				}]
			},
			legend: {
				display: false
			},
			tooltips: {
				enabled: false,
				backgroundColor: 'rgba(0, 0, 0, 1)',
			},
			plugins: {
				datalabels: {
					display: false,
					align: 'center',
					anchor: 'center'
				}
			}				
		};
		if ($("#growth").length) {
			var barChartCanvas = $("#growth").get(0).getContext("2d");
			// This will get the first returned node in the jQuery collection.
			if(screen.width>767) {
				var chartHeight = document.getElementById("growth");
				chartHeight.height = 60;
			}
			var barChart = new Chart(barChartCanvas, {
				type: 'bar',
				data: growthData,
				options: growthOptions
			});
		}
		var revenueData = {
			labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov" ],
			datasets: [{
				label: 'New Tickets',
				data: [13, 18, 31, 38, 33, 24, 19, 13],
				backgroundColor: [
					'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#ff8f00', '#e4e4e4', '#e4e4e4', '#e4e4e4',
				],
				borderColor: [
					'#e4e4e4', '#e4e4e4', '#e4e4e4', '#e4e4e4', '#ff8f00', '#e4e4e4', '#e4e4e4', '#e4e4e4',
				],
				borderWidth: 1,
				fill: false
			}
			]
		};
		var revenueOptions = {
			scales: {
				xAxes: [{
				barPercentage: 1,
				position: 'bottom',
				display: true,
				gridLines: {
					display: false,
					drawBorder: false,
				},
				ticks: {
					display: false, //this will remove only the label
					stepSize: 300,
				}
				}],
				yAxes: [{
					display: false,
					gridLines: {
						drawBorder: false,
						display: true,
						color: "#f0f3f6",
						borderDash: [8, 4],
					},
					ticks: {
						display: false,
						beginAtZero: true,
					},
				}]
			},
			legend: {
				display: false
			},
			tooltips: {
				enabled: false,
				backgroundColor: 'rgba(0, 0, 0, 1)',
			},
			plugins: {
				datalabels: {
					display: false,
					align: 'center',
					anchor: 'center'
				}
			}				
		};
		if ($("#revenue").length) {
			var barChartCanvas = $("#revenue").get(0).getContext("2d");
			// This will get the first returned node in the jQuery collection.
			if(screen.width>767) {
				var chartHeight = document.getElementById("revenue");
				chartHeight.height = 60;
			}
			var barChart = new Chart(barChartCanvas, {
				type: 'bar',
				data: revenueData,
				options: revenueOptions
			});
		}
	
	
	
	var piedata = [
      { label: "By Email", data: [[1,10]], color: '#40a2ed'},
      { label: "By Phone", data: [[1,30]], color: '#25b5b5'},
      { label: "On Site", data: [[1,90]], color: '#e84a50'},
      { label: "By Agent", data: [[1,70]], color: '#fad050'}
	 ];
    $.plot('#flotPie2', piedata, {
      series: {
        pie: {
          show: true,
          radius: 1,
          innerRadius: 0.5,
          label: {
            show: true,
            radius: 2/3,
            formatter: labelFormatter,
            threshold: 0.1
          },			
        },	  
      },
      grid: {
        hoverable: true,
        clickable: true
      }
    });

    function labelFormatter(label, series) {
		  return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
	  }
	
	
	      var options = {
            chart: {
                height: 285,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%'	
                },
            },
            dataLabels: {
                enabled: false
            },
			colors: ["#40a2ed", '#e84a50'],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [{
                name: 'Inquery',
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
            }, {
                name: 'Conform',
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
            }],
            xaxis: {
                categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            fill: {
                opacity: 1

            },
			  legend: {
				position: 'top',
				horizontalAlign: 'left'
			  },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        }

        var chart = new ApexCharts(
            document.querySelector("#bookingstatus"),
            options
        );

        chart.render();
	
	
	
	
	    var options = {
		  chart: {
			height: 285,
			type: 'line',
			zoom: {
			  enabled: false
			}
		  },
		  dataLabels: {
			enabled: false
		  },
	      colors: ["#40a2ed"],
		  stroke: {
			curve: 'straight'
		  },
		  series: [{
			name: "Revenue",
			data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
		  }],
		  grid: {
			row: {
			  colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
			  opacity: 0.5
			},
		  },
		  xaxis: {
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
		  }
		}

		var chart = new ApexCharts(
		  document.querySelector("#revenue2"),
		  options
		);

		chart.render();
      
	
}); // End of use strict
