
<?php
(!defined('RUN_INIT')) && die('No Init');

$this->pimp();
$this->pimp('/common/echarts.min.js','vendui');
?>

<p>ECharts</p>

<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="main" style="width:400px;height:300px;"></div>
<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));
    // 指定图表的配置项和数据
    var option = {
        title: {
            text: '2016-某店',
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:['平均价(万)','销量(亿件)']
        },
        calculable: true,
        xAxis: {
            type: 'category',
            splitLine: {
                show: false
            },
            boundaryGap: false,
            data: ["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"]
            
        },
        yAxis: {},
        series: [{
            name: '平均价(万)',
            type: 'line', // line,bar
            data: [115.5, 212.0, 413.6, 510.5, 316.9, 620.60]
        },{
            name: '销量(亿件)',
            type: 'line', // line,bar
            data: [188.5, 99.0, 113.6, 110.5, 216.9, 320.60]
        }]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>
