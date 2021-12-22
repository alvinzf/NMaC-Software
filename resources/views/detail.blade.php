@extends('layouts.app')

@section('content')
<script>
    let url = "{{ route('api-detail') }}";


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
                    text: 'Grafik Pemantauan Kebisingan'
                },
                subtitle: {
                    text: document.ontouchstart === undefined ?
                        'Klik dan tarik pada grafik untuk zoom' : 'Pinch the chart to zoom in'
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
                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="bg-white rounded-lg p-5 shadow">
                        <h2 class="h6 font-weight-bold text-center mb-4">Tahun Tertinggi</h2>
                        @foreach ($most as $most)
                        <div class="yprogress mx-auto">
                            <div class="xprogress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="h2 font-weight-bold"> {{$most->no_year}}</div>
                            </div>
                        </div>
                        @endforeach
                        <div class="row text-center mt-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <p style="font-size: 25px;">Hasil Pemantauan</p>
    <table class="table table-bordered data-table">
        <thead>
            <tr>

                <th scope="col" style="width: 80%">Tahun</th>

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
            paging: true,
            info: true,
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            columnDefs: [{
                    "targets": [0, 1],
                    "className": "text-center",
                },

            ],
            language: {
                paginate: {
                    next: '<span class="fas fa-arrow-right">&#8594;</span>', // or '→'
                    previous: '<span class="fas fa-arrow-left">&#8592;</span>' // or '←' 
                },
                lengthMenu: "Tampilkan _MENU_ Item",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ Item",
                search: "Cari:",
            },


            ajax: "{{ route('detail') }}",
            columns: [{
                    data: 'no_year',
                    name: 'no_year'
                },


                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },


            ]
        });

    });
</script>
@endsection