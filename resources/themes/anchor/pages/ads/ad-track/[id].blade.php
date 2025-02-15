<?php
    use function Laravel\Folio\{middleware, name};
    use Livewire\Attributes\Validate;
    use App\Models\Ad;
    use Livewire\Volt\Component;
    middleware('auth');
    name('ads.ad-track');

    new class extends Component
    {

        
        public $specific_ad;
        public $ad;
        public $jsonData;

        
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
    @volt('ads.ad-track.[id]')
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


<div class="bg-gray-100 p-2 rounded-lg shadow-lg max-w-4xl mx-auto mb-5 ">

    <h1 class="text-xl font-semibold mb-2 md:self-start">Product Tracking & Updates</h1>

    <div class="flex flex-col md:flex-row gap-4">


        <div class="flex-grow bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
            <canvas id="myChart"></canvas>
        </div>

        <div class="bg-white shadow-md rounded-lg p-4 space-y-4 ml-auto">
          <!-- Date Information -->
          <div class="space-y-1">
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
                  <path d="M0 0h24v24H0z" fill="none"/><path d="M12 6c4.41 0 8-1.79 8-4H4c0 2.21 3.59 4 8 4zm0 2C7.48 8 0 9.79 0 12h24c0-2.21-7.48-4-12-4zm0 2c4.41 0 8 1.79 8 4H4c0-2.21 3.59-4 8-4z"/>
                  </svg>
                  <span>Added</span>
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
        label: '',
        data: counts,
        borderWidth: 1
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
