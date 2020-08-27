<div class="rounded-lg border border-60 pt-2 pl-2 pr-2 mb-4 no-break">

    <div class="flex mb-2">

        <div class="w-2/3">
            <p class="text-xl text-gray-500">#{{ $finding->id }}</p>
        </div>

        <div class="w-1/3">
            <div class="float-right">
                <span class="rounded-full uppercase p-2 text-xs font-bold badge-default
                @if(!$finding->severity)
                @elseif($finding->severity->name == 'Info') badge-info
                @elseif($finding->severity->name == 'Low') badge-warning
                @elseif($finding->severity->name == 'Medium') badge-warning-dark
                @elseif($finding->severity->name == 'High') badge-danger
                @elseif($finding->severity->name == 'Critical') badge-danger-dark
                @endif">
                    {{ $finding->severity ? $finding->severity->name : 'Unknwon' }}
                </span>
            </div>
        </div>
    </div>

    <div class="flex mb-4">
        <div class="w-full">

            <p class="text-s inline">{{ $finding->title }}</p>

            @if($finding->type && $finding->type->name !== 'Unknown')
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Type</p>
                <p class="text-xs">{!! $finding->type->name !!}</p>
            @endif

            @if($finding->target)
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Target</p>
                <p class="text-xs">
                    @if(strstr(get_class($finding->target), 'Website'))
                        Website: <a href="{{ $finding->target->url }}" target="_blank">{{ $finding->target->url }}</a>
                    @elseif(strstr(get_class($finding->target), 'Domain'))
                        Domain: {{ $finding->target->name }}
                    @elseif(strstr(get_class($finding->target), 'Repository'))
                        Repository: {{ $finding->target->name }}
                    @elseif(strstr(get_class($finding->target), 'Host'))
                        Host: {{ $finding->target->name ?: $finding->target->host }}
                    @endif
                </p>
                @if($finding->childTarget)
                    <p class="text-xs">
                        @if(strstr(get_class($finding->childTarget), 'Port'))
                            Port: {{ $finding->childTarget->port }}
                        @elseif(strstr(get_class($finding->childTarget), 'Certificate'))
                            Certificate: {{ $finding->childTarget->name }}
                        @endif
                    </p>
                @endif
            @endif


            @if($finding->vulnerability && $finding->vulnerability->description)
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Description</p>
                <p class="text-xs">{{ $finding->vulnerability->description }}</p>
            @elseif($finding->type && $finding->type->description)
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Description</p>
                <p class="text-xs">{{ $finding->type->description }}</p>
            @endif

            @if($finding->details)
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Details
                <div class="text-xs">{!! Illuminate\Mail\Markdown::parse($finding->details) !!}</div>
            @endif

            @if($finding->vulnerability)

                @if($finding->vulnerability->proof_of_concept)
                    <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Proof of concept
                    <p class="text-xs">{{ $finding->vulnerability->proof_of_concept }}
                @endif

                @if($finding->vulnerability->vulnerable_code)
                    <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Vulnerable code
                    <p class="text-xs">{{ $finding->vulnerability->vulnerable_code }}
                @endif

                @if($finding->vulnerability->details->count())
                    <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">References
                    <ul class="list-inside list-disc text-xs">
                        @foreach($finding->vulnerability->details as $detail)
                            <li>
                                @include('vulnerabilities.partials.detail', ['detail' => $detail])
                            </li>
                        @endforeach
                    </ul>
                @endif

            @endif

            @if($finding->type && $finding->type->remediation)
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Remediation</p>
                <div class="text-xs">{!! Illuminate\Mail\Markdown::parse($finding->type->remediation) !!}</div>
            @endif

        </div>
    </div>
</div>