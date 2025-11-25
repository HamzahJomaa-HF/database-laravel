<div class="d-flex flex-column h-100 py-3">
    <div class="navbar-brand text-uppercase fw-bold ms-3 mb-3" style="color: #fff;">FlowDash</div>
    
    <div class="nav-header">MENU</div>
    <ul class="nav flex-column mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="{{ url('/dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboards</a>
            <ul class="nav flex-column ps-3">
                <li class="nav-item"><a class="nav-link text-white-50 py-1" href="#">Default</a></li>
                <li class="nav-item"><a class="nav-link text-white-50 py-1" href="#">Analytics</a></li>
                <li class="nav-item"><a class="nav-link text-white-50 py-1" href="{{ route('users.index') }}">Staff (Users)</a></li>
                <li class="nav-item"><a class="nav-link text-white-50 py-1" href="#">E-commerce</a></li>
                <li class="nav-item"><a class="nav-link text-white-50 py-1" href="#">Quick Access</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-app-indicator me-2"></i> Apps</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-journal-text me-2"></i> Pages</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-grid me-2"></i> Layouts</a></li>
    </ul>

    <div class="nav-header mt-3">UI COMPONENTS</div>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-hand-index me-2"></i> Buttons</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bell me-2"></i> Alerts</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person me-2"></i> Avatars <span class="badge bg-primary ms-2">NEW</span></a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-window-split me-2"></i> Modals</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bar-chart me-2"></i> Charts <span class="badge-pro">PRO</span></a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-star me-2"></i> Icons</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-file-earmark-font me-2"></i> Forms</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-sliders me-2"></i> Range Sliders</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-calendar-check me-2"></i> Time & Date</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-table me-2"></i> Tables</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-activity me-2"></i> Loaders</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-mouse me-2"></i> Drag & Drop</a></li>
    </ul>
    
    <div class="mt-auto py-3 px-3">
        <a href="#" class="btn btn-warning w-100 btn-sm"><i class="bi bi-question-circle me-1"></i> Need Help?</a>
    </div>
</div>
