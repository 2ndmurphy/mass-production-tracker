{{-- Example usage of the layout --}}
<x-layout :title="'Staff Dashboard'">
  <x-slot name="header">Staff Production Dashboard</x-slot>

  <section class="mx-auto w-full">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm w-full">
      <h2 class="text-lg font-semibold text-slate-900">Welcome back, {{ auth()->user()->name ?? 'User' }}</h2>
      <p class="mt-1 text-sm text-slate-600">Admin summary & quick stats here…</p>
    </div>
  </section>

  <section class="mx-auto w-full mt-4">
    <h1 class="text-2xl font-semibold mb-4">Inventory</h1>

    <div class="mb-4 flex gap-3">
      <!-- quick stock in modal trigger -->
      <button x-data @click="$dispatch('open-stock-in')" class="bg-green-600 text-white px-3 py-2 rounded">Stock In</button>
      <button x-data @click="$dispatch('open-stock-out')" class="bg-red-600 text-white px-3 py-2 rounded">Stock Out</button>
    </div>

    <div class="bg-white rounded shadow overflow-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Batch Code</th>
            <th class="p-3">Material</th>
            <th class="p-3">Received</th>
            <th class="p-3">Available (calc)</th>
            <th class="p-3">Status</th>
            <th class="p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($materials as $m)
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3">{{ $m->batch_code ?? '–' }}</td>
              <td class="p-3">{{ optional($m->material)->name ?? '–' }}</td>
              <td class="p-3">{{ $m->quantity }} {{ $m->unit }}</td>
              <td class="p-3">
                @php
                  $in = \App\Models\StockMovement::where('raw_batch_id',$m->id)->whereIn('type',['in','transfer_in'])->sum('quantity');
                  $out = \App\Models\StockMovement::where('raw_batch_id',$m->id)->whereIn('type',['out','transfer_out'])->sum('quantity');
                  $avail = $m->quantity + $in - $out;
                @endphp
                {{ $avail }} {{ $m->unit }}
              </td>
              <td class="p-3">{{ $m->status }}</td>
              <td class="p-3">
                <button class="text-indigo-600" @click="$dispatch('open-stock-in')">Stock In</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Stock In Modal --}}
    <div x-data="stockInModal()" x-on:open-stock-in.window="open()" x-cloak x-show="openNow" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black/40" @click="close()"></div>
      <div class="bg-white p-4 rounded shadow max-w-md w-full z-10">
        <h3 class="font-semibold mb-3">Stock In</h3>

        <form method="POST" action="{{ route('stock.in') }}">
          @csrf
          <div class="space-y-3">
            <div>
              <label class="block text-sm">Material</label>
              <select name="material_id" class="w-full border rounded px-3 py-2" required>
                @foreach($materials as $mat)
                  <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm">Suppliers</label>
              <select name="supplier_id" class="w-full border rounded px-3 py-2" required>
                @foreach($suppliers as $supp)
                  <option value="{{ $supp->id }}">{{ $supp->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm">Warehouse</label>
              <select name="warehouse_id" class="w-full border rounded px-3 py-2" required>
                @foreach($warehouses as $w)
                  <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->type }})</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm">Quantity</label>
              <input name="quantity" type="number" step="0.01" class="w-full border rounded px-3 py-2" required />
            </div>

            {{-- <div>
              <label class="block text-sm">Batch Code (optional)</label>
              <input name="batch_code" class="w-full border rounded px-3 py-2" />
            </div> --}}

            <div>
              <label class="block text-sm">Notes</label>
              <textarea name="notes" class="w-full border rounded px-3 py-2"></textarea>
            </div>
          </div>

          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="close()" class="px-3 py-2 border rounded">Cancel</button>
            <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded">Submit</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Stock Out Modal (similar) --}}
    <div x-data="stockOutModal()" x-on:open-stock-out.window="open()" x-cloak x-show="openNow" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black/40" @click="close()"></div>
      <div class="bg-white p-4 rounded shadow max-w-md w-full z-10">
        <h3 class="font-semibold mb-3">Stock Out</h3>

        <form method="POST" action="{{ route('stock.out') }}">
          @csrf
          <div class="space-y-3">
            <div>
              <label class="block text-sm">Raw Batch</label>
              <select name="raw_batch_id" class="w-full border rounded px-3 py-2" required>
                @foreach($raw_material_batches as $rb)
                  <option value="{{ $rb->id }}">{{ $rb->batch_code }} — {{ optional($rb->material)->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm">Quantity</label>
              <input name="quantity" type="number" step="0.01" class="w-full border rounded px-3 py-2" required />
            </div>

            <div>
              <label class="block text-sm">Warehouse</label>
              <select name="warehouse_id" class="w-full border rounded px-3 py-2" required>
                @foreach($warehouses as $w)
                  <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->type }})</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm">Note</label>
              <input name="note" class="w-full border rounded px-3 py-2" />
            </div>
          </div>

          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="close()" class="px-3 py-2 border rounded">Cancel</button>
            <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded">Submit</button>
          </div>
        </form>
      </div>
    </div>

    <script>
    function stockInModal(){
      return {
        openNow:false,
        open(){ this.openNow=true },
        close(){ this.openNow=false },
        submit(){ this.$el.querySelector('form').submit() }
      }
    }
    function stockOutModal(){
      return {
        openNow:false,
        open(){ this.openNow=true },
        close(){ this.openNow=false },
        submit(){ this.$el.querySelector('form').submit() }
      }
    }
    </script>
  </section>
</x-layout>