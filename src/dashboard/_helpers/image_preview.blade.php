<script>
    function previewThumb(inputId, previewId) {
        const inputEl = document.getElementById(inputId);
        const previewEl = document.getElementById(previewId);
        const img_url = URL.createObjectURL(inputEl.files[0]);
        previewEl.children[0].setAttribute("src", img_url);
        previewEl.style.display = "block";
    }

    function resetImage(inputId, previewId) { //for, id
        const inputEl = document.getElementById(inputId);
        const previewEl = document.getElementById(previewId);

        if (previewEl.style.display === "block") {
            const markerInput = document.createElement('input');
            markerInput.type = 'hidden';
            markerInput.name = inputId + '_removed';
            markerInput.value = 'true';
            inputEl.form.appendChild(markerInput);
        }

        inputEl.value = "";
        previewEl.style.display = "none";
    }

</script>
