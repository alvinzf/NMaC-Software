@extends('layouts.app')

@section('content')
<script>
    let url = "{{ route('homeChart') }}";


    Highcharts.getJSON(
        url,

        function(data) {

            const _date = data.map(d => (parseInt(d.ts) * 1000));
            const value = data.map(d => (parseInt(d.value)));
            var result = [];
            for (var i = 0; i < _date.length; i++) {
                result.push([_date[i], value[i]]);
            }
            // console.log(result);
            const timezone = new Date().getTimezoneOffset()

            Highcharts.setOptions({
                global: {
                    timezoneOffset: timezone
                }
            });
            Highcharts.chart('container', {
                chart: {
                    zoomType: 'x'
                },
                title: {
                    text: 'Kebisingan Minggu ini'
                },
                subtitle: {
                    text: document.ontouchstart === undefined ?
                        ' Klik dan tarik pada grafik untuk zoom' : 'Pinch the chart to zoom in'
                },
                xAxis: {
                    type: 'datetime'
                },
                yAxis: {
                    title: {
                        text: ' Tingakat Kebisingan (dB)'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    area: {
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, Highcharts.getOptions().colors[0]],
                                [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    }
                },

                series: [{
                    type: 'area',
                    name: 'Kebisingan',
                    data: result
                }]
            });

        }
    );
</script>

<div class="mx-auto w-75">
    <figure class="highcharts-figure">
        <div id="container"></div>

    </figure>
    <section class="content">
        <div class="center">
            <div class=" d-flex flex-row">

                <div class="p-2">
                    <div class="mask">
                        <div class="semi-circle"></div>
                        <div class="semi-circle--mask"></div>
                    </div>
                    @foreach ($max as $m)
                    <p style="font-size: 30px;" class="cent" id="max-val">Max : {{$m->value}}</p>
                    @endforeach
                    <table cellspacing="5" cellpadding="5" class="cent">
                        <tr>
                            <th colspan="3">Today</th>
                        </tr>

                    </table>
                </div>
                <div class="p-2">
                    <div class="mask">
                        <div class="semi-circle"></div>
                        <div class="semi-circle--mask"></div>
                    </div>
                    @foreach ($avg as $a)
                    <p style="font-size: 30px;" class="cent" id="temp">Average : {{$a->avg}}</p>
                    @endforeach
                    <table cellspacing="5" cellpadding="5" class="cent">
                        <tr>
                            <th colspan="3">Today</th>
                        </tr>

                    </table>
                </div>



            </div>
        </div>
    </section>
    <h2> Sensor Readings </h2>
    <table class="table table-bordered data-table">
        <thead>
            <tr>

                <th>Week</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>
<script type="text/javascript">
    $(function() {

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('week') }}",
            columns: [{
                    data: 'no_week',
                    name: 'no_week'
                },
                {
                    data: 'sdate',
                    name: 'sdate'
                }, {
                    data: 'edate',
                    name: 'edate'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'action',
                    name: 'action'
                },


            ]
        });

    });
</script>
@endsection