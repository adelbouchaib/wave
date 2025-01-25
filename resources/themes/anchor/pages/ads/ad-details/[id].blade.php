<?php
    use function Laravel\Folio\{middleware, name};
    use Livewire\Attributes\Validate;
    use App\Models\Ad;
    use Livewire\Volt\Component;
    use Filament\Notifications\Notification;

    middleware('auth');
    name('wave.ads');

    new class extends Component
    {

        
        public $specific_ad;
        public $ad;
        public $jsonData;

        public function isTracked($adId)
        {
            $user = auth()->user();
            return $user->ads()->where('ad_id', $adId)->exists();
        }

        public function addSave($id)
        {
            $user = auth()->user(); // Get the currently authenticated user
            // // Check if the user has already tracked 10 ads
            // if ($user->ads()->count() >= 10) {
            //     Notification::make()
            //     ->title('Limit Reached')
            //     ->body('You can only track up to 10 ads.')
            //     ->danger() // Style it as an error (red alert)
            //     ->send();
        
            // return;
            // }

            // Add the ad to the user's tracked ads if the limit is not reached
            $user->ads()->syncWithoutDetaching([$id]);

            Notification::make()
            ->title('Ad Tracked Successfully')
            ->body('The ad has been added to your tracking list.')
            ->success() // Style it as a success (green alert)
            ->send();
    
            $ads = Ad::where('id', $id)->first(); // Fetch a single record
            $adId = $ads->library_id;
            return redirect()->to('/ads/ad-details/' . $adId);
    
        }
        
        
                public function goToAdTrack($id)
                {
                    return redirect()->to('/ads/ad-track/' . $id);
                }
        

        
        public function mount($id)
        {

        // $this->ad = Ad::find($id); // Replace YourModel with the relevant model
        $this->ad = Ad::where('library_id', $id)->first(); 


        $ads = Ad::where('library_id', $id)->first(); // Fetch a single record
        $this->jsonData = $ads ? $ads->count : null; // Access the count attribute  

          // $data = Ad::find($ad)->pluck('count')->toArray();
          // $this->jsonData = $data;
        
            $this->specific_ad = $ads;
        }

        
        // public $data = [
        //   ['Day'=>'Mon', 'Value'=>'{{$this->ad->count1}}']
        //   ['Day'=>'Mon', 'Value'=>'{{$this->ad->count2}}']

        // ];

        

    }
?>

<x-layouts.app>
    @volt('ads.create')
        <x-app.container>



            <x-elements.back-button
                class="max-w-full mx-auto mb-3"
                text="Back to Ads"
                :href="route('ads')"
            />

            <div class="flex items-center justify-between">
                <x-app.heading
                        title=""
                        description=""
                        :border="false"
                    />
            </div>


                          

<div class="flex flex-col md:flex-row p-2 rounded-lg shadow-lg max-w-4xl mx-auto mb-5 gap-4 ">
    
            <!-- Image Section -->
  <div class="relative bg-gray-10 lg:w-1/2 max-w-md">
                <div class="rounded-lg p-2 bg-gray-100" >
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

                     
                        <div  class="flex items-center justify-center rounded mt-2 bg-gray-100">
                            @if ($ad->creative_type == "IMAGE")
                                <div class="flex justify-center items-center">
                                    <img src="{{ $ad->creative_url }}" alt="Ad Image"  class=" object-cover">
                                </div>
                            @elseif ($ad->creative_type == "VIDEO")
                                <div class="flex justify-center rounded items-center">
                                    <video class="" controls>
                                        <source src="{{ $ad->creative_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        </div>



                       

                        <div class="flex justify-between items-center bg-gray-100 rounded-lg gap-2 my-4" style=" width: 100%; overflow: hidden;">
                                <!-- <p class="text-sm text-gray-500" style="white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis;">{{$ad->headline}}</p>
                                <p class="text-sm text-gray-500" style="white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis;">{{$ad->description}}</p> -->
                                <div style="width: 100%; overflow: hidden;">
                                    <p class="text-md text-gray-500" 
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
                                    <p class="text-sm text-gray-500" 
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; 
                                    {{ isTextArabic($ad->description) ? 'direction: rtl; text-align: right;' : 'direction: ltr;' }}">                                         
                                        {!! cleanArabicText($ad->description) !!}

                                    </p>
                                </div>
                                <div >
                                    <a href="{{ empty($ad->url) ? $ad->page_url : $ad->url}}" target="_blank">
                                    <button class="w-full bg-gray-200 text-md hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md"  style="white-space: nowrap;">{{$ad->cta}}</button>
                                    </a>
                                </div>
                        </div>
                        

                       

  </div>


  <div class="mt-6 md:mt-0 md:ml-6 border w-full">
    <div class="mb-4 mt-4 px-4">
    
                            <div class="mb-4">

                            @if ($this->isTracked($ad->id))
                                <button style="width: 100%" class="bg-green-500 text-white px-2 py-2 rounded font-semibold text-sm flex items-center justify-center gap-2">
                                <x-heroicon-s-bookmark class="w-4 h-4" /> Saved to Spyder
                                </button>
                            @else
                               
                            <button wire:click="addSave({{ $ad->id }})" style="width: 100%" class="bg-gray-200 py-2 font-semibold border text-sm rounded flex items-center justify-center gap-2">
                                <x-heroicon-o-bookmark class="w-4 h-4" /> Save to Spyder
                            </button>
                            @endif
                            </div>
                              
                          

                            <div  class="flex-grow bg-white border shadow rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
                                <canvas id="myChart"></canvas>
                            </div>

                           

        
        <div class="bg-white shadow-md border rounded-lg p-4 space-y-4 ml-auto mt-4">
          <!-- Date Information -->
          <div class="space-y-1">
            <div class="flex items-center gap-2 justify-between text-gray-700">
                <span class="flex items-center space-x-1">
                      <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 3h-1V2h-2v1H8V2H6v1H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 19V8h14v11H5zm14-13H5V5h14v1z"/>
                      </svg>
                      <span>Active time</span>
                </span>
                  <span class="text-gray-900">
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
                  </span>
                            
                          
              </div>

              <div class="flex items-center gap-2 justify-between text-gray-700">
                  <span class="flex items-center space-x-1">
                      <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 3h-1V2h-2v1H8V2H6v1H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 19V8h14v11H5zm14-13H5V5h14v1z"/>
                      </svg>
                      <span>Created at</span>
                  </span>
                  <span class="text-gray-900">
                  {{ \Carbon\Carbon::createFromTimestamp($specific_ad->starting_date)->format('Y-m-d') }}
                  </span>
              </div>
           
              <div class="flex items-center gap-2 justify-between text-gray-700">
              <span class="flex items-center space-x-1">
                  <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M0 0h24v24H0z" fill="none"/><path d="M12 6c4.41 0 8-1.79 8-4H4c0 2.21 3.59 4 8 4zm0 2C7.48 8 0 9.79 0 12h24c0-2.21-7.48-4-12-4zm0 2c4.41 0 8 1.79 8 4H4c0-2.21 3.59-4 8-4z"/>
                  </svg>
                  <span>First update</span>
              </span>
              <span id="firstDate" class="text-gray-900"></span>
              </div>
              <div class="flex items-center gap-2 justify-between text-gray-700">
              <span class="flex items-center space-x-1">
                  <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M0 0h24v24H0z" fill="none"/><path d="M12 12c2.21 0 4-.79 4-2H8c0 1.21 1.79 2 4 2zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm0-4c3.31 0 6-1.34 6-3V5c0-1.66-2.69-3-6-3S6 3.34 6 5v2c0 1.66 2.69 3 6 3z"/>
                  </svg>
                  <span>Last update</span>
              </span>
              <span id="lastDate" class="text-gray-900"></span>
              </div>
          </div>
        </div>

     

    </div>
      
   
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@script
<script>
  const ctx = document.getElementById('myChart');
  const data = {!! json_encode($jsonData) !!};

  let validJsonString = `[${data}]`;  // This will now look like: '[{"date":"2025-01-18","count":"4"},{"date":"2025-01-18","count":"4"}]'
  
  const parsedData = JSON.parse(validJsonString);

    const counts = parsedData.map(item => item.count); // Get only the 'number' field
    const labels = parsedData.map(item => item.date); // Get the 'date' field

    // Get the last number from the counts array
    const lastDate = labels[labels.length - 1];
    document.getElementById('lastDate').innerText = lastDate;
     
    const firstDate = labels[0];
    document.getElementById('firstDate').innerText = firstDate;


  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Active ads',
        data: counts,
        borderWidth: 1,
        borderColor: '#441752',
        

      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
@endscript
         
        </x-app.container>
    @endvolt
</x-layouts.app>
