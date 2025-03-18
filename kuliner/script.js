document.addEventListener("DOMContentLoaded", generateData);

// Data makanan
const food = [
    { id: 1, name: "Asinan Bogor", harga: 15000, stok: 10, image: "asinan_bogor.png" },
    { id: 2, name: "Bapatong", harga: 20000, stok: 8, image: "bapatong.png" },
    { id: 3, name: "Doclang", harga: 12000, stok: 15, image: "doclang.png" },
    { id: 4, name: "Laksa Bogor", harga: 18000, stok: 12, image: "laksa_bogor.png" },
    { id: 5, name: "Toge Goreng", harga: 14000, stok: 10, image: "toge_goreng.png" }
];

function generateData() {
    const foodList = document.getElementById("foodList");
    if (!foodList) return;
    foodList.innerHTML = ""; // mekosongkan list sebelum diisi ulang
    food.forEach((item, index) => {
        let divCard = document.createElement("div");
        divCard.classList.add("card");

        let img = document.createElement("img");
        let imagePath = `./kuliner/assets/images/${item.image}`;

        console.log(`Loading image for ${item.name}: ${imagePath}`);

        img.src = imagePath;
        img.onerror = function () {
            console.error(`Image not found: ${imagePath}, using default image.`);
            img.src = "./kuliner/assets/images/default.png";
        };
        divCard.appendChild(img);

        let title = document.createElement("p");
        title.innerText = item.name;
        divCard.appendChild(title);

        let divAction = document.createElement("div");
        divAction.classList.add("action");

        let spanData = document.createElement("span");
        spanData.innerText = `Rp ${toRupiah(item.harga)},00 | Stok: ${item.stok}`;
        divAction.appendChild(spanData);

        let jumlahInput = document.createElement("input");
        jumlahInput.type = "number";
        jumlahInput.min = 1;
        jumlahInput.max = item.stok;
        jumlahInput.value = 1;
        jumlahInput.oninput = function () {
            validateStock(jumlahInput, item.stok);
        };
        divAction.appendChild(jumlahInput);

        let btnAdd = document.createElement("button");
        btnAdd.innerHTML = '<i class="fas fa-cart-plus"></i> Pesan';
        btnAdd.onclick = () => addToCart(index, jumlahInput.value);
        divAction.appendChild(btnAdd);

        divCard.appendChild(divAction);
        foodList.appendChild(divCard);
    });
}

// fungsinya untuk menambahkan ke keranjang
function addToCart(index, qty) {
    let selectedFood = food[index];
    let jumlahPesanan = parseInt(qty);

    if (selectedFood.stok < jumlahPesanan) {
        alert(`${selectedFood.name} hanya tersisa ${selectedFood.stok} porsi.`);
        return;
    }

    let formData = new FormData();
    formData.append('id', selectedFood.id);
    formData.append('name', selectedFood.name);
    formData.append('price', selectedFood.harga);
    formData.append('image', selectedFood.image);
    formData.append('qty', jumlahPesanan);

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Berhasil ditambahkan ke keranjang!");
            food[index].stok -= jumlahPesanan; 
            generateData(); 
            updateCart();
        } else {
            alert("Gagal menambahkan ke keranjang.");
        }
    })
    .catch(error => console.error('Error:', error));
}

// Fungsi untuk mengubah format harga ke Rupiah
function toRupiah(harga) {
    return harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Fungsi untuk memperbarui tampilan keranjang
function updateCart() {
    // Implementasikan kode untuk memperbarui jumlah keranjang di UI sesuai data keranjang
}
