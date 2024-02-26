<script>
    function previewThumb(el, _target_el) {
        const target_el = document.getElementById(_target_el);
        const img_url = URL.createObjectURL(el.files[0]);
        target_el.children[0].setAttribute("src", img_url);
        target_el.style.display = "block"
    }

    function resetImage() {
        const input_el = document.getElementById('image');
        const target_el = document.getElementById("featured-thumb");

        if (target_el.style.display === "block") {
            const markerInput = document.createElement('input');
            markerInput.type = 'hidden';
            markerInput.name = 'image_removed';
            markerInput.value = 'true';
            input_el.form.appendChild(markerInput);
        }

        input_el.value = "";
        target_el.style.display = "none";
    }
</script>
