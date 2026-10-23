<nav class="navbar navbar-expand-lg bg-white shadow-sm">

    <div class="container-fluid">

        <h4 class="mb-0">

            @yield('page-title')

        </h4>

        <div class="ms-auto">

            <span class="fw-semibold">

                {{ auth()->user()->first_name }}
                {{ auth()->user()->last_name }}

            </span>

        </div>

    </div>

</nav>
