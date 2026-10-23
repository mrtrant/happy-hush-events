<div
    class="bg-dark text-white vh-100 shadow"
    style="width:270px;">

    <div class="text-center py-4 border-bottom">

        <img
            src="{{ asset('assets/images/logo/logo-white.png') }}"
            class="img-fluid mb-3"
            style="max-width:120px;">

        <h5>

            Client Portal

        </h5>

    </div>

    <div class="list-group list-group-flush">

        <a
            href="{{ route('client.dashboard') }}"
            class="list-group-item list-group-item-action">

            Dashboard

        </a>

        <a
            href="#"
            class="list-group-item list-group-item-action">

            My Bookings

        </a>

        <a
            href="#"
            class="list-group-item list-group-item-action">

            Quotations

        </a>

        <a
            href="#"
            class="list-group-item list-group-item-action">

            Invoices

        </a>

        <a
            href="#"
            class="list-group-item list-group-item-action">

            Payments

        </a>

        <a
            href="#"
            class="list-group-item list-group-item-action">

            My Profile

        </a>

        <form
            method="POST"
            action="{{ route('logout') }}">

            @csrf

            <button
                class="list-group-item list-group-item-action w-100 text-start border-0">

                Logout

            </button>

        </form>

    </div>

</div>
