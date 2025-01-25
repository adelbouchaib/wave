<?php
    use function Laravel\Folio\{middleware, name};
    use App\Models\Ad;
    use App\Models\User;

    use Livewire\Volt\Component;
    use Carbon\Carbon;

    middleware('auth');
    name('save');

    new class extends Component
    {
        public $search;
        public $media = '';
        public $cta_type = '';

        public $start_date = '';
        public $end_date = '';

        public $min_ads = '';
        public $max_ads = '';

        public $amount = 9; // Number of ads to load at a time
        public $ads = []; // Current ads loaded
        public $offads = []; // Current ads loaded
        public $filters = []; // Array to store filters

        public $id;

        public function goToAdDetails($id)
        {
            return redirect()->to('/ads/ad-details/' . $id);
        }

        public function goToAdTrack($id)
        {
            return redirect()->to('/ads/ad-track/' . $id);
        }

        
        public function mount()
        {
            // $this->applyFilters();

            $this->applyFilters();

        }
        
        public function applyFilters()
        {
            // Base query with all filters
            $query = Ad::query()
            ->whereHas('users', function ($q) {
                $q->where('user_id', auth()->id()); // Filter ads related to the authenticated user
            })
            ->orderBy('id', 'asc');
        
            // Fetch only the number of ads based on the current amount
            $this->ads = $query->get();

            $this->offads = $query->where('count', '0')
            ->get();

        }
        
        public function searchAds()
        {
            // Reset the number of ads to load
            $this->amount = 9;
        
            // Reapply filters and fetch the initial batch
            $this->applyFilters();
        }
        
        public function loadmore()
        {
            // Increment the number of ads to load
            $this->amount += 9;
        
            // Fetch more ads based on updated amount
            $this->applyFilters();
        }
        

        public function open()
        {
            $this->showModal = true;
        }
    
        public function close()
        {
            $this->showModal = false;
        }

    };

?>



<x-layouts.app>
    @volt('save')
    
   
        <x-app.container class="max-w-full lg:pt-0 ">
            <div class="">
                <x-app.heading
                        title=""
                        description=""
                        :border="false"
                    />
            </div>


            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($ads as $ad)

                <div class="border rounded-lg">
                        <!-- <div class="flex justify-between items-center bg-gray-100 rounded-lg" style="width: 100%; overflow: hidden;">
                        <p class="text-sm text-gray-500">Started running on: {{ \Carbon\Carbon::createFromTimestamp($ad->starting_date)->toDateString() }}</p>
                        </div> -->
                        
                    <div class="border-b bg-gray-100 border-gray-300 p-2">

                        <span style="display: inline-block; vertical-align: middle;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" style="color: #6B7280;">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                <line x1="12" y1="6" x2="12" y2="12" stroke="currentColor" stroke-width="2" />
                                <line x1="12" y1="12" x2="16" y2="14" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </span>

                        <span style="display: inline-block; vertical-align: middle;" class="text-sm text-gray-500">
                            @php
                            $currentTime = \Carbon\Carbon::now();
                            $startingDate = \Carbon\Carbon::createFromTimestamp($ad->starting_date);

                            $diff = $currentTime->diffForHumans($startingDate);

                            $cleanDiff = str_replace('after', '', $diff);

                            echo $cleanDiff;
                            @endphp
                        </span>

                        <span style="display: inline-block; vertical-align: middle;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 16 16" fill="none">
                            <circle cx="8" cy="8" r="6" fill="green" />
                            </svg>
                        </span>

                        <span style="display: inline-block; vertical-align: middle;" class="text-sm text-gray-500">
                            {{$ad->collation_count}} active ads
                            
                            <!-- <p class="text-sm font-semibold text-red-500 ml-4 mt-1">
                                
                                    @php
                                    $countsData = '[' . $ad->count . ']'; // Wrap the data in square brackets to make it valid JSON
                                    $countsData = json_decode($countsData, true); // Decode the JSON string from the 'count' column
                                    
                                    $today = \Carbon\Carbon::today()->toDateString();
                                    $yesterday = \Carbon\Carbon::yesterday()->toDateString();
                                    $firstData = collect($countsData)->firstWhere('date', $today);
                                    $secondData = collect($countsData)->firstWhere('date', $yesterday);

                                    if($firstData && $secondData){
                                        $todayCount = (int)$firstData['count'];  // Ensure it's treated as an integer
                                        $yesterdayCount = (int)$secondData['count'];  // Ensure it's treated as an integer
                                        
                                        if ($todayCount != $yesterdayCount) {
                                            $difference = $todayCount-$yesterdayCount;
                                            echo "(";
                                            echo $difference >= 0 ? '+' : '' ;
                                            echo $difference;
                                            echo " compared to yesterday)";

                                        }
                                    }
                                    @endphp

                            </p> -->
                        </span>






                        <span style=" display: inline-block;">
                            <button wire:click="goToAdDetails({{ $ad->library_id }})" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-1 px-2 rounded-md ml-2" >
                                Details
                            </button>
                        </span>

                      
                    </div>

                        <div class="rounded-lg p-2">
                            <div class="flex items-center mb-2">
                                    <img src="{{$ad->page_picture}}" alt="Image" class="w-8 h-8 mr-2 rounded-full object-cover"> 
                                    <h3 class="text-md font-semibold">
                                        @php
                                        $decodedString = json_decode('"' . $ad->page_name . '"');
                                        echo $decodedString;
                                        @endphp
                                    </h3>
                            </div>
                            <div x-data="{ isTruncated: true }">
        
                                <p x-bind:class="{ 'line-clamp-3': isTruncated }" style="{{ isTextArabic($ad->copy) ? 'direction:rtl' : 'direction:ltr' }}"  x-on:click="isTruncated = !isTruncated" class="text-sm text-gray-700 cursor-pointer">
                                    <!-- {{ $ad->copy }} -->
                        
                                    {!! cleanArabicText($ad->copy) !!}
                                </p>
                            </div>
                        </div>

                     
                        <div  class="flex items-center justify-center rounded mt-2 bg-gray-100" style="max-height:340px;">
                            @if ($ad->creative_type == "IMAGE")
                                <div class="flex justify-center items-center">
                                    <img src="{{ $ad->creative_url }}" alt="Ad Image"  class=" object-cover"  style="max-height: 340px"; width: auto;">
                                </div>
                            @elseif ($ad->creative_type == "VIDEO")
                                <div class="flex justify-center rounded items-center">
                                    <video class="" controls style="max-height:340px;">
                                        <source src="{{ $ad->creative_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        </div>



                       

                        <div class="flex justify-between items-center bg-gray-100 rounded-lg gap-2" style="display: none; width: 100%; overflow: hidden;">
                                <!-- <p class="text-sm text-gray-500" style="white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis;">{{$ad->headline}}</p>
                                <p class="text-sm text-gray-500" style="white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis;">{{$ad->description}}</p> -->
                                <div style="width: 100%; overflow: hidden;">
                                    <p class="text-xs text-gray-500" 
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; 
                                    {{ isTextArabic($ad->headline) ? 'direction: rtl; text-align: right;' : 'direction: ltr;' }}">@if($ad->headline == '')
                                            @php
                                            $decodedString = json_decode('"' . $ad->page_name . '"');
                                            echo $decodedString; 
                                            @endphp
                                            @else
                                            {!! cleanArabicText($ad->headline) !!}
                                            @endif
                                    </p>
                                    <p class="text-xs text-gray-500" 
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; 
                                    {{ isTextArabic($ad->description) ? 'direction: rtl; text-align: right;' : 'direction: ltr;' }}">                                         
                                        {!! cleanArabicText($ad->description) !!}

                                    </p>
                                </div>
                                <div>
                                    <a href="{{ empty($ad->url) ? $ad->page_url : $ad->url}}" target="_blank">
                                    <button class="w-full bg-gray-200 text-xs hover:bg-gray-300 text-gray-700 font-medium py-2 px-2 rounded-md"  style="white-space: nowrap;">{{$ad->cta}}</button>
                                    </a>
                                </div>
                        </div>
                        


                       
                    </div>
                @endforeach
                            
            </div>
            <script src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>


                        <div x-intersect="$wire.loadmore" class ="border-4 h-60">
                        </div>
        

        </x-app.container>
    @endvolt
</x-layouts.app>
