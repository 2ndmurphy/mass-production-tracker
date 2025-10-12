@php
  use App\Helpers\MenuHelper;
  $role = auth()->user()->role->name ?? '';
  $dept = auth()->user()->department->name ?? '';
  $menus = MenuHelper::getMenuByRole($role, $dept);
@endphp


<!doctype html>
<html lang="en" class="h-full">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="h-full bg-slate-50 text-slate-800 antialiased">
    <div
      x-data="{
        // UI state
        open: false, // mobile sidebar open
        collapsed: JSON.parse(localStorage.getItem('sidebar:collapsed') ?? 'false'),
        width: +(localStorage.getItem('sidebar:width') ?? 280),
        min: 200,
        max: 360,
        rail: 72,
        // Actions
        toggle() {
          this.collapsed = !this.collapsed
          localStorage.setItem('sidebar:collapsed', JSON.stringify(this.collapsed))
          if (this.collapsed) {
            localStorage.setItem('sidebar:lastWidth', this.width)
            this.width = this.rail
          } else {
            const last = +(localStorage.getItem('sidebar:lastWidth') ?? this.min)
            this.width = Math.max(this.min, Math.min(this.max, last))
          }
          localStorage.setItem('sidebar:width', this.width)
        },
        startDrag(e) {
          if (this.collapsed) return
          const startX = e.clientX
          const startW = this.width
          const move = (ev) => {
            let w = startW + (ev.clientX - startX)
            w = Math.max(this.min, Math.min(this.max, w))
            this.width = w
            localStorage.setItem('sidebar:width', w)
            localStorage.setItem('sidebar:lastWidth', w)
          }
          const up = () => {
            window.removeEventListener('mousemove', move)
            window.removeEventListener('mouseup', up)
          }
          window.addEventListener('mousemove', move)
          window.addEventListener('mouseup', up)
        }
      }"
      class="h-full"
    >
      <div class="flex h-screen">
        <div
          class="fixed inset-0 z-30 bg-black/30 transition-opacity md:hidden"
          x-show="open"
          x-transition.opacity
          @click="open=false"
          aria-hidden="true"
        ></div>

        <aside
          class="z-40 flex h-full flex-col border-r border-slate-200 bg-white md:static"
          :class="{'fixed inset-y-0 left-0': open, 'md:translate-x-0 -translate-x-full': !open}"
          :style="`width:${width}px`"
          x-trap.noscroll.inert="open"
        >
          <div class="flex h-14 items-center gap-3 px-3">
            <button
              type="button"
              class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 md:hidden"
              @click="open=false"
              aria-label="Close sidebar"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>

            <a href="#" class="group flex items-center gap-3">
              <div class="grid h-8 w-8 place-items-center rounded-md bg-indigo-600 text-white">
                <span class="text-sm font-semibold">MP</span>
              </div>
              <div class="min-w-0" x-show="!collapsed">
                <p class="text-sm font-semibold leading-tight text-slate-900">Mass Production</p>
                <p class="text-xs text-slate-500 -mt-0.5">Tracker</p>
              </div>
            </a>
          </div>

          @auth
          <div class="mx-3 mt-2 rounded-md bg-slate-50 p-3" x-show="!collapsed">
            <p class="text-xs font-medium text-slate-500">Signed in as</p>
            <div class="mt-2 flex items-center gap-3">
              <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-200 text-slate-600">
                <span class="text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
              </div>
              <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->role->name ?? 'User' }}</p>
              </div>
            </div>
          </div>
          @endauth

          <nav class="mt-3 flex-1 overflow-y-auto px-2">
            <ul class="space-y-1">
                @foreach ($menus as $menu)
                    <li>
                        <a
                            href="{{ route($menu['route']) }}"
                            :title="collapsed ? '{{ $menu['name'] }}' : null"
                            @class([
                            'group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors',
                            'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' => !request()->routeIs($menu['route'].'*'),
                            'bg-indigo-50 text-indigo-700' => request()->routeIs($menu['route'].'*'),
                            ])
                        >
                            @switch($menu['icon'])
                              @case('squares-2x2')
                                  <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>
                                  </svg>
                                  @break
                              @case('arrow-down-circle')
                                  <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V8m0 8l4-4m-4 4l-4-4m8 8a8 8 0 1 0-8 0"/></svg>
                                  @break
                              @case('arrow-up-circle')
                                  <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m0-8l4 4m-4-4L8 12m8-8a8 8 0 1 1-8 0"/></svg>
                                  @break
                              @case('boxes')
                                  <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 3L3 7.5v9L9.5 21l6.5-4.5v-9L9.5 3zM9.5 12l6.5 4.5M9.5 12L3 7.5M9.5 12V21"/></svg>
                                  @break
                              @default
                                  <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                  <circle cx="12" cy="12" r="10"/>
                                  </svg>
                            @endswitch

                            <span class="truncate" x-show="!collapsed">{{ $menu['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="my-4 h-px bg-slate-200" x-show="!collapsed"></div>

            <div class="mt-auto pb-3">
              @auth
              {{-- <a
                href="{{ route('profile.show') }}"
                :title="collapsed ? 'Profile' : null"
                @class([
                  'group mb-1 flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors',
                  'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' => !request()->routeIs('profile*'),
                  'bg-indigo-50 text-indigo-700' => request()->routeIs('profile*'),
                ])
              >
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14a5 5 0 0 0-5 5h10a5 5 0 0 0-5-5Zm0-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/>
                </svg>
                <span class="truncate" x-show="!collapsed">Profile</span>
              </a> --}}

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                  type="submit"
                  :title="collapsed ? 'Logout' : null"
                  class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                >
                  <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7h-3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h3M10 12h10m0 0-3-3m3 3-3 3"/>
                  </svg>
                  <span class="truncate" x-show="!collapsed">Logout</span>
                </button>
              </form>
              @endauth
            </div>
          </nav>
        </aside>

        <div
          class="hidden w-1 cursor-col-resize select-none md:block"
          @mousedown="startDrag($event)"
        >
          <div class="mx-auto h-full w-0.5 bg-transparent hover:bg-indigo-200"></div>
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
          <header class="flex h-14 items-center justify-between border-b border-slate-200 bg-white px-3">
            <div class="flex items-center gap-2">
              <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 md:hidden"
                @click="open = true"
                aria-label="Open sidebar"
                aria-controls="sidebar"
                :aria-expanded="open"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              </button>

              <button
                type="button"
                class="hidden md:inline-flex h-9 items-center gap-2 rounded-md border border-slate-200 px-2 text-sm text-slate-700 hover:bg-slate-50"
                @click="toggle()"
                :aria-pressed="collapsed"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                title="Toggle sidebar width"
              >
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M10 4H4c-1.10457 0-2 .89543-2 2v12c0 1.1046.89543 2 2 2h6V4ZM7.79283 9.29289c.39053.39053.39053 1.02371 0 1.41421L6.5 11.9999l1.29283 1.2929c.39053.3905.39053 1.0237 0 1.4142-.39052.3905-1.02368.3905-1.41421 0l-1.99994-2c-.39052-.3905-.39052-1.0236 0-1.4142l1.99994-1.99991c.39053-.39052 1.02369-.39052 1.41421 0Z" clip-rule="evenodd"/>
                  <path d="M12 20h8c1.1046 0 2-.8954 2-2V6c0-1.10457-.8954-2-2-2h-8v16Z"/>
                </svg>
                <span class="hidden lg:inline" x-text="collapsed ? 'Expand' : 'Collapse'"></span>
              </button>

              <h1 class="ml-1 text-sm font-semibold text-slate-900 md:ml-2 md:text-base">
                {{ $header ?? 'Manager Dashboard' }}
              </h1>
            </div>

            <div class="flex items-center gap-3">
              <button class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100" aria-label="Notifications">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17H5l1.5-2V9a5.5 5.5 0 1 1 11 0v6L19 17h-4Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 21a2 2 0 0 0 4 0"/>
                </svg>
              </button>

              @auth
              <div class="flex items-center gap-2">
                <div class="hidden text-sm text-slate-700 sm:block">
                  {{ auth()->user()->name }}
                </div>
                <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-200 text-slate-600">
                  <span class="text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
              </div>
              @endauth
            </div>
          </header>

          <main class="min-h-0 flex-1 overflow-y-auto p-4">
            {{ $slot ?? '' }}
          </main>

          <footer class="border-t border-slate-200 bg-white py-3 text-center text-xs text-slate-500">
            © {{ now()->year }} Mass Production Tracker — built with <span class="text-indigo-600">♥</span>
          </footer>
        </div>
      </div>
    </div>
  </body>
</html>