const labels = Object.keys(concertsPerYear);
const dataValues = Object.values(concertsPerYears);

const data = {
    labels: labels,
    datasets: [{
        label: 'Concerts Attended',
        data: dataValues,
        backgroundColor: [
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 99, 132, 0.2)'
        ],
        borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 99, 132, 1)'
        ],
        pointBackgroundColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 99, 132, 1)'
        ],
        borderWidth: 1,
        pointRadius: 4,
        pointHoverRadius: 8,
        fill: true
    }]
};

const config = {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return `${tooltipItem.raw} concerts`;
                    }
                }
            }
        },
        scales: {
          y: {
            ticks: {
              stepSize: 1,          
              suggestedMin: '0',      
              suggestedMax: 'max-int-value'       
            }
          }
        }
    }
};

const ctx = document.getElementById('concertsChart').getContext('2d');
new Chart(ctx, config);