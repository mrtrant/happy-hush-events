<nav class="navbar navbar-expand-lg bg-white shadow-sm">

    <div class="container-fluid">

        <span class="navbar-brand fw-bold">

            Happy Hush Events Kenya

        </span>

        <div class="ms-auto">

            {{ auth()->user()->first_name }}
            {{ auth()->user()->last_name }}

        </div>

    </div>

</nav>
