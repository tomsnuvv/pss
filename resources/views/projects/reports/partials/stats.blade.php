<div class="flex mb-2">
    <div class="w-1/2">
        <div class="text-center">
            <p class="text-xs">Findings By Severity</p>
        </div>
        <canvas id="findings-by-severity"></canvas>
        <script>
            new Chart(document.getElementById("findings-by-severity"),
            {
               type: "doughnut",
               options: {
                   animation: false
               },
               data: {
                  labels: [
                     @foreach($stats['severities'] as $name => $count)
                        "{{ $name }}",
                     @endforeach
                  ],
                  datasets: [
                     {
                        data:[
                            @foreach($stats['severities'] as $count)
                               {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: [
                           "#87ceeb",
                           "#ffd700",
                           "#ffa500",
                           "#ff6347",
                           "#f00",
                        ]
                     }
                  ]
              }
            });
        </script>
    </div>
    <div class="w-1/2 pr-5">
        <div class="text-center">
            <p class="text-xs">Findings By Category</p>
        </div>
        <canvas id="findings-by-category"></canvas>
        <script>
            new Chart(document.getElementById("findings-by-category"), {
                type: "bar",
                options: {
                    animation: false,
                    ticks: {
                        precision: 0
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 5
                            }
                        }]
                    }
                },
                data: {
                    labels: [
                    @foreach($stats['types'] as $name => $count)
                        "{{ $name }}",
                        @endforeach
                    ],
                    datasets: [{
                        data:[
                            @foreach($stats['types'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: Chart.colorschemes.brewer['SetThree12']
                    }]
                }
            });
        </script>
    </div>
</div>