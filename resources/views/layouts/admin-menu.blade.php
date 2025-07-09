@if(isset($menuData))
    @foreach($menuData as $menu)
        @if($menu['text'] === 'Student Enrollment')
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Student Enrollment
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.batches.index') }}" class="nav-link {{ Request::is('admin/batches*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Batch Management</p>
                        </a>
                    </li>
                    <!-- Existing enrollment menu items -->
                    @foreach($menu['submenu'] as $submenu)
                        <li class="nav-item">
                            <a href="{{ $submenu['url'] }}" class="nav-link {{ Request::is($submenu['active']) ? 'active' : '' }}">
                                <i class="nav-icon {{ $submenu['icon'] }}"></i>
                                <p>{{ $submenu['text'] }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <!-- Other menu items -->
            <li class="nav-item">
                <a href="{{ $menu['url'] }}" class="nav-link {{ Request::is($menu['active']) ? 'active' : '' }}">
                    <i class="nav-icon {{ $menu['icon'] }}"></i>
                    <p>{{ $menu['text'] }}</p>
                </a>
            </li>
        @endif
    @endforeach
@endif
