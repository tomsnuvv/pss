<div class="flex pl-2">
    @if(isset($scope['websites']) || isset($scope['repositories']))
        <div class="w-1/2 float-left">
            @if(isset($scope['websites']))
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Websites</p>
                <ul class="list-inside list-disc text-xs">
                    @foreach($scope['websites'] as $website)
                        <li>{{ $website }}</li>
                    @endforeach
                </ul>
            @endif
            @if(isset($scope['repositories']))
                <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Repositories</p>
                <ul class="list-inside list-disc text-xs">
                    @foreach($scope['repositories'] as $repository)
                        <li>{{ $repository }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
    <div class="w-1/2 float-left">
        @if(isset($scope['domains']))
            <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Domains</p>
            <ul class="list-inside list-disc text-xs">
                @foreach($scope['domains'] as $domains)
                    <li>{{ $domains }}</li>
                @endforeach
            </ul>
        @endif
        @if(isset($scope['hosts']))
            <p class="text-xs pt-3 font-bold pb-1 text-gray-500 font-bold">Hosts</p>
            <ul class="list-inside list-disc text-xs">
                @foreach($scope['hosts'] as $host)
                    <li>{{ $host }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>