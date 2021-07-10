<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            Winery<span>App</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item">
                <a href="{{route('home')}}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item nav-category">Master Data</li>
            <li class="nav-item">
                <a href="{{route('client.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Clients</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('employee.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">Employees</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('company.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="briefcase"></i>
                    <span class="link-title">Companies</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('seller.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="share-2"></i>
                    <span class="link-title">Sellers</span>
                </a>
            </li>

            <li class="nav-item nav-category">Transactions</li>
            <li class="nav-item">
                <a href="{{route('tx.regular.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="shopping-cart"></i>
                    <span class="link-title">Regular</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('tx.compliment.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="percent"></i>
                    <span class="link-title">Compliment</span>
                </a>
            </li>

            <li class="nav-item nav-category">Inventory</li>
            <li class="nav-item">
                <a href="{{route('unit.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="grid"></i>
                    <span class="link-title">Units</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('goods.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="package"></i>
                    <span class="link-title">Goods</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('client.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="plus-square"></i>
                    <span class="link-title">Incoming Goods</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('breakage.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="trash-2"></i>
                    <span class="link-title">Breakages</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('supplier.index')}}" class="nav-link">
                    <i class="link-icon" data-feather="truck"></i>
                    <span class="link-title">Suppliers</span>
                </a>
            </li>
        </ul>
    </div>
</nav>