<?php
    use function Laravel\Folio\{middleware, name};
    use App\Models\Ad;
    use Livewire\Volt\Component;
    use Carbon\Carbon;
    use Filament\Notifications\Notification;


    middleware('auth');
    name('ads');

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
        public $filters = []; // Array to store filters

        public $id;

        public function goToAdDetails($id)
        {
            return redirect()->to('/ads/ad-details/' . $id);
        }



        
        public function mount()
        {
            $this->applyFilters();
        }

        public $selected = 'Newest'; // Default selected option
        public $sortOrder = 'desc';  // Default sort order
        
        public function applyFilters()
        {
            // Base query with all filters
            // $query = Ad::query()->orderBy('starting_date', 'desc');

             // Base query with all filters
        $query = Ad::query();

        // Apply the sort order based on the selected option
        if ($this->selected == 'Newest') {
            $query->orderBy('starting_date', 'desc');
        } elseif ($this->selected == 'Longest Running') {
            $query->orderBy('starting_date', 'asc');
        } elseif ($this->selected == 'Total Active Ads') {
            $query->orderBy('collation_count', 'desc');
        }
        
            // Apply filters dynamically
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('copy', 'like', "%{$this->search}%")
                      ->orWhere('headline', 'like', "%{$this->search}%");
                });
            }
        
            if ($this->media) {
                $query->where('creative_type', '=', $this->media);
            }
        
            if ($this->start_date) {
                $start_date = Carbon::createFromFormat('Y-m-d', $this->start_date)->timestamp;
                $query->where('starting_date', '>=', $start_date);
            }
        
            if ($this->end_date) {
                $end_date = Carbon::createFromFormat('Y-m-d', $this->end_date)->timestamp;
                $query->where('starting_date', '<=', $end_date);
            }
        
            if ($this->min_ads) {
                $query->where('collation_count', '>=', $this->min_ads);
            }
        
            if ($this->max_ads) {
                $query->where('collation_count', '<=', $this->max_ads);
            }

            if ($this->cta_type) {
                $query->where('cta', '=', $this->cta_type);
            }
        
            // Fetch only the number of ads based on the current amount
            $this->ads = $query->latest()->take($this->amount)->get();
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
        

    };

?>

<x-layouts.app>

    @volt('ads')
        <x-app.container class="max-w-full lg:pt-0 ">
            <div class="">
                <x-app.heading
                        title=""
                        description=""
                        :border="false"
                    />
            </div>




<!-- Hidden Input to Bind to Livewire Property -->
<!-- <input type="hidden" wire:model.defer="selected" /> -->



<!-- filters -->
<form wire:submit.prevent="searchAds" @keydown.enter.prevent="return false;">




<div x-data="{ showForm: false }" style="text-align:center">
    <!-- Toggle Button -->
    <button 
        @click="showForm = !showForm" 
        class="w-1/2 mb-4 border bg-white-200 text-gray-600 py-2 rounded-md text-md font-semibold shadow-md"
    >
        Add Filters
    </button>

    <!-- Filters Form -->
    <div x-show="showForm" x-transition class="mt-4">


    <div class="border px-8 mb-4">

   

            <div class="mt-8 grid gap-4 grid-cols-2  md:grid-cols-2 lg:grid-cols-5 xl:grid-cols-5 ">
                <div class="flex flex-col">
                    <label for="name" class="text-stone-600 text-sm font-medium">Search</label>
                    <input wire:model.defer="search" type="text" id="name" placeholder="'للطلب'" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                </div>

                <div class="flex flex-col">
                    <label for="status" class="text-stone-600 text-sm font-medium">Media type</label>
                    <select wire:model.defer="media" id="status" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="" disabled selected>Media type</option>
                        <option value="IMAGE">Image</option>
                        <option value="VIDEO">Video</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="status" class="text-stone-600 text-sm font-medium">CTA type</label>
                    <select  wire:model.defer="cta_type" id="status" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="" disabled selected>CTA type</option>
                        <option value="shop now">Shop now</option>
                        <option value="send message">Send message</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="status" class="text-stone-600 text-sm font-medium">Creation date</label>
                        <div x-data="{
                                        startDate: @entangle('startDate'),
                                        endDate: @entangle('endDate'),
                                        open: false
                                    }"
                                    class="relative inline-block text-sm mt-2 w-full "
                                >
                                    <!-- Datepicker Button -->
                                    <button 
                                        @click="open = !open"
                                        class="w-full width-text-gray-700 font-medium border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <span x-text="startDate ? startDate : 'Start'"></span> - 
                                        <span x-text="endDate ? endDate : 'End'"></span>
                                    </button>

                                    <!-- Datepicker Pop-up -->
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false" 
                                        x-transition
                                        class="absolute z-10 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg"
                                    >
                                        <div class="p-4 space-x-4 gap-4">
                                            <div class="w-full">
                                                <label class="block text-xs font-medium text-gray-600">Start Date</label>
                                                <input 
                                                    wire:model.defer="start_date" 
                                                    type="date" 
                                                    x-model="startDate" 
                                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                />
                                            </div>
                                            <div class="w-full">
                                                <label class="block text-xs font-medium text-gray-600">End Date</label>
                                                <input 
                                                    wire:model.defer="end_date" 
                                                    type="date" 
                                                    x-model="endDate" 
                                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                />
                                            </div>
                                        </div>

                                    </div>
                        </div>
                </div>
                
                <div class="flex flex-col">
                    <label for="status" class="text-stone-600 text-sm font-medium">Active ads</label>

                    <div x-data="{
                                minNumber: 2,
                                maxNumber: 100,
                            }" 
                            class="flex items-center space-x-4 pt-2 bg-white"
                        >
                            <!-- Min Number Input -->
                            <input 
                                wire:model.defer="min_ads" 
                                type="number" 
                                x-model="minNumber" 
                                min="2" 
                                max="100" 
                                class="w-30 px-2 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 text-center"
                            />
                            <span class="text-sm text-gray-700 px-2">to</span>
                            <!-- Max Number Input -->
                            <input 
                                wire:model.defer="max_ads" 
                                type="number" 
                                x-model="maxNumber" 
                                min="0" 
                                max="100" 
                                class="w-24 px-2 py-1 border border-gray-300  rounded-md focus:ring-2 focus:ring-blue-500 text-center"
                            />
                        
                    </div>

                </div>

            </div>

            <div class="flex flex-col my-4">
                    <!-- <button type="reset" class="active:scale-95 rounded-lg bg-gray-200 px-8 py-2 font-medium text-gray-600 outline-none focus:ring hover:opacity-90">Reset</button> -->
                    <button class="active:scale-95 bg-gray-200 font-semibold border rounded-lg px-8 py-2 font-medium outline-none focus:ring hover:opacity-90 flex items-center justify-center gap-2">                     
                        <x-heroicon-s-magnifying-glass class="w-4 h-4" /> Search
                     </button>
            </div>

</div>
</div>
</div>

<div x-data="{ open: false, selected: @entangle('selected') }" class="relative inline-block">
  <!-- Dropdown Button -->
  <button 
    @click="open = !open" 
    class="bg-gray-100 border px-4 py-1 rounded flex items-center gap-2"
  >
    <span x-text="selected"></span>
    <x-heroicon-s-chevron-down class="w-4 h-4" />
  </button>

  <!-- Dropdown Menu -->
  <div 
    x-show="open" 
    @click.away="open = false" 
    class="absolute left-0 mt-2 bg-white border rounded shadow-lg w-48"
    x-transition
  >
    <!-- Dropdown Items -->
    <div 
      class="px-4 py-2 hover:bg-gray-100 cursor-pointer" 
      @click="selected = 'Newest'; open = false; $wire.applyFilters();"
    >
      Newest
    </div>
    <div 
      class="px-4 py-2 hover:bg-gray-100 cursor-pointer" 
      @click="selected = 'Longest Running'; open = false; $wire.applyFilters();"
    >
      Longest Running
    </div>
    <div 
      class="px-4 py-2 hover:bg-gray-100 cursor-pointer" 
      @click="selected = 'Total Active Ads'; open = false; $wire.applyFilters();"
    >
        Total Active Ads
    </div>
  </div>
</div>

<!-- Hidden Input to Bind to Livewire Property -->
<input type="hidden" wire:model="selected" />



</form>


            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($ads as $ad)

                <div class="border rounded-lg mt-4">
                        <!-- <div class="flex justify-between items-center bg-gray-100 rounded-lg" style="width: 100%; overflow: hidden;">
                        <p class="text-sm text-gray-500">Started running on: {{ \Carbon\Carbon::createFromTimestamp($ad->starting_date)->toDateString() }}</p>
                        </div> -->
                        

                      


                    <div class="border-b bg-gray-100 border-gray-300 p-2 flex flex-col">

                    <div>
                    <span 
                        class="flex items-center gap-1 px-2 border bg-green-100 text-green-700 font-semibold text-xs rounded-full"
                        style="display: inline-flex; align-items: center;">
                        <x-heroicon-o-arrow-trending-up class="w-4 h-4" />
                        New
                    </span>
                    </div>

                        <div>
                      
                        <span style="display: inline-block; vertical-align: middle;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" style="color: #6B7280;">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                <line x1="12" y1="6" x2="12" y2="12" stroke="currentColor" stroke-width="2" />
                                <line x1="12" y1="12" x2="16" y2="14" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </span>

                        <span style="display: inline-block; vertical-align: middle;" class="text-sm text-gray-500">
                            @php
                            // Get the current time
                            $currentTime = \Carbon\Carbon::now();

                            // Get the starting date from the timestamp
                            $startingDate = \Carbon\Carbon::createFromTimestamp($ad->starting_date);

                            // Calculate the difference between current time and starting date
                            $diff = $currentTime->diffForHumans($startingDate);

                            $cleanDiff = str_replace('after', '', $diff);

                            echo $cleanDiff;
                            echo "running"

                            @endphp
                        </span>
                        </div>

                        <div>
                        <span style="display: inline-block; vertical-align: middle;" class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
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

                       
                        </div>

                       
                            
                      


                        <div style="width:100%; text-align:center; " class="mt-2 text-md border bg-gray-200 rounded flex items-center justify-center">
                            <button 
                                wire:click="goToAdDetails({{ $ad->library_id }})" 
                                class="text-black border-b px-2 py-1 rounded font-semibold text-md flex items-center justify-center gap-2"
                            >
                            <x-heroicon-s-eye class="w-4 h-4" /> Tracking & Insights
                            </button>
</div>



                    </div>
                    
                        

                        <div class="rounded-lg p-2 bg-gray-100">

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



                       

                        <div class="flex justify-between items-center bg-gray-100 rounded-lg gap-2 py-2 px-2" style="width: 100%; overflow: hidden;">
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
