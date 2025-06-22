@php
$configData = Helper::appClasses();
$menuDatas = json_decode(session()->get('menus'));
if (Auth::check()) {
    $newMenuItem = (object) [
        'url' => route('dokumen.index'),
        'name' => 'Document',
        'icon' => 'ti ti-clipboard-list',
        'slug' => 'dokumen.index',
        'display' => 1,
        'name_id' => 'Dokumen',
        'name_en' => 'Document',
    ];
    array_splice($menuDatas->menu, 11, 0, [$newMenuItem]);
}
@endphp
<!-- Horizontal Menu -->
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal  menu bg-menu-theme flex-grow-0">
  <div class="{{$containerNav}} d-flex h-100">
    <ul class="menu-inner">
      @foreach ($menuDatas->menu as $menu)


      {{-- active menu method --}}
      @php
        $activeClass = null;
        $currentRouteName =  Route::currentRouteName();

        if ($currentRouteName === $menu->slug) {
            $activeClass = 'active';
        }
        elseif (isset($menu->submenu)) {
          if (gettype($menu->slug) === 'array') {
            foreach($menu->slug as $slug){
              if (str_contains($currentRouteName,$slug) and strpos($currentRouteName,$slug) === 0) {
                $activeClass = 'active';
              }
            }
          }
          else{
            if (str_contains($currentRouteName,$menu->slug) and strpos($currentRouteName,$menu->slug) === 0) {
              $activeClass = 'active';
            }
          }

        }
      @endphp


      {{-- main menu --}}
      @if ((isset($menu->url) and isset($menu->display) and $menu->display == 1) or isset($menu->submenu))

      <li class="menu-item {{$activeClass}}">
        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
          @isset($menu->icon)
          <i class="menu-icon {{ $menu->icon }}"></i>
          @endisset
          <div>
          @php
            $lang = \Lang::locale();
            $menu_name = $menu->{"name_$lang"};
          @endphp
          {{ isset($menu_name) ? __($menu_name) : '' }}</div>
        </a>

        {{-- submenu --}}
        @isset($menu->submenu)
          @include('layouts.sections.menu.submenu',['menu' => $menu->submenu])
        @endisset
      </li>
      @endif
      @endforeach
    </ul>
  </div>
</aside>
<!--/ Horizontal Menu -->
