<?php
    use function Laravel\Folio\{middleware, name};
    use App\Models\Project;
    use Livewire\Volt\Component;
    middleware('auth');
    name('projects');

    new class extends Component{
        public $projects;

        public function mount()
        {
            $this->projects = auth()->user()->projects()->latest()->get();
        }
    }
?>

<x-layouts.app>
    @volt('projects')
        <x-app.container>



            <div class="">
                <x-app.heading
                        title=""
                        description=""
                        :border="false"
                    />
            </div>

            
<!-- filters -->
<div class="m-2 mb-4">
  <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg">
    <h2 class="text-stone-700 text-xl font-bold">Apply filters</h2>
    <p class="mt-1 text-sm">Use filters to further refine search</p>
    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <div class="flex flex-col">
        <label for="name" class="text-stone-600 text-sm font-medium">Name</label>
        <input type="text" id="name" placeholder="raspberry juice" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
      </div>

      <div class="flex flex-col">
        <label for="manufacturer" class="text-stone-600 text-sm font-medium">Manufacturer</label>
        <input type="manufacturer" id="manufacturer" placeholder="cadbery" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
      </div>

      <div class="flex flex-col">
        <label for="date" class="text-stone-600 text-sm font-medium">Date of Entry</label>
        <input type="date" id="date" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
      </div>

      <div class="flex flex-col">
        <label for="status" class="text-stone-600 text-sm font-medium">Status</label>

        <select id="status" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
          <option>Dispached Out</option>
          <option>In Warehouse</option>
          <option>Being Brought In</option>
        </select>
      </div>
    </div>

    <div class="mt-6 grid w-full grid-cols-2 justify-end space-x-5 md:flex">
      <button class="active:scale-95 rounded-lg bg-gray-200 px-8 py-2 font-medium text-gray-600 outline-none focus:ring hover:opacity-90">Reset</button>
      <button class="active:scale-95 rounded-lg bg-black px-8 py-2 font-medium text-white outline-none focus:ring hover:opacity-90">Search</button>
    </div>
  </div>
</div>



            @if($projects->isEmpty())

                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @for ($i = 1; $i <= 6; $i++)

                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Library ID: 1791005801307248</p>
                        <p class="text-sm text-gray-500">Started running on: 3 Jul 2024</p>
                        <p class="text-sm text-gray-500">Country: Canada</p>
                        <p class="text-lg font-semibold mb-2">139 active ads</p>
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 w-full mb-2 rounded-md">See ad details</button>
                        
                        <div class="bg-gray-100 rounded-lg">
                            <div class="flex items-center mb-2">
                                <img src="image_path" class="w-10 h-10 rounded-full mr-2" alt="Dr. Atul Gawande"> 
                                <div>
                                    <h3 class="text-lg font-semibold">Dr. Atul Gawande</h3>
                                    <p class="text-sm text-gray-500">Sponsored</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700">Did you know that 25-33% of women and 10-20% of men deal with varicose veins? Say farewell to discom...</p>
                        </div>

                        <div class="flex justify-center items-center">
                        <video class="" width="150" height="150" controls>
                            <source src="https://video.xx.fbcdn.net/v/t42.1790-2/472970839_1258996325215598_240301483610512581_n.?_nc_cat=106&ccb=1-7&_nc_sid=c53f8f&_nc_ohc=yX4gbwQymCQQ7kNvgGZFfFi&_nc_zt=28&_nc_ht=video.falg3-2.fna&_nc_gid=AY_MgQEWhFWyP5KDpCX69Mf&oh=00_AYDwnyXBpcnRL_tIF0LaIlqJdLRM7JWUVIFr-n75bPA6vA&oe=678ADF17" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        </div>


                        <div class="flex justify-between items-center p-4 bg-gray-100 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-500">ساعة عصرية ذكية</p>
                            </div>
                            <div class="flex-grow text-right">
                                <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md">Order now</button>
                            </div>
                        </div>

                    </div>
                    @endfor

                </div>



            @else
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full bg-white">
                        <thead class="text-sm bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-left">Start Date</th>
                                <th class="px-4 py-2 text-left">End Date</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                                <tr>
                                    <td class="px-4 py-2">{{ $project->name }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($project->description, 50) }}</td>
                                    <td class="px-4 py-2">{{ $project->start_date ? $project->start_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $project->end_date ? $project->end_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        <a href="/project/edit/{{ $project->id }}" class="mr-2 text-blue-500 hover:underline">Edit</a>
                                        <button wire:click="deleteproject({{ $project->id }})" class="text-red-500 hover:underline">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-app.container>
    @endvolt
</x-layouts.app>
