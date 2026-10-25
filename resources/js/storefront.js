/* Keranjang katalog. Tampilan saja: harga dan total akhir
   dihitung ulang oleh server saat pesanan dibayar. */
(function () {
    var grid = document.getElementById('menuGrid');
    if (!grid) return;

    var KEY = 'sg_cart';
    var cards = Array.prototype.slice.call(grid.querySelectorAll('.menu-card'));
    var cart = {};              // { idProduk: jumlah }
    var activeCat = 'all';

    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function load() {
        try {
            var saved = JSON.parse(localStorage.getItem(KEY) || '{}');
            cards.forEach(function (card) {
                var qty = parseInt(saved[card.dataset.id], 10);
                var stock = parseInt(card.dataset.stock, 10);
                if (qty > 0) cart[card.dataset.id] = Math.min(qty, stock);
            });
        } catch (e) {
            cart = {};
        }
    }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(cart)); } catch (e) {}
    }

    function render() {
        var count = 0, total = 0;

        cards.forEach(function (card) {
            var qty = cart[card.dataset.id] || 0;
            var stock = parseInt(card.dataset.stock, 10);
            var price = parseFloat(card.dataset.price);

            card.classList.toggle('in-cart', qty > 0);
            card.querySelector('[data-act="add"]').hidden = qty > 0;
            card.querySelector('.stepper').hidden = qty === 0;
            card.querySelector('.qty').textContent = qty;
            card.querySelector('[data-act="inc"]').disabled = qty >= stock;

            count += qty;
            total += qty * price;
        });

        document.getElementById('cartCount').textContent = count;
        document.getElementById('cartTotal').textContent = rupiah(total);
        document.getElementById('cartBar').hidden = count === 0;
    }

    grid.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-act]');
        if (!btn) return;

        var card = btn.closest('.menu-card');
        var id = card.dataset.id;
        var stock = parseInt(card.dataset.stock, 10);
        var qty = cart[id] || 0;

        if (btn.dataset.act === 'add' || btn.dataset.act === 'inc') qty = Math.min(qty + 1, stock);
        if (btn.dataset.act === 'dec') qty = Math.max(qty - 1, 0);

        if (qty > 0) cart[id] = qty; else delete cart[id];
        save();
        render();
    });

    function applyFilter() {
        var term = document.getElementById('menuSearch').value.trim().toLowerCase();
        var shown = 0;

        cards.forEach(function (card) {
            var okCat = activeCat === 'all' || card.dataset.cat === activeCat;
            var okName = !term || card.dataset.name.indexOf(term) !== -1;
            card.hidden = !(okCat && okName);
            if (!card.hidden) shown++;
        });

        document.getElementById('menuEmpty').hidden = shown !== 0;
    }

    document.getElementById('chips').addEventListener('click', function (e) {
        var chip = e.target.closest('.chip');
        if (!chip) return;

        Array.prototype.forEach.call(this.querySelectorAll('.chip'), function (c) {
            c.classList.toggle('active', c === chip);
        });
        activeCat = chip.dataset.cat;
        applyFilter();
    });

    document.getElementById('menuSearch').addEventListener('input', applyFilter);

    // Saat "Lanjut pesan": isi input tersembunyi dari keranjang.
    document.getElementById('cartForm').addEventListener('submit', function (e) {
        var box = document.getElementById('cartInputs');
        var i = 0;
        box.innerHTML = '';

        Object.keys(cart).forEach(function (id) {
            [['product_id', id], ['quantity', cart[id]]].forEach(function (pair) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'items[' + i + '][' + pair[0] + ']';
                input.value = pair[1];
                box.appendChild(input);
            });
            i++;
        });

        if (i === 0) e.preventDefault();
    });

    load();
    render();
})();