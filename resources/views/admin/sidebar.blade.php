<div class="sidebar sidebar-content">
    <div class="sidebar-profile p-3 mb-3">
        <div class="fw-bold">PracticalERP</div>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="{{ route('contacts.index') }}"><i class="fa fa-users"></i> Contacts</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('custom-fields.index') }}"><i class="fa fa-cogs"></i> Custom Fields</a></li>
        <li class="nav-item">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>