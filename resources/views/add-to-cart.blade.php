<x-layout.bootstrap>
    <form action="/cart/add" method="POST">
        <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt=""
            width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Add to cart</h1>
        <label for="product" class="sr-only">Product ID</label>
        <input type="number" id="product" class="form-control" placeholder="Product" required autofocus>
        <label for="quantity" class="sr-only">Quantity</label>
        <input id="quantity" type="number" class="form-control" placeholder="Quantity" required>
        <div class="text-center">
            <button type="button" id="add" class="btn bg-gradient-info w-100 mt-4 mb-0">Add to cart</button>

            <button class="btn bg-gradient-primary w-100 mt-4 mb-0 d-none" id="btn-loading" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span id="load-text"> Please wait...</span>
            </button>
        </div>
        <p class="mt-5 mb-3 text-muted">&copy; 2017-2018</p>
    </form>
    <x-slot:scripts>
        <script src="/js/cart/add.js"></script>
        </x-slot>
</x-layout.bootstrap>
