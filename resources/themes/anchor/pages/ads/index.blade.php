<?php
    use function Laravel\Folio\{middleware, name};
    use App\Models\Ad;
    use Livewire\Volt\Component;
    use Carbon\Carbon;

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
        
        public function mount()
        {
            $this->applyFilters();
        }
        
        public function applyFilters()
        {
            // Base query with all filters
            $query = Ad::query();
        
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

<!-- filters -->
<form wire:submit.prevent="searchAds" @keydown.enter.prevent="return false;">
    <div class="m-2 mb-4 lg:px-10">
        <div class="rounded-xl border border-gray-200 bg-white p-6 pt-0 shadow-lg">
            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 ">
                <div class="flex flex-col">
                    <label for="name" class="text-stone-600 text-sm font-medium">Search</label>
                    <input wire:model.defer="search" type="text" id="name" placeholder="'Veste'" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
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
            </div>

            <div class="m-2 mt-4 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                <!-- date -->
                <div class="flex flex-col">
                    <label for="status" class="text-stone-600 text-sm font-medium">Creation date</label>
                        <div x-data="{
                                        startDate: @entangle('startDate'),
                                        endDate: @entangle('endDate'),
                                        open: false
                                    }"
                                    class="relative inline-block text-sm mt-2"
                                >
                                    <!-- Datepicker Button -->
                                    <button 
                                        @click="open = !open"
                                        class="width-text-gray-700 font-medium border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <span x-text="startDate ? startDate : 'Start Date'"></span> - 
                                        <span x-text="endDate ? endDate : 'End Date'"></span>
                                    </button>

                                    <!-- Datepicker Pop-up -->
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false" 
                                        x-transition
                                        class="absolute z-10 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg"
                                    >
                                        <div class="flex p-4 space-x-4 gap-4">
                                            <div class="w-1/2">
                                                <label class="block text-xs font-medium text-gray-600">Start Date</label>
                                                <input 
                                                    wire:model.defer="start_date" 
                                                    type="date" 
                                                    x-model="startDate" 
                                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                />
                                            </div>
                                            <div class="w-1/2">
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
                <!-- date -->

                <!-- active time -->
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
                <!-- active time -->

                <div class="mt-6 grid w-full grid-cols-2 justify-end space-x-5 md:flex">
                    <button type="reset" class="active:scale-95 rounded-lg bg-gray-200 px-8 py-2 font-medium text-gray-600 outline-none focus:ring hover:opacity-90">Reset</button>
                    <button class="active:scale-95 rounded-lg bg-black px-8 py-2 font-medium text-white outline-none focus:ring hover:opacity-90">Search</button>
                </div>

            </div>

        </div>
    </div>
</form>


            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($ads as $ad)

                    <div class="bg-gray-100 rounded-lg p-4">
                        <div class="flex justify-between items-center bg-gray-100 rounded-lg" style="width: 100%; overflow: hidden;">
                        <p class="text-sm text-gray-500">Started running on: {{ \Carbon\Carbon::createFromTimestamp($ad->starting_date)->toDateString() }}</p>

                        </div>
                        <p class="text-sm text-gray-500">Total active time: 
                        
                            @php
                            // Get the current time
                            $currentTime = \Carbon\Carbon::now();

                            // Get the starting date from the timestamp
                            $startingDate = \Carbon\Carbon::createFromTimestamp($ad->starting_date);

                            // Calculate the difference between current time and starting date
                            $diff = $currentTime->diffForHumans($startingDate);

                            $cleanDiff = str_replace('after', '', $diff);
                
                            echo $cleanDiff;
                            @endphp

                        </p>
                        <p class="text-lg font-semibold mb-2 text-blue-500">{{$ad->collation_count}} active ads</p>

                        <a href="{{ route('ads.create', ['id' => $ad->id]) }}">
                        <button class=" text-md bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 w-full mb-2 rounded-md">See ad details</button>
                        </a>

                        <div class="bg-gray-100 rounded-lg">
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
                                @php
                                $firstChar = mb_substr($ad->copy, 0, 1);
                                $isArabic = preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u', $firstChar);

                                @endphp
                                <p x-bind:class="{ 'line-clamp-3': isTruncated }" style="{{ $isArabic ? 'direction:rtl' : 'direction:ltr' }}"  x-on:click="isTruncated = !isTruncated" class="text-xs text-gray-700 cursor-pointer">
                                    <!-- {{ $ad->copy }} -->
                                    @php
                                    $inputString = nl2br(e($ad->copy));
                                    $cleanedString = preg_replace('/(\?){2,}/', '', $inputString); // Replaces consecutive ?? or more with a single ?
                                    echo $cleanedString;
                                    @endphp
                                </p>
                            </div>
                        </div>

                     
                        <div style="height: 250px;" class="flex items-center justify-center mb-4 mt-4">
                        @if ($ad->creative_type == "IMAGE")
                            <div class="flex justify-center items-center">
                                <img src="{{ $ad->creative_url }}" alt="Ad Image"  class=" object-cover"  style="max-height: 250px; width: auto;">
                            </div>
                        @elseif ($ad->creative_type == "VIDEO")
                            <div class="flex justify-center items-center">
                                <video class="" width="150" height="150" style="max-height: 250px; width: auto;" controls >
                                    <source src="{{ $ad->creative_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @endif
                        </div>



                       

                        <div class="flex justify-between items-center bg-gray-100 rounded-lg" style="width: 100%; overflow: hidden;">
                                <!-- <p class="text-sm text-gray-500" style="white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis;">{{$ad->headline}}</p>
                                <p class="text-sm text-gray-500" style="white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis;">{{$ad->description}} ...</p> -->
                                <div style="width: 100%; overflow: hidden;">
                                    <p class="text-sm text-gray-500" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">
                                    @if($ad->headline == '')
                                        @php
                                        $decodedString = json_decode('"' . $ad->page_name . '"');
                                        echo $decodedString; 
                                        @endphp
                                    @else
                                        {{ $ad->headline }}
                                    @endif
                                    </p>
                                    <p class="text-sm text-gray-500" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">{{$ad->description}}</p>
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
