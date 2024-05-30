document.addEventListener("DOMContentLoaded", function () {
    // Add event listeners to "Add to Cart" buttons
    document.querySelectorAll(".n_btn").forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const bookCard = this.closest(".category_card");
            const title = bookCard.querySelector(".name").innerText;
            const author = bookCard.querySelector(".writer").innerText;
            const price = parseFloat(
                bookCard
                    .querySelector(".book_price")
                    .innerText.replace("Rs.", "")
                    .replace(",", "")
            );
            const imageUrl = bookCard.querySelector("img").src;

            addToCart(title, author, price, imageUrl);
        });
    });
});

function addToCart(title, author, price, imageUrl) {
    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const existingItemIndex = cartItems.findIndex(
        (item) => item.title === title
    );

    if (existingItemIndex !== -1) {
        // If item already in cart, increase quantity
        cartItems[existingItemIndex].quantity += 1;
    } else {
        // Add new item to cart
        const item = {
            title: title,
            author: author,
            price: price,
            imageUrl: imageUrl,
            quantity: 1,
        };
        cartItems.push(item);
    }

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    // alert("Item added to cart!");
}

// Function to enhance all "Add To Cart" buttons
function enhanceAddToCartButtons() {
    const addToCartButtons = document.querySelectorAll(".n_btn");
    addToCartButtons.forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault(); // Prevent the default behavior of the link
            Swal.fire({
                title: "Success!",
                text: "The item has been added to your cart.",
                icon: "success",
                confirmButtonText: "OK",
            });
        });
    });
}

// Initialize all enhancements when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
    enhanceAddToCartButtons();
});
