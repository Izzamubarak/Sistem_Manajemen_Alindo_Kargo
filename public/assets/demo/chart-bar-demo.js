// Bar Chart Example
var ctxBar = document.getElementById("myBarChart");
if (ctxBar) {
    var myBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ["Jakarta", "Bandung", "Surabaya", "Medan", "Makassar"],
            datasets: [{
                label: "Jumlah Paket",
                backgroundColor: "rgba(2,117,216,1)",
                borderColor: "rgba(2,117,216,1)",
                data: [100, 200, 150, 175, 130]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}
