monthlySales();
function monthlySales()
{
    var sales = [];
      $.ajax({
          url: '/get-monthly-sales',
          type: 'GET',
          beforeSend: function(request) {
              return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
          },
          success: function(response) {
            sales.push(response);
            getMonthlySale(sales);
            
          }
      });
}

function getMonthlySale(sales){
    var dataPoints = [];
    
    var jsonData = sales[0];
      for (var i = 0; i < jsonData.length; i++) {
        dataPoints.push({
          y: Number(jsonData[i].y),
          label: jsonData[i].label,
        });
      }
      //  console.log(dataPoints);


var today = new Date();
var yyyy = today.getFullYear();
var options = {
    animationEnabled: true,  
    title:{
        text: "Monthly Sales - " + yyyy
    },
    axisX: {
        //title: "Months"
        labelAngle: -30,
    },
    axisY: {
        title: "Sales (in BDT)",
        prefix: "৳",
        includeZero: false
    },
    data: [{
        yValueFormatString: "৳#,###",
        xValueFormatString: "MMMM",
        type: "spline",
        dataPoints: dataPoints
        
    }]
};
$("#chartContainer").CanvasJSChart(options);

}