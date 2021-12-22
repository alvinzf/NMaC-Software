@extends('layouts.app')

@section('content')
<script>
    let url = "{{ route('homeChart') }}";


    Highcharts.getJSON(
        url,

        function(data) {
            // console.log(data)

            const _date = data.map(d => (parseInt(d.ts) * 1000));
            // console.log(_date);
            const value = data.map(d => (parseInt(d.value)));
            // console.log(value);
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
                    text: 'Kebisingan Hari ini'
                },
                subtitle: {
                    text: document.ontouchstart === undefined ?
                        'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
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
        <p class="highcharts-description">
            Highcharts has extensive support for time series, and will adapt
            intelligently to the input data. Click and drag in the chart to zoom in
            and inspect the data.
        </p>
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
    <table class="table" id="tableReadings">

        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Sensor</th>
                <th scope="col">Value</th>

                <th scope="col">Classification</th>
                <th scope="col">Timestamp</th>
            </tr>
        </thead>
        @foreach ($data as $d)
        @php
        $classification = $d->classification;
        if (strlen($classification) > 0) {
        $classification = explode("#", $classification);
        if (count($classification) > 6) {
        $fallingObj = round(($classification[0] + $classification[5]) / 2 * 100);
        $horn = round(($classification[1] + $classification[6]) / 2 * 100);
        $human = round(($classification[2] + $classification[7]) / 2 * 100);
        $phone = round(($classification[3] + $classification[8]) / 2 * 100);
        $siren = round(($classification[4] + $classification[9]) / 2 * 100);
        } else if (count($classification) > 11) {

        $fallingObj = round(($classification[0] + $classification[5]) / 2 * 100);
        $horn = round(($classification[1] + $classification[6]) / 2 * 100);
        $human = round(($classification[2] + $classification[7]) / 2 * 100);
        $phone = round(($classification[3] + $classification[8]) / 2 * 100);
        $siren = round(($classification[4] + $classification[9]) / 2 * 100);
        } else {
        $fallingObj = round($classification[0] * 100);
        $horn = round($classification[1] * 100);
        $human = round($classification[2] * 100);
        $phone = round($classification[3] * 100);
        $siren = round($classification[4] * 100);
        }

        if ($fallingObj > 70) {
        $result = "Falling Obj " . $fallingObj . "%";
        } else if ($horn > 70) {
        $result = "Horn " . $horn . "%";
        } else if ($human > 70) {
        $result = "Human " . $human . "%";
        } else if ($phone > 70) {
        $result = "Phone " . $phone . "%";
        } else if ($siren > 70) {
        $result = "Siren " . $siren . "%";
        } else {
        $result = "Not Sure";
        }
        } else {
        $result = "-";
        }
        @endphp
        <tbody>
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->sensor}}</td>
                <td>{{ $d->value }}</td>
                <td>{{ $result}}</td>
                <td>{{ $d->created_at }}</td>

            </tr>
        </tbody>

        @endforeach
</div>
@endsection