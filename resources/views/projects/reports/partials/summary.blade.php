@if(count($findings))

    @if($severity == 'Info')
        <div class="bg-blue-100 border-t-4 border-blue-500 rounded-b text-blue-900 px-4 py-3" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">The project has only Informational severity findings.</p>
                    <p class="text-sm">These are very low risk, potential issues that, by itself, usually do not require immediate action.</p>
                </div>
            </div>
        </div>
    @elseif($severity == 'Low')
        <div class="bg-yellow-100 border-t-4 border-yellow-500 rounded-b text-yellow-900 px-4 py-3" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">The project has Low or Informational severity findings.</p>
                    <p class="text-sm">Low severity findings are not threat by themselves, but might be in combination with others issues. Please consider fixing those at the earliest convenience.</p>
                </div>
            </div>
        </div>
    @elseif($severity == 'Medium')
        <div class="bg-orange-100 border-t-4 border-orange-500 rounded-b text-orange-900 px-4 py-3" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-orange-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">The project has Medium severity findings.</p>
                    <p class="text-sm">There are important issues to fix or actions to be performed. The Medium severity findings must be fixed before the project gets deployed to production or as soon as possible if the go-live already occurred.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M17 16a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4.01V4a1 1 0 0 1 1-1 1 1 0 0 1 1 1v6h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v8h1V1a1 1 0 1 1 2 0v9h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v13h1V9a1 1 0 0 1 1-1h1v8z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">The project contains High / Critical severity issues.</p>
                    <p class="text-sm">The project is not fit for production. The issues must be addressed immediately and before making the project live.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex mb-2">
        <div class="w-full">
            <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Statistics</p>
        </div>
    </div>
    @include('projects.reports.partials.stats', ['stats' => $stats])
@else
    <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3" role="alert">
        <div class="flex">
            <div class="py-1">
                <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold">No issues found!</p>
                <p class="text-sm">We couldn't find any single issue with this project. Good job!</p>
            </div>
        </div>
    </div>
@endif