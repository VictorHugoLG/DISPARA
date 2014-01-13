<div class="row">
    <div id="chart_column" class="span6"></div>
    <div id="chart_pizza" class="span6"></div>
</div>

<!-- Highcharts JS -->
<?php
    $total = $stats['agendados'] + $stats['enviados'] + $stats['lidos'] + $stats['rejeitados'] + $stats['falhas'];
    //echo $total;
    if ($total)
    {
        $chartsData =  "[['Agendados', ".(100*$stats['agendados']/$total).'],';
        $chartsData .= "['Enviados', ".(100*$stats['enviados']/$total).'],';
        $chartsData .= "['Lidos', ".(100*$stats['lidos']/$total)."],";
        $chartsData .= "['Rejeitados', ".(100*$stats['rejeitados']/$total).'],';
        $chartsData .= "['Falhas', ".(100*$stats['falhas']/$total).']]';
    }
    else
        $chartsData =  "[['Agendados', 0], ['Enviados', 0], ['Lidos', 0], ['Rejeitados', 0], ['Falhas', 0]]";
?>
<script type="text/javascript" src="<?php echo base_url('resources/js/highcharts/js/highcharts.js'); ?>"></script>
<script type="text/javascript">
    $(function () {
        var detail_lnk = "<?php echo site_url('mail/stats/'.$mail_id); ?>";
        $('#chart_column').highcharts({
            chart: {
                type: 'column'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Status dos emails'
            },
            xAxis: {
                categories: [ 'Emails' ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Quantidade (un.)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0"><a href="' + detail_lnk + '/{series.name}">{series.name}: </a></td>' +
                    '<td style="padding:0"><b>{point.y:.1f} un.</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                    name: 'Agendados',
                    data: [<?php echo $stats['agendados']; ?>]
                }, {
                    name: 'Enviados',
                    data: [<?php echo $stats['enviados']; ?>]
                }, {
                    name: 'Lidos',
                    data: [<?php echo $stats['lidos']; ?>]
                }, {
                    name: 'Rejeitados',
                    data: [<?php echo $stats['rejeitados']; ?>]
                }, {
                    name: 'Falhas',
                    data: [<?php echo $stats['falhas']; ?>]
                }, {
                    name: 'Total',
                    data: [<?php echo $total; ?>]
            }]
        });

        $('#chart_pizza').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Situação da fila de emails'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Fila de emails',
                data:  <?php echo $chartsData; ?>
            }]
        });
    });
</script>