<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i" rel="stylesheet">
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.2/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-colorschemes"></script>
    <style>
        body {
            width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .badge-default {
            background-color: #c0c0c0;
            color: #fff;
            white-space: nowrap;
        }
        .badge-success {
            background-color: #32cd32;
        }
        .badge-info {
            background-color: #87ceeb;
        }
        .badge-warning {
            background-color: #ffd700;
        }
        .badge-warning-dark {
            background-color: #ffa500;
            color: #fff;
        }
        .badge-danger {
            background-color: #ff6347;
        }
        .badge-danger-dark {
            background-color: #f00;
            color: #fff;
        }
        a {
            text-decoration: underline;
            color: blue;
        }
        .no-break {
            page-break-inside: avoid;
        }
        .break-before {
            page-break-before: always;
        }
    </style>
  </head>
  <body style="-webkit-print-color-adjust:exact;">

    <div class="p-5">
        <div class="flex">
            @include('projects.reports.partials.header', ['project' => $project, 'severity' => $severity])
        </div>

        <div class="flex">
            <div class="w-full">
                <p class="pt-4 pb-2 text-2xl">Summary</p>
                @include('projects.reports.partials.summary', ['findings' => $findings, 'stats' => $stats])
            </div>
        </div>

        <div class="flex">
            <div class="w-full">
                <p class="pt-4 text-2xl">Scope</p>
                @include('projects.reports.partials.scope', ['scope' => $scope])
            </div>
        </div>

        @if(count($findings))
            <div class="flex break-before">
                <div class="w-full">
                    <p class="pt-4 py-2 text-2xl">Findings</p>
                    @each('projects.reports.partials.finding', $findings, 'finding')
                </div>
            </div>
        @endif
    </div>

  </body>
</html>