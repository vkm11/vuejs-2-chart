
<script>
  import { Line, mixins } from 'vue-chartjs'
  const { reactiveProp } = mixins
  export default {
    extends: Line,
    mixins: [reactiveProp],
    props:['chartData'],
    data () {
      return {
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              },
              gridLines: {
                display: true
              }
            }],
            xAxes: [ {
              gridLines: {
                display: false
              }
            }]
          },
          legend: {
            display: true
          },
          responsive: true,
          maintainAspectRatio: false
        }
      }
    },
    mounted () {
      console.log(this.chartData);
      this.renderChart(this.chartData, this.options)
      
    },
    
  }
</script>
                
