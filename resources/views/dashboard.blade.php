@extends('layouts.app')

@section('content')
<script type="text/javascript">
    generate: (state, counter) => {
        state.rows = state.rows || [];
        state.rows.push([startDate.toISOString(), Math.random() * 10]);
        trimArray(state.rows);
        return JSON.stringify(state.rows);

    }
    let chart; // global
    async function requestData() {
        const result = await fetch('https://demo-live-data.highcharts.com/time-rows.json');
        const res = await fetch("{{ route('homeChart') }}")

        if (result.ok) {
            const data = await result.json();
            const dat = await res.json();
            const _date = dat.map(d => (parseInt(d.ts) * 1000));

            const va = dat.map(d => (parseInt(d.value)));

            var re = [];
            for (var i = 0; i < _date.length; i++) {
                re.push([_date[i], va[i]]);
            }

            const [_dat, _val] = re[0];
            const [date, value] = data[0];
            const point = [new Date(date).getTime(), value * 10];
            const po = [_dat, _val];
            // console.log(point);
            console.log(po);
            const series = chart.series[0],
                shift = series.data.length > 300; // shift if the series is longer than 20
            // add the point
            chart.series[0].addPoint(po, true, shift);
            // call it again after one second
            setTimeout(requestData, 1000);
        }
    }
    window.addEventListener('load', function() {
        const timezone = new Date().getTimezoneOffset()

        Highcharts.setOptions({
            global: {
                timezoneOffset: timezone
            }
        });
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                defaultSeriesType: 'spline',
                events: {
                    load: requestData
                }
            },
            title: {
                text: 'Monitoring Langsung'
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 150,
                maxZoom: 20 * 1000
            },
            yAxis: {
                minPadding: 0.2,
                maxPadding: 0.2,
                title: {
                    text: 'Tingkat Kebisingan (dB)',
                    margin: 80
                }
            },
            series: [{
                name: 'Tingkat Kebisingan',
                data: []
            }]
        });
    });
</script>


<div class="mx-auto w-75 h-100">
    <figure class="highcharts-figure">
        <div id="container"></div>

    </figure>
    <section class="content">
        <div class="center">
            <div class="d-flex justify-content-around">
                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="bg-white rounded-lg p-5 shadow">
                        <h2 class="h6 font-weight-bold text-center mb-4">Maksimal</h2>
                        @foreach ($max as $m)
                        <div class="xprogress mx-auto" data-value='{{$m->maxVal}}'>

                            <span class="xprogress-left">
                                <span class="xprogress-bar border-danger"></span>
                            </span>
                            <span class="xprogress-right">
                                <span class="xprogress-bar border-danger"></span>
                            </span>
                            <div class="xprogress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="h2 font-weight-bold">{{$m->maxVal}}<sup class="small">dB</sup></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="row text-center mt-4">
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="bg-white rounded-lg p-5 shadow">
                        <h2 class="h6 font-weight-bold text-center mb-4">Rata-Rata</h2>
                        @foreach ($avg as $a)
                        <div class="xprogress mx-auto" data-value='{{$a->avg}}'>

                            <span class="xprogress-left">
                                <span class="xprogress-bar border-warning"></span>
                            </span>
                            <span class="xprogress-right">
                                <span class="xprogress-bar border-warning"></span>
                            </span>
                            <div class="xprogress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="h2 font-weight-bold">{{$a->avg}}<sup class="small">dB</sup></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="row text-center mt-4">
                        </div>
                    </div>
                </div>

            </div>
    </section>
    <!-- <h2> Sensor Readings </h2>
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

        @endforeach -->
</div>
@endsection