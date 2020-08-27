@extends('nova::auth.layout')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js" crossorigin="anonymous"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet" type="text/css" crossorigin="anonymous"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

<style type="text/css">
    #mynetwork {
      width: 1980px;
      height: 1080px;
    }
  </style>

<script type="text/javascript">
    function draw() {
        var len = undefined;

        var nodes = [
            @foreach($data['nodes'] as $id => $item)
                {
                    id: '{{ $id }}',
                    label: "{!! $item['label'] !!}",
                    group: '{{ $item['group'] }}',
                    icon: {
                        color: '{{ $item['color'] }}',
                    }
                },
            @endforeach
        ];
        var edges = [
            @foreach($data['edges'] as $item)
                {from: '{{ $item['from'] }}', to: '{{ $item['to'] }}'},
            @endforeach
        ]

        // create a network
        var container = document.getElementById('mynetwork');
        var data = {
            nodes: nodes,
            edges: edges
        };

        var options = {
            groups: {
              dns: {
               shape: 'icon',
               icon: {
                 face: 'FontAwesome',
                 code: '\uf0e8'
               }
              },
              cert: {
                shape: 'icon',
                icon: {
                  face: 'FontAwesome',
                  code: '\uf0a3'
                }
              },
              port: {
                shape: 'icon',
                icon: {
                  face: 'FontAwesome',
                  code: '\uf0b0'
                }
              },
              domain: {
                shape: 'icon',
                icon: {
                  face: 'FontAwesome',
                  code: '\uf0ac'
                }
              },
              host: {
                shape: 'icon',
                icon: {
                  face: 'FontAwesome',
                  code: '\uf233'
                }
              },
              repo: {
                shape: 'icon',
                icon: {
                  face: 'FontAwesome',
                  code: '\uf121'
                }
              },
              web: {
                shape: 'icon',
                icon: {
                  face: 'FontAwesome',
                  code: '\uf0c1'
                }
              },
            },
            nodes: {
                shape: 'dot',
                size: 30,
                font: {
                    size: 16,
                    color: '#000'
                },
                borderWidth: 2
            },
            edges: {
                width: 2,
                length: 200,
            }
        };
        network = new vis.Network(container, data, options);
    }
</script>

<body onload="draw()">
    <div id="mynetwork"></div>
</body>

@endsection
