@if(session('success'))
  <div class="mb-4 rounded-md bg-green-50 border border-green-100 p-3 text-green-800">
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="mb-4 rounded-md bg-red-50 border border-red-100 p-3 text-red-800">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="mb-4 rounded-md bg-red-50 border border-red-100 p-3 text-red-800">
    <ul class="list-disc pl-5">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif
